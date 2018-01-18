<?php
/**
 * The template for displaying the content of Image Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nyquist
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nyquist-format-image' ); ?>>

  <header class="entry-header background-image" style="background-image: url( <?php echo nyquist_get_attachment(); ?> );">
    <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>') ?>

    <div class="entry-meta"><?php echo nyquist_posted_meta(); ?></div>

    <div class="entry-excerpt image-caption"><?php the_excerpt(); ?></div>
  </header>

  <footer class="entry-footer">
    <?php echo nyquist_posted_footer(); ?>
    <hr>
  </footer>

</article>
