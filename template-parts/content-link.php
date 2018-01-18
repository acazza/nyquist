<?php
/**
 * The template for displaying the content of Link Post Format
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nyquist
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'nyquist-format-link' ); ?>>

  <header class="entry-header">
    <?php
      $link = nyquist_grab_url();
      the_title( '<h2 class="entry-title"><a href="' . $link . '" target="_blank">', '<div class="link-icon">' .  nyquist_get_svg( array( 'icon' => esc_attr( 'link' ) ) )  .  '</div></a></h2>')
    ?>

    <div class="entry-meta">
      <?php echo nyquist_posted_meta(); ?>
    </div>

  </header>

  <hr>

</article>
