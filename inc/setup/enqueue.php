<?php
/**
 * Nyquist Frontend Enqueue - javascript & css files
 *
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 *
 * @package Nyquist
 */

function nyquist_load_scripts()
{
  wp_enqueue_style( 'nyquist', get_template_directory_uri() . '/css/nyquist.min.css', array(), '1.0.0', 'all' );
  wp_enqueue_script( 'nyquist', get_template_directory_uri() . '/js/nyquist.min.js', array('jquery'), '1.0.0', true );
  wp_enqueue_script( 'retina', get_template_directory_uri() . '/js/retina.min.js', array(), '1.3.0', true );
}

add_action( 'wp_enqueue_scripts', 'nyquist_load_scripts' );
