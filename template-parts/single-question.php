<?php
/**
 * The template for displaying the content of Single Post
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-single-question' ); ?>>

  <?php
  $question_id = get_the_ID();
  $content     = get_the_content();
  ?>

  <header class="entry-header">
    <?php //the_title( '<h2 class="entry-title">', '</h2>') ?>
    <?php the_title( '<h4>', '</h4>') ?>
  </header>

  <div class="entry-content">

    <?php if( soundlush_get_attachment() ): ?>
      <div class="standard-featured background-image" style="background-image: url( <?php echo soundlush_get_attachment(); ?> );"></div>
    <?php endif ?>

    <?php echo $content; ?>

    <?php SoundlushQuiz::setup_questions( $question_id ); ?>

  </div> <!-- .entry-content -->

</article>
