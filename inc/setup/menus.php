<?php
/**
 * Nyquist Register Navigation Menus
 *
 * @link https://codex.wordpress.org/Function_Reference/register_nav_menus
 *
 * @package Nyquist
 */

function nyquist_register_nav_menu()
{
  register_nav_menu( 'primary', 'Header Navigation Menu' );
  register_nav_menu( 'secondary', 'Footer Navigation Menu' );
}

add_action( 'after_setup_theme', 'nyquist_register_nav_menu' );
