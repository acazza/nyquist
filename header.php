<?php
/**
 * The template for displaying the header
 *
 * This is the template that displays all of the <head> section and everything up until <div id="primary">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
  <meta charset="<?php bloginfo( 'charset' ); ?>">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="profile" href="https://gmpg.org/xfn/11">
  <?php if( is_singular() && pings_open( get_queried_object() ) ): ?>
    <link rel="pingback" href="<?php bloginfo( 'pingback_url' ); ?>">
  <?php endif; ?>
  <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
  <header class="site-header container-fluid background-image" style="background-image: url( <?php header_image(); ?> );">

    <div class="top-bar">
      <div class="container">

      <div class="site-brand">
        <?php if ( has_custom_logo() ): ?>
          <div class="site-logo">
            <?php the_custom_logo(); ?>
          </div> <!-- .site-logo -->
          <h1 class="site-name hide"><?php bloginfo( 'name' ); ?></h1>
        <?php else: ?>
          <h1 class="site-name"><a href="<?php echo get_home_url() ?>"><?php bloginfo( 'name' ); ?></a></h1>
        <?php endif; ?>
      </div><!--.site-brand -->

        <div class="site-nav">
          <nav class="main-nav">
            <?php
              wp_nav_menu( array(
                'theme_location' => 'primary',
                'container' => false,
                'menu_class' => 'nav navbar',
                'walker' => new Walker_Nav_MegaMenu()
              ) );
            ?>
          </nav>
        </div> <!-- .site-nav -->

      </div> <!-- .container -->
    </div> <!-- .top-bar -->

  </header>
