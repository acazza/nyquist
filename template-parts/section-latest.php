<?php
/**
 * The template for displaying the content of Lastest Posts - Frontpage Section
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<section id="section-latest-posts" class="">
  <div class="container">
    <?php echo soundlush_get_latest_posts( 1 ); ?>
  </div> <!-- . container -->
</section>
