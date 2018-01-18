<?php
/**
 * The template for displaying the content of Audio Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nyquist
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nyquist-format-audio' ); ?>>

  <header class="entry-header">
    <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>') ?>

    <div class="entry-meta"><?php echo nyquist_posted_meta(); ?></div>
  </header>

  <div class="entry-content"><?php echo nyquist_get_embedded_media( array( 'audio', 'iframe' ) ); ?></div>

  <footer class="entry-footer">
    <?php echo nyquist_posted_footer(); ?>
    <hr>
  </footer>

</article>
