<?php

/**
 * ==============================
 *  CUSTOM FUNCTION: Mark as Viewed
 * ==============================
 */

function soundlush_mark_as_viewed()
{
  if( is_user_logged_in() && is_singular() )
  {
    $user     = wp_get_current_user();
    $post_id  = get_the_id();

    // TODO maybe use custom field istead of parent?
    //$metakey    = '_soundlush_userview_' . $post->post_parent;
    $metakey  = '_soundlush_userview_' . '247';

    $usermeta = get_user_meta( $user->ID, $metakey, false );

    if( isset( $usermeta[0] ) ){
      $meta = $usermeta[0];
      $meta[] = $post_id;
    } else {
      $meta[0] = $post_id;
    }
    $meta = array_unique($meta);

    update_user_meta($user->ID, $metakey, $meta);
  }
}



/**
 * ==============================
 *  SHORTCODE: Mark as Complete
 * ==============================
 */

// TODO NO SHORTCODE, USE FILTER HOOK AT THE BOTTOM OF LESSON POST, ALSO GIVE OPTION TO NOT SHOW?? ON EXERCISES AND QUIZZES, SUBMISSION == COMPLETE

add_shortcode( 'markcomplete', 'soundlush_create_markcomplete_btn' );

function soundlush_create_markcomplete_btn( $atts, $content = null ){

  // Get the attributes
  $atts = shortcode_atts(
    array(),
    $atts,
    'markcomplete'
  );

  if( is_user_logged_in() && is_singular() ) // and user has product and user have not upload it yet
  {
    $html = '<button type="submit" id="soundlush_markcomplete_btn" class="btn btn-accent" data-id="'.get_the_id().'" data-user="'.get_current_user_id().'" >Mark as Complete</button>';

    return $html;

  } else {
    return;
  }
}



/**
 * ==============================
 *  AJAX CALLBACK: Mark as Complete
 * ==============================
 */

add_action( 'wp_ajax_nopriv_mark_as_completed', 'mark_as_completed' );
add_action( 'wp_ajax_mark_as_completed', 'mark_as_completed' );

function mark_as_completed()
{
  // check nonce before doing anything
  check_ajax_referer( 'frontend_nonce', 'nonce' );

    $user_id    = wp_strip_all_tags( $_POST['user'] );
    $post_id    = wp_strip_all_tags( $_POST['post'] );

    // TODO maybe use custom field istead of parent?
    //$metakey    = '_soundlush_usercomplete_' . $post->post_parent;
    $metakey    = '_soundlush_usercomplete_' . '247';

    $usermeta = get_user_meta( $user_id, $metakey, false );

    if( isset( $usermeta[0] ) ){
      $meta = $usermeta[0];
      $meta[] = $post_id;
    } else {
      $meta[0] = $post_id;
    }
    $meta = array_unique($meta);

    update_user_meta($user_id, $metakey, $meta);

  die;
}
