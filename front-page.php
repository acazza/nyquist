<?php
/**
 * Template Nyquist Frontpage
 *
 * @link https://developer.wordpress.org/themes/template-files-section/page-template-files/
 *
 * @package Nyquist
 */
?>

<?php get_header( 'home' ); ?>

<div id="primary" class="content-area">
  <main id-"main" class="site-main" role="main">
    <div class="container">
      <?php get_template_part('template-parts/section-latest'); ?>
      <?php get_template_part('template-parts/section-featured'); ?>
      <?php get_template_part('template-parts/section-courses'); ?>
    </div> <!-- .container -->
  <main>
<div> <!-- #primary -->

<?php get_footer(); ?>
