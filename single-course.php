<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package Nyquist
 */
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id-"main" class="site-main" role="main">
    <div class="container">
      <?php
      if( have_posts() ):
        while( have_posts() ): the_post();

          nyquist_save_post_views( get_the_ID() );

          get_template_part( 'template-parts/single-course', get_post_format() ); ?>
          <section class="article-navigation">
            <?php //the_post_navigation(); ?>
            <?php nyquist_get_post_navigation(); ?>
          </section>
          <?php if( comments_open() ): ?>
            <?php comments_template();?>
          <?php endif;
        endwhile;
      endif;
      ?>
    </div> <!-- .container -->
