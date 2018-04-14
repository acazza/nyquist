<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package com.soundlush.theme.v1
 */
?>

<?php get_header(); ?>

<div id="primary" class="content-area">
  <main id-"main" class="site-main" role="main">
    <div class="container">

      <?php
      if( have_posts() ){

        while( have_posts() ): the_post();

          echo the_title();
          $post_id = get_the_id();

          //if( SoundlushCustomPostQuiz::has_taken_quiz($post_id) ) {
          ?>
            <div id="quiz-result">
              <div id="quiz-percent">
                <?php echo SoundlushCustomPostQuiz::get_quiz_result($post_id);  ?>
              </div>

              <div id="quiz-detail">
                <input type="button" name="display_results" id="display_results" class="btn btn-standard" value="See Details" />
              </div>

              <div id="quiz-retake">
                <input type="submit" name="retake_quiz" id="retake_quiz" class="btn btn-accent" value="Retake Quiz" />
              </div>

            </div>
          <?php
          //} else {
          ?>
            <div class="quiz-start">
              <?php echo the_content(); ?>
              <input type="submit" name="start_quiz" id="start_quiz" class="btn btn-accent" value="Start Quiz" />
            </div>
          <?php
          //}
        endwhile;
      }
      ?>


      <?php SoundlushCustomPostQuiz::get_the_questions( $post_id ); ?>

      <!-- <div class="">
        <input type="submit" name="save_quiz" id="save_quiz" class="btn btn-accent" value="Submit Quiz"/>
      </div> -->

    </div> <!-- .container -->

  <main>
<div> <!-- #primary -->

<?php get_footer(); ?>
