<?php
/**
 * The template for displaying the content of Single Post
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<aside id="sidebar-lesson">
  <?php soundlush_generate_course_index(); ?>
</aside>


<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-single-lesson' ); ?>>

  <header class="entry-header">
    <?php the_title( '<h2 class="entry-title">', '</h2>') ?>
  </header>

  <div class="entry-content">
    <?php if( soundlush_get_attachment() ): ?>
      <div class="standard-featured background-image" style="background-image: url( <?php echo soundlush_get_attachment(); ?> );"></div>

    <?php endif ?>
    <?php the_content(); ?>
  </div> <!-- .entry-content -->

  
  <footer class="entry-footer">
    <?php // echo soundlush_posted_footer(); ?>
    <hr>
  </footer>

</article>
