<?php
/**
 * The template for displaying the content of Single Post
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nyquist
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nyquist-single' ); ?>>

  <header class="entry-header">
    <?php the_title( '<h2 class="entry-title">', '</h2>') ?>

    <div class="entry-meta">
      <?php echo nyquist_posted_meta(); ?>
    </div>
  </header>

  <div class="entry-content">
    <?php if( nyquist_get_attachment() ): ?>
      <div class="standard-featured background-image" style="background-image: url( <?php echo nyquist_get_attachment(); ?> );"></div>

    <?php endif ?>
    <?php the_content(); ?>
  </div> <!-- .entry-content -->

  <footer class="entry-footer">
    <?php echo nyquist_posted_footer(); ?>
    <hr>
  </footer>

</article>
