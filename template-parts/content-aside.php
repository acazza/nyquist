<?php
/**
 * The template for displaying the content of Aside Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-format-aside' ); ?>>

  <div class="aside-container">
    <header class="entry-header">

      <div class="entry-meta">
        <?php echo soundlush_posted_meta(); ?>
      </div>
    </header>

    <div class="entry-content">
      <?php if( soundlush_get_attachment() ): ?>
        <div class="aside-featured background-image" style="background-image: url( <?php echo soundlush_get_attachment(); ?> );"></div>
      <?php endif ?>

      <div class="entry-excerpt"><?php the_content(); ?></div>

    </div> <!-- .entry-content -->

    <footer class="entry-footer">
      <?php echo soundlush_posted_footer(); ?>
      <hr>
    </footer>
  </div> <!-- .aside-container -->
</article>
