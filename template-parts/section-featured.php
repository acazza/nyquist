<?php
/**
 * The template for displaying the content of Featured Posts - Frontpage Section
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nyquist
 */
?>

<section id="section-featured-posts" class="">
  <div class="container">
    <?php echo nyquist_get_featured_posts( 3 ); ?>
  </div> <!-- . container -->
</section>
