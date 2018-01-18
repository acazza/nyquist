<?php
/**
 * The template for displaying the content of Newsletter - Frontpage Section
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package Nyquist
 */
?>

<section id="section-newsletter" class="">
  <div class="container">
    <?php
      if ( is_active_sidebar( 'cta-section' ) ):
  		  dynamic_sidebar('cta-section');
      endif;
    ?>
  </div> <!-- . container -->
</section>
