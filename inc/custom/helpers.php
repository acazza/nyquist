<?php

/**
 * Soundlush Custom Post Helpers
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 * @package soundlush
 */

if( ! class_exists( 'SoundlushHelpers') )
{

  class SoundlushHelpers
  {

    /**
     * Flush rewrite rules for custom post types on theme (de)activation.
     */

    public static function activate()
    {
      add_action( 'after_switch_theme', 'flush_rewrite_rules' );
    }



    /**
     * Set and unifies the use of textdomain
     */

    public static function get_textdomain() {
      return 'soundlush';
    }



    /**
     * Convert to first caps and replace undercores with spaces
     * @example $name = self::beautify( $string );
     */

    public static function beautify( $string )
    {
        //return ucwords( str_replace( '_', ' ', $string ) );
        return ucwords( strtolower( str_replace( '-', ' ', str_replace( '_', ' ', $string ) ) ) );
    }



    /**
     * Convert to small caps and replace spaces with undercores
     * @example $name = self::uglify( $string );
     */

    public static function uglify( $string )
    {
        //return strtolower( str_replace( ' ', '_', $string ) );
        return strtolower( str_replace( ' ', '_', str_replace( '-', '_', $string ) ) );
    }



    /**
     * Generate plural form
     * @example $plural = self::pluralize( $string )
     */

    public static function pluralize( $string )
    {
        $last = $string[strlen( $string ) - 1];

        switch( $last ){

          case 'y': //convert y to ies
            $cut = substr( $string, 0, -1 );
            $plural = $cut . 'ies';
            break;

          case 'z': //repeat last consonant and attach es
            $plural = $string . 'zes';
            break;

          default: // just attach an s
            $plural = $string . 's';
            break;
        }
        return $plural;
    }

    public static function isPosttype( $type )
    {
        global $wp_query;
        if($type == get_post_type($wp_query->post->ID)) return true;
        return false;
    }


  }
}
