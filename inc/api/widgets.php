<?php
/**
 * Soundlush Custom Widgets
 * All classes & custom functions for creation of theme widgets will be added here
 * @link https://codex.wordpress.org/Widgets_API
 *
 * @package com.soundlush.theme.v1
 */

/**
 * === === === === === === === ===
 * Soundlush Popular Posts Widget
 * Retrieve the x more popular posts (based on view count)
 * === === === === === === === ===
 */

// count/update post views and store info as post metadata
function soundlush_save_post_views( $postID ){

  $metaKey = 'soundlush_post_views';
  $views = get_post_meta( $postID, $metaKey, true );

  $count = ( empty( $views ) ? 0 : $views );
  $count++;

  update_post_meta( $postID, $metaKey, $count );
}
remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 );


// Popular Posts Widget
class SoundlushPopularPostsWidget extends WP_Widget
{
   // setup the widget name, description, etc.
  public function __construct(){
      $widget_ops = array(
        'classname' => 'soundlush-popular-posts',
        'description' => __('Custom Soundlush Popular Posts Widget', 'soundlush' ),
      );

      parent::__construct('soundlush_popular', 'Soundlush Popular Posts', $widget_ops);
  }

  // display at the backend
  public function form( $instance ){

    // store widget title
    $title = ( !empty( $instance[ 'title' ] ) ? $instance[ 'title' ] : 'Popular Posts' );
    // store number of posts to retrieve
    $total = ( !empty( $instance[ 'total' ] ) ? absint( $instance[ 'total' ] ) : 4 );

    // html output for the backend
    $output = '<p>';
    $output .= '<label for = "' . esc_attr( $this-> get_field_ID( 'title' ) ) . '">Title:</label>';
    $output .= '<input type="text" class="widefat" id="' . esc_attr( $this-> get_field_ID( 'title' ) ) . '" name = "' . esc_attr( $this-> get_field_name( 'title' ) ) . '" value="' . esc_attr( $title ) . '" >';
    $output .= '</p>';

    $output .= '<p>';
    $output .= '<label for = "' . esc_attr( $this-> get_field_ID( 'total' ) ) . '">Number of Posts:</label>';
    $output .= '<input type="number" class="widefat" id="' . esc_attr( $this-> get_field_ID( 'total' ) ) . '" name = "' . esc_attr( $this-> get_field_name( 'total' ) ) . '" value="' . esc_attr( $total ) . '" >';
    $output .= '</p>';

    echo $output;
  }

  // update widgets
  public function update( $new_instance, $old_instance ){

    $instance = array();
    $instance[ 'title' ] = ( !empty( $new_instance[ 'title' ] ) ? strip_tags( $new_instance[ 'title' ] ) : '' );
    $instance[ 'total' ] = ( !empty( $new_instance[ 'total' ] ) ? absint(strip_tags( $new_instance[ 'total' ] )) : 0 );
    return $instance;

  }

  // display at the frontend
  public function widget( $args, $instance ){

    // define query params
    $total = absint( $instance[ 'total' ] );
    $posts_args = array(
      'post_type'       => 'post',
      'posts_per_page'  => $total,
      'meta_key'        => 'soundlush_post_views',
      'orderby'         => 'meta_value_num',
      'order'           => 'DESC'
    );

    // query posts
    $posts_query = new WP_Query( $posts_args );

    // html output for the front end
    echo $args['before_widget'];

    if( !empty( $instance[ 'title' ] ) ):
      echo $args[ 'before_title' ] . apply_filters( 'widget_title', $instance[ 'title' ] ) . $args[ 'after_title'];
    endif;

    if( $posts_query->have_posts() ):
      echo '<ul>';
      while ( $posts_query->have_posts() ) : $posts_query->the_post();
        echo '<li><a href="' . get_the_permalink()  . '">' . get_the_title() . '</a></li>';
      endwhile;
      echo '</ul>';
    endif;
    echo $args['after_widget'];
  }
}

add_action('widgets_init', function(){
  register_widget('SoundlushPopularPostsWidget');
});



/*
 * === === === === === === ===
 * Soundlush Social Media Widget
 * === === === === === === ===
 */

class SoundlushSocialWidget extends WP_Widget
{
  // setup the widget name, description, etc.
  public function __construct(){
      $widget_ops = array(
        'classname' => 'soundlush-social-widget',
        'description' => 'Custom Soundlush Social Media Widget',
      );

      parent::__construct('soundlush_social', 'Soundlush Social Media', $widget_ops);
  }

  // display at the backend
  public function form( $instance ){
    echo '<p>No options for this widget</p>';
  }

  // display at the frontend
  public function widget( $args, $instance ){
    echo $args['before_widget'];
    echo $args['after_widget'];
  }

}

add_action('widgets_init', function(){
  register_widget('SoundlushSocialWidget');
});


/*
 * === === === === === === ===
 * Soundlush Course Table of Content Widget
 * === === === === === === ===
 */

 class SoundlushCourseIndexWidget extends WP_Widget
 {
   // setup the widget name, description, etc.
   public function __construct(){
       $widget_ops = array(
         'classname' => 'soundlush-course-index-widget',
         'description' => 'Custom Soundlush Course Index Widget',
       );

       parent::__construct('soundlush_course_index', 'Soundlush Course Index', $widget_ops);
   }

   // display at the backend
   public function form( $instance ){
     echo '<p>No options for this widget</p>';
   }

   // display at the frontend
   public function widget( $args, $instance ){

    // List all items in the taxonomy
    // TODO maybe exclude parent here????
    $tax_args = array(
      'taxonomy'  => 'module', //change this to any taxonomy
      'orderby'   => 'name',
      'order'     => 'ASC',
    );
    $taxonomies = get_categories( $tax_args );


    //$top_parent = get_terms( array(
    //  'taxonomy' => 'module',
    //  'parent' => 0,
    //));
    //$the_parent = $top_parent['term_id'];


    // html output for the front end
    echo $args['before_widget'];

    foreach ($taxonomies as $taxonomy):
      echo '<h4>' . $taxonomy->name . '</h4>';

      // TODO if top parent, skip to avoid duplicates
      if( $taxonomy->cat_ID == 34):
        continue;
      endif;

      // WP_Query arguments
      $lesson_args = array (
        'post_type'       => 'lesson',
        'post_status'     => 'publish',
        'posts_per_page'  => '-1',
        'order'           => 'ASC',
        'orderby'         => 'menu_order',
        'tax_query'       => array(
      		array(
      			'taxonomy' => 'module',
      			'field'    => 'term_id',
      			'terms'    => $taxonomy->cat_ID,
      		),
      	),
      );

      // The Query
      $query = new WP_Query( $lesson_args );

      // The Loop
      if ( $query->have_posts() ):
        echo '<ul>';
        while ( $query->have_posts() ):
          $query->the_post();
          $lesson_name = get_the_title();
          $lesson_link = get_the_permalink();
          echo '<li><a href="' . $lesson_link . '">'. $lesson_name . '</a></li>';
        endwhile;
        echo '</ul>';
      endif;
    endforeach;
    wp_reset_postdata();
    echo $args['after_widget'];
   }
 }

 add_action('widgets_init', function(){
   register_widget('SoundlushCourseIndexWidget');
 });



/*
 * === === === === === === ===
 * Soundlush Address Widget
 * === === === === === === ===
 */

 /*
  * === === === === === === ===
  * Soundlush About Widget
  * === === === === === === ===
  */
