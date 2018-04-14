<?php

function get_tree_structure()
{

  $taxonomy     = 'volume';
  $metakey      = '_meta_mycourse';

  $current_post = get_the_id();
  $metavalue    = wp_get_post_parent_id( $current_post );


  $included_terms = get_terms(
      array(
          'taxonomy'   => $taxonomy,
          'hide_empty' => '0',
          'fields'     => 'ids',
          'meta_query' => array(
              array(
                'key'     => $metakey,
                'value'   => 247, //$metavalue, // TODO get this dinamicaly
                'compare' => 'LIKE',
          ))
      )
  );

  $args = array(
    'child_of'            => 0,
    'current_category'    => 0,
    'depth'               => 0,
    'echo'                => 1,
    'include'             => $included_terms,
    'exclude'             => '',
    'exclude_tree'        => '',
    'feed'                => '',
    'feed_image'          => '',
    'feed_type'           => '',
    'hide_empty'          => 1,
    'hide_title_if_empty' => true,// false
    'hierarchical'        => true,
    'order'               => 'ASC',
    'orderby'             => 'name',
    'separator'           => '<br />',
    'show_count'          => 0,
    'show_option_all'     => '',
    'show_option_none'    => '', //__( 'No categories' ),
    'style'               => 'list',
    'taxonomy'            => $taxonomy,
    'title_li'            => '', //__( 'Categories' ),
    'use_desc_for_title'  => 1,
    'walker'              => new SoundlushTreeStructure
  );

  wp_list_categories($args);

}


if( ! class_exists('SoundlushTreeStructure') )
{
    class SoundlushTreeStructure extends Walker
    {

      public $tree_type = 'category';
      public $db_fields = array( 'parent'=>'parent', 'id'=>'term_id' );

      // TODO get this dinamicaly
      public $taxonomy  = 'volume';



      function start_lvl( &$output, $depth = 0, $args = array() )
      {
        if( 'list' != $args['style'] ) return;

        $indent  = str_repeat( "\t", $depth );
        $output .= "{$indent}<ul class='tree-children children'>\n";
      }



      function end_lvl( &$output, $depth = 0, $args = array() )
      {
        if( 'list' != $args['style'] ) return;

        $indent  = str_repeat( "\t", $depth );
        $output .= "{$indent}</ul>\n";
      }



      function start_el( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 )
      {
        $cat_name = apply_filters(
      			'list_cats',
      			esc_attr( $category->name ),
      			$category
    		);

        // Don't generate an element if the category name is empty.
		    if ( ! $cat_name ) return;
    		$termlink = $cat_name;

        // Custom
        $post_type = get_post_type();

        $posts = get_posts( array(
          'post_type' => $post_type,
          'numberposts' => -1,
          'tax_query' => array(
            array(
              'taxonomy' => $this->taxonomy,
              'field' => 'id',
              'terms' => $category->term_id,
              'include_children' => false
            )
          )
        ));
        $postlink = '';
        $_current_post = get_the_id();


        if($posts)
        {
          $postlink = '<ul>';
          foreach( $posts as $post )
          {
            $css_classes_post = '';
            if( $_current_post == $post->ID ){
              $css_classes_post = 'current_post';
            }
            $postlink .= '<a class="tree-post-link" href="'. esc_url( get_the_permalink( $post->ID ) ) .'">';
            $postlink .= '<li class="tree-item tree-post-item tree-post-item-'.$post->ID.' depth-'. ($depth + 1).' '.$css_classes_post.'">';
            $postlink .= get_the_title($post->ID);
            $postlink .= '<i class="fas fa-check tree-icon-item"></i>';
            $postlink .= '<i class="fas fa-eye tree-icon-item"></i>';

            $postlink .= '</li></a>';
          }
          $postlink .= '</ul>';
        }


        if ( 'list' == $args['style'] )
        {
    			$output     .= "\t<li";
    			$css_classes = array(
            'tree-item',
    				'tree-cat-item',
    				'tree-cat-item-' . $category->term_id,
            'depth-'.$depth,
    			);

    			if ( ! empty( $args['current_category'] ) )
          {
    				// 'current_category' can be an array, so we use `get_terms()`.
    				$_current_terms = get_terms(
    					$category->taxonomy, array(
    						'include'    => $args['current_category'],
    						'hide_empty' => false,
    					)
    				);

            // run through all current terms
    				foreach ( $_current_terms as $_current_term )
            {
              // add classes to the current_term and its direct parent
              if ( $category->term_id == $_current_term->term_id )
              {
    						$css_classes[] = 'current-cat';
    					} elseif ( $category->term_id == $_current_term->parent ) {
    						$css_classes[] = 'current-cat-parent';
    					}

              while ( $_current_term->parent )
              {
    						if ( $category->term_id == $_current_term->parent ) {
    							$css_classes[] = 'current-cat-ancestor';
    							break;
    						}
    						$_current_term = get_term( $_current_term->parent, $category->taxonomy );
    					}
    				}
    			}

    			$css_classes = implode( ' ', apply_filters( 'category_css_class', $css_classes, $category, $depth, $args ) );
    			$output .= ' class="' . $css_classes . '"';
    			$output .= ">$termlink\n";
          $output .= $postlink;
    		} elseif ( isset( $args['separator'] ) ) {
    			$output .= "\t$termlink" . $args['separator'] . "\n";
    		} else {
    			$output .= "\t$termlink<br />\n";
    		}
      }



      function end_el( &$output, $category, $depth = 0, $args = array() )
      {
        if( 'list' != $args['style'] ) return;
        $output .= "</li>\n";
      }

    }
}
