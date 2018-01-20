<?php
/**
 * The template for displaying the content of Quote Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-format-quote' ); ?>>

  <header class="entry-header">
    <div class="container-narrow">
      <h2 class="quote-content"><?php echo get_the_content() ?></h2>
      <?php the_title( '<h4 class="quote-author">', '</h4>') ?>
    </div>
  </header>

  <footer class="entry-footer">
    <?php echo soundlush_posted_footer(); ?>
    <hr>
  </footer>

</article>
