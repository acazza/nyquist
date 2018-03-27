<?php
/**
 * Soundlush Theme Support
 *
 * @link https://developer.wordpress.org/reference/functions/add_theme_support/
 *
 * @package com.soundlush.theme.v1
 */

 function mailtrap($phpmailer) {
   $phpmailer->isSMTP();
   $phpmailer->Host = 'smtp.mailtrap.io';
   $phpmailer->SMTPAuth = true;
   $phpmailer->Port = 2525;
   $phpmailer->Username = 'e6a8b2dc92c1ed';
   $phpmailer->Password = '86d14489014aa8';
 }

 add_action('phpmailer_init', 'mailtrap');

function soundlush_theme_support_setup()
{
  add_theme_support( 'post-formats',
    array(
      'aside',
      'gallery',
      'link',
      'image',
      'quote',
      'status',
      'video',
      'audio',
      'chat'
    )
  );
  add_theme_support( 'post-thumbnails' );
  add_theme_support( 'custom-header' );
  add_theme_support( 'custom-logo' );
  add_theme_support( 'html5',
    array(
      'comment-list',
      'comment-form',
      'search-form',
      'gallery',
      'caption'
    )
  );
  add_theme_support( 'customize-selective-refresh-widgets' );
  add_theme_support( 'automatic-feed-links' );
  add_theme_support( 'title-tag' );
}

add_action( 'after_setup_theme', 'soundlush_theme_support_setup' );
