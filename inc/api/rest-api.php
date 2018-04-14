<?php
/**
 * Grab $data['qty'] of random posts of type 'question' by term $data['id']
 *
 * @param array $data Options for the function.
 * @return string|null  Posts or null if none.
 */

function rest_get_the_questions( $data )
{
    $posts = get_posts( array(
      'post_type'      => 'question',
      'orderby'        => 'rand',
      'posts_per_page' => $data['qty'],
      'post_status'    => 'publish',
      'tax_query' => array(
          array(
            'taxonomy' => 'quiz_pool',
            'field' => 'term_id',
            'terms' => $data['id'],
          )
      )
    ));

    if ( empty( $posts ) ) {
      return null;
    } else {
      // include custom fields in json
      foreach ($posts as $key => $post) {
          $posts[$key]->level   = get_post_meta( $post->ID, 'soundlush_question_level', true );
          $posts[$key]->options = get_post_meta( $post->ID, '_repeater', true );
      }
    }

    return $posts;
}


add_action( 'rest_api_init', function(){

    register_rest_route( 'soundlush/v2', '/question/(?P<id>\d+)/(?P<qty>\d+)', array(
        'methods'  => 'GET',
        'callback' => 'rest_get_the_questions',
    ));

});
