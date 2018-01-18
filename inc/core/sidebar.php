<?php
/**
 * Nyquist Register Widget Areas
 *
 * @link https://codex.wordpress.org/Function_Reference/register_nav_menus
 *
 * @package Nyquist
 */


function nyquist_register_sidebar()
{
  register_sidebar( array(
    'name' => esc_html( 'Footer Col-1', 'nyquist' ),
    'id' => 'footer-col-1',
    'description' => __( 'Footer Col 1', 'nyquist' ),
    'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
    'after_widget' => '</section>',
    'before_title' => '<h4 class="footer-widget-title">',
    'after_title' => '</h4>',
  ));

  register_sidebar( array(
    'name' => esc_html( 'Footer Col-2', 'nyquist' ),
    'id' => 'footer-col-2',
    'description' => __( 'Footer Col 2', 'nyquist' ),
    'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
    'after_widget' => '</section>',
    'before_title' => '<h4 class="footer-widget-title">',
    'after_title' => '</h4>',
  ));

  register_sidebar( array(
    'name' => esc_html( 'Footer Col-3', 'nyquist' ),
    'id' => 'footer-col-3',
    'description' => __( 'Footer Col 3', 'nyquist' ),
    'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
    'after_widget' => '</section>',
    'before_title' => '<h4 class="footer-widget-title">',
    'after_title' => '</h4>',
  ));

  register_sidebar( array(
    'name' => esc_html( 'Footer Bottom', 'nyquist' ),
    'id' => 'footer-bottom',
    'description' => __( 'Footer Bottom', 'nyquist' ),
    'before_widget' => '<section id="%1$s" class="footer-widget %2$s">',
    'after_widget' => '</section>',
    'before_title' => '<h4 class="footer-widget-title">',
    'after_title' => '</h4>',
  ));
}

add_action( 'widgets_init', 'nyquist_register_sidebar' );
