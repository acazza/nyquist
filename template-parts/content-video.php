<?php
/**
 * The template for displaying the content of Video Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-format-video' ); ?>>

  <header class="entry-header">

    <div class="embed-responsive widescreen"><?php echo soundlush_get_embedded_media( array( 'video', 'iframe' ) ); ?></div>

    <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>') ?>

    <div class="entry-meta">
      <?php echo soundlush_posted_meta(); ?>
    </div>
  </header>

  <div class="entry-content">
    <?php if( soundlush_get_attachment() ): ?>
    <a class="standard-featured-link" href="<?php the_permalink(); ?>">
      <div class="standard-featured background-image" style="background-image: url( <?php echo soundlush_get_attachment(); ?> );"></div>
    </a>
    <?php endif ?>

    <div class="entry-excerpt"><?php the_excerpt(); ?></div>
    <div class="button-container">
      <a href="<?php the_permalink(); ?>" class="btn btn-default btn-outlined"><?php _e( 'Read More' ); ?></a>
    </div> <!-- .button-container -->

  </div> <!-- .entry-content -->

  <footer class="entry-footer">
    <?php echo soundlush_posted_footer(); ?>
    <hr>
  </footer>

</article>
