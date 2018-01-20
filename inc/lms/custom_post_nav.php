<?php

/**
 * Soundlush Custom Post Types Navigation Class
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 * @package soundlush
 */

if( !class_exists( 'SoundlushCustomPostNav' ) )
{

  class SoundlushCustomPostNav
  {


    public $post_type_name;

    /**
    * Re-order lessons by menu_order and filters per parent
    * for previous/next post navigation
    */

    public function get_custom_post_nav($post_type) {

      $this->post_type_name = SoundlushHelpers::uglify( $post_type );

      if ( is_singular( $this->post_type_name ) ) {

      	global $post, $wpdb;

      	add_filter( 'get_next_post_sort', array( &$this, 'filter_next_post_sort' ) );
      	add_filter( 'get_next_post_where', array( &$this, 'filter_next_post_where' ) );

      	add_filter( 'get_previous_post_sort',  array( &$this, 'filter_previous_post_sort' ) );
      	add_filter( 'get_previous_post_where',  array( &$this, 'filter_previous_post_where' ) );


      	$previous_post = get_previous_post();
      	$next_post = get_next_post();


        // Html Output

      	echo '<div class="adjacent-entry-pagination pagination">';

      	if ( $previous_post ) {
      		echo '<div class="pagination-previous"><a href="' .get_permalink( $previous_post->ID ). '">&laquo; ' .$previous_post->post_title. '</a></div>';
      	} else {
      		echo '<div class="pagination-previous"><a href="' .get_post_type_archive_link( 'book-post' ). '">---</a></div>';
      	}

      	if ( $next_post ) {
      		echo '<div class="pagination-next"><a href="' .get_permalink( $next_post->ID ). '">' .$next_post->post_title. ' &raquo;</a></div>';
      	} else {
      		echo '<div class="pagination-next"><a href="' .get_post_type_archive_link( 'book-post' ). '">---</a></div>';
      	}

      	echo '</div>';

      }
    }

    public function filter_next_post_sort( $sort )
    {
      $sort = 'ORDER BY p.menu_order ASC LIMIT 1';

      return $sort;
    }


    public function filter_next_post_where( $where )
    {
      global $post, $wpdb;

      $where = $wpdb->prepare( "WHERE p.menu_order > '%s' AND p.post_type = '" . $this->post_type_name . "' AND p.post_status = 'publish' AND p.post_parent = '%s'",$post->menu_order, $post->post_parent);

      return $where;
    }


    public function filter_previous_post_sort( $sort )
    {
      $sort = 'ORDER BY p.menu_order DESC LIMIT 1';

      return $sort;
    }


    public function filter_previous_post_where($where)
    {
      global $post, $wpdb;

      $where = $wpdb->prepare( "WHERE p.menu_order < '%s' AND p.post_type = '" . $this->post_type_name . "' AND p.post_status = 'publish'AND p.post_parent = '%s'",$post->menu_order, $post->post_parent);

      return $where;
    }

  }
}
