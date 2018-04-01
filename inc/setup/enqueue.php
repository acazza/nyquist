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
  wp_enqueue_style( 'fontawesome', get_template_directory_uri() . '/css/fa-svg-with-js.css', array(), '5.0.9', 'all' );
  wp_enqueue_style( 'soundlush', get_template_directory_uri() . '/css/soundlush.min.css', array(), '1.0.0', 'all' );
  wp_enqueue_script( 'fontawesome', get_template_directory_uri() . '/js/fontawesome-all.min.js', array('jquery'), '5.0.9', true );
  wp_enqueue_script( 'soundlush', get_template_directory_uri() . '/js/soundlush.js', array('jquery'), '1.0.0', true );
  wp_enqueue_script( 'retina', get_template_directory_uri() . '/js/retina.min.js', array(), '1.3.0', true );

  global $post;

  wp_localize_script( 'soundlush', 'ajax_soundlush', array(
     'ajax_url'   => admin_url( 'admin-ajax.php' ),
     'ajax_nonce' => wp_create_nonce('frontend_nonce'),
     'postID'     => $post->ID,
  ));

}

add_action( 'wp_enqueue_scripts', 'soundlush_load_scripts' );
