<?php
/**
 * The template for displaying the content of Link Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-format-link' ); ?>>

  <header class="entry-header">
    <?php
      $link = soundlush_grab_url();
      the_title( '<h2 class="entry-title"><a href="' . $link . '" target="_blank">', '<div class="link-icon">' .  soundlush_get_svg( array( 'icon' => esc_attr( 'link' ) ) )  .  '</div></a></h2>')
    ?>

    <div class="entry-meta">
      <?php echo soundlush_posted_meta(); ?>
    </div>

  </header>

  <hr>

</article>
