<?php
/**
 * Soundlush Frontend Enqueue - javascript & css files
 *
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_script/
 * @link https://developer.wordpress.org/reference/functions/wp_enqueue_style/
 *
 * @package com.soundlush.theme.v1
 */

function soundlush_load_scripts()
{
  wp_enqueue_style( 'soundlush', get_template_directory_uri() . '/css/soundlush.min.css', array(), '1.0.0', 'all' );
  wp_enqueue_script( 'soundlush', get_template_directory_uri() . '/js/soundlush.min.js', array('jquery'), '1.0.0', true );
  wp_enqueue_script( 'retina', get_template_directory_uri() . '/js/retina.min.js', array(), '1.3.0', true );
}

add_action( 'wp_enqueue_scripts', 'soundlush_load_scripts' );
