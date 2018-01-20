<?php
/**
 * The template for displaying the content of Course Post Type
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package com.soundlush.theme.v1
 */
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'soundlush-single-course' ); ?>>

  <header class="entry-header">
    <?php the_title( '<h2 class="entry-title">', '</h2>') ?>
    <div class="entry-meta">
      <?php echo soundlush_posted_meta(); ?>
    </div>
  </header>

  <div class="entry-content">
    <?php if( soundlush_get_attachment() ): ?>
      <div class="standard-featured background-image" style="background-image: url( <?php echo soundlush_get_attachment(); ?> );"></div>

    <?php endif ?>
    <?php the_content(); ?>
  </div> <!-- .entry-content -->


  <?php
    //Check if user has purchased the product
    $product = get_field( 'product' );
    if( $product ):
      if ( soundlush_check_purchase( $product ) ) :
        // TODO show personal options & data ...
        echo '<a class="btn btn-default" href=#>' . __( 'GO TO LESSONS', 'soundlush' ) . '</a>';
        echo '<a class="btn btn-default" href=#>' . __( 'RETAKE COURSE', 'soundlush' ) . '</a>';
      else:
        $_product = wc_get_product( $product->ID );
        echo $_product->get_price();
        echo '<a class="btn btn-default" href="' . get_permalink( $product->ID ) . '">' . __( 'BUY NOW', 'soundlush' ) . '</a>';
      endif;
    endif;
  ?>

  <div class="course-syllabus">
  <?php

    // Instructor
    $instructor = get_field( 'course_instructor' );
    if( $instructor ):
      echo $instructor['user_avatar'];
      echo '<p>' . __( 'Instructor:', 'soundlush' ) . ' ' . $instructor['display_name'] . '</p>';
      echo $instructor['user_description'];
    endif;

    // Course Syllabus
    echo '<h3>' . __( 'Course Syllabus', 'soundlush' ) . '</h3>';

    // Description
    if(get_field( 'course-description' ) ):
      echo '<h4>'. __( 'Description', 'soundlush' ) . '</h4>';
      echo get_field( 'course-description' );
    endif;

    // Learning Outcomes
    if(get_field( 'course_learning_outcomes' ) ):
      echo '<h4>'. __( 'Learning Outcomes', 'soundlush' ) . '</h4>';
      echo get_field( 'course_learning_outcomes' );
    endif;

    // Bibliography
    if(get_field( 'course_bibliography' ) ):
      echo '<h4>'. __( 'Bibliography', 'soundlush' ) . '</h4>';
      echo get_field( 'course_bibliography' );
    endif;

    // Course Outline
    soundlush_generate_course_index();

  ?>

  <footer class="entry-footer">
    <?php echo soundlush_posted_footer(); ?>
    <hr>
  </footer>

</article>
