<?php
/**
 * Soundlush Register Widget Areas
 *
 * @link https://codex.wordpress.org/Function_Reference/register_nav_menus
 *
 * @package com.soundlush.theme.v1
 */


function soundlush_register_sidebar()
{
  register_sidebar(
    array(
      'name' => esc_html( 'Footer Col-1', 'soundlush' ),
      'id' => 'footer-col-1',
      'description' => __( 'Footer Col 1', 'soundlush' ),
      'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
      'after_widget' => '</section>',
      'before_title' => '<h4 class="footer-widget-title">',
      'after_title' => '</h4>',
    )
  );

  register_sidebar(
    array(
      'name' => esc_html( 'Footer Col-2', 'soundlush' ),
      'id' => 'footer-col-2',
      'description' => __( 'Footer Col 2', 'soundlush' ),
      'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
      'after_widget' => '</section>',
      'before_title' => '<h4 class="footer-widget-title">',
      'after_title' => '</h4>',
    )
  );

  register_sidebar(
    array(
      'name' => esc_html( 'Footer Col-3', 'soundlush' ),
      'id' => 'footer-col-3',
      'description' => __( 'Footer Col 3', 'soundlush' ),
      'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
      'after_widget' => '</section>',
      'before_title' => '<h4 class="footer-widget-title">',
      'after_title' => '</h4>',
    )
  );

  register_sidebar(
    array(
      'name' => esc_html( 'Footer Bottom', 'soundlush' ),
      'id' => 'footer-bottom',
      'description' => __( 'Footer Bottom', 'soundlush' ),
      'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
      'after_widget' => '</section>',
      'before_title' => '<h4 class="footer-widget-title">',
      'after_title' => '</h4>',
    )
  );
}

add_action( 'widgets_init', 'soundlush_register_sidebar' );
