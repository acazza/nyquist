<?php







if( ! class_exists( SoundlushCourses ) )
{

    class SoundlushCourses
    {

      public static function mark_as_viewed( $posttype )
      {
        global $post;

        if( is_user_logged_in() && post_type_exists($posttype) && is_singular( $posttype ) )
        {
          $user       = wp_get_current_user();

          // TODO maybe use custom field istead of parent?
          $metakey    = '_soundlush_userview_' . $post->post_parent;

          $usermeta   = get_user_meta( $user->ID, $metakey, false );

          $usermeta[] = $post->ID;
          $usermeta   = array_unique($usermeta);

          update_user_meta($user->ID, $metakey, $usermeta);
        }
      }


      // AJAX CALL
      public static function mark_as_completed( $posttype )
      {
        global $post;

        if( is_user_logged_in() && post_type_exists($posttype)  && is_singular( $posttype ) )
        {
          $user       = wp_get_current_user();

          // TODO maybe use custom field istead of parent?
          $metakey    = '_soundlush_usercomplete_' . $post->post_parent;

          $usermeta   = get_user_meta( $user->ID, $metakey, false );

          $usermeta[] = $post->ID;
          $usermeta   = array_unique($usermeta);

          update_user_meta($user->ID, $metakey, $usermeta);
        }
      }

    }
}
