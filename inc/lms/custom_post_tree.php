<?php

/**
 * Soundlush Custom Post Types Tree Class
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 * @package soundlush
 */

if( !class_exists( 'SoundlushCustomPostTree' ) )
{

  class SoundlushCustomPostTree
  {

    public $parent;
    public $child;
    public $post_type_parent;
    public $post_type_child;


    public function __construct( $parent, $child )
    {
      // Set variables
      $this->post_type_parent   = SoundlushHelpers::uglify( $parent );
      $this->post_type_child    = SoundlushHelpers::uglify( $child );

      $this->create_tree();
    }



    public function create_tree()
    {
      global $post;

      if( $post->post_type == $this->post_type_parent ) {

        $parent_id = $post->ID;

        $children = get_children( array(
        	'post_parent' => $parent_id,
        	'post_type'   => $this->post_type_child,
        	'numberposts' => 1,
        	'post_status' => 'publish'
          )
        );

        $post_id = current($children)->ID;

      } elseif( $post->post_type == $this->post_type_child ){

        $post_id = $post->ID;

      } else {

        return;

      }

      // Get taxonomy by post ID
      $taxonomy = get_post_taxonomies( $post_id );
      $taxonomy_name = $taxonomy[0];

      // Get direct term associated with the post
      $term_obj = get_the_terms( $post_id, $taxonomy_name );
      $term_id  = $term_obj[0]->term_id;

      // Get term ascestors from direct term
      $term_ancestors = get_ancestors($term_id, $taxonomy_name);


      // Get all top level terms
      $top_level_terms = get_terms( $taxonomy_name,
        array(
          'parent'     => 0,
          'fields'     => 'ids',
          'hide_empty' => false,
        )
      );


      // Compare arrays and retrieve top level term ancestor for post
      $top_ancestor     = array_intersect( $term_ancestors, $top_level_terms );
      $top_ancestor_id  = array_shift( $top_ancestor );


      if( is_null($top_ancestor_id ) ) $top_ancestor_id = $term_id;


      // Get child terms from top ancestor
      $terms = get_terms($taxonomy_name,
        array(
          'orderby'   => 'parent',
          'order'     => 'ASC',
          'child_of'  => $top_ancestor_id,
        )
      );

      // Call recursive function to generate indented list (Modules and Lessons)
      print_r ( $this->format_tree( $terms, $top_ancestor_id ) );

    }



    /* Format Taxonomy Tree - Recursive Function */
    public function format_tree( $terms, $level = 0 )
    {

      $output = '';

      foreach( $terms as $term ):

        if( $term->parent == $level ):
          $output .= '<li>';
          $output .= $term->name;
          $output .= $this->post_tree( $term->term_id );
          $output .= $this->format_tree( $terms, $term->term_id );
          $output .= '</li>';
        endif;

      endforeach;

      return ( $output == '' ? '' : $output = '<ul>' . $output . '</ul>');

    }



    /* Format Post Tree - Complementary */
    public function post_tree( $term_id )
    {

      global $post;

      if( $post->post_type == $this->post_type_parent ) {

        $parent_id = $post->ID;

      }elseif( $post->post_type == $this->post_type_child ){

        $parent_id = get_post_ancestors( $post->ID );

      }else{

        return;
      }


      // Get taxonomy by post ID
      $taxonomy = get_post_taxonomies( $post->ID );
      $taxonomy_name = $taxonomy[0];


      // WP_Query arguments: Check if taxonomy term has children that are children of current parent
      $args = array(
        'post_type'       => $this->post_type_child,
        'post_parent'     => $parent_id,
        'fields'          => 'ids',
        'post_status'     => 'publish',
        'posts_per_page'  => '-1',
        'order'           => 'ASC',
        'orderby'         => 'menu_order',
        'tax_query'       => array(
          array(
            'taxonomy' => $taxonomy_name,
            'field'    => 'term_id',
            'terms'    => $term_id,
          ),
        ),
      );

      // The Query
      $query = new WP_Query( $args );
      $output = '';

      // The Loop
      if ( $query->have_posts() ):

        while ( $query->have_posts() ):

          $query->the_post();

          $post_id   = get_the_ID();
          $post_name = get_the_title();
          $post_link = get_the_permalink();

          // If term has children terms, assign child posrt to the lowest level term
          $post_term = get_the_terms( $post_id, $taxonomy_name );

          if( $term_id == $post_term[0]->term_id ) {
            $output .= '<li><a href="' . $post_link . '">'. $post_name . '</a></li>';
          }

        endwhile;

      endif;

      wp_reset_postdata();

      return ( $output == '' ? '' : $output = '<ul>' . $output . '</ul>');
    }

  }
}
