<?php
/**
 * The template for displaying the content of Video Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nyquist
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nyquist-format-video' ); ?>>

  <header class="entry-header">

    <div class="embed-responsive widescreen"><?php echo nyquist_get_embedded_media( array( 'video', 'iframe' ) ); ?></div>

    <?php the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>') ?>

    <div class="entry-meta">
      <?php echo nyquist_posted_meta(); ?>
    </div>
  </header>

  <div class="entry-content">
    <?php if( nyquist_get_attachment() ): ?>
    <a class="standard-featured-link" href="<?php the_permalink(); ?>">
      <div class="standard-featured background-image" style="background-image: url( <?php echo nyquist_get_attachment(); ?> );"></div>
    </a>
    <?php endif ?>

    <div class="entry-excerpt"><?php the_excerpt(); ?></div>
    <div class="button-container">
      <a href="<?php the_permalink(); ?>" class="btn btn-default btn-outlined"><?php _e( 'Read More' ); ?></a>
    </div> <!-- .button-container -->

  </div> <!-- .entry-content -->

  <footer class="entry-footer">
    <?php echo nyquist_posted_footer(); ?>
    <hr>
  </footer>

</article>
