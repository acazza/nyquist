<?php
/**
 * Soundlush Custom Post Types Quiz Class
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 * @package soundlush
 */

if( !class_exists( 'SoundlushCustomPostQuiz') )
{

  class SoundlushCustomPostQuiz
  {

    public static $questions = array();


    /**
     * Check if user has already taken the quiz
     * @param $post_id
     */

    static function has_taken_quiz($post_id)
    {
      //check if there is saved usermeta for that quiz
      if( is_user_logged_in() )
      {
        $user = wp_get_current_user();
        $user_id = $user->ID;

        $usermeta = get_user_meta( $user_id, 'user_quiz', false );

        //verify if user has saved data for this quiz id
        foreach( $usermeta as $meta ){
          if( isset( $meta[$post_id] ) ) {
            return true;
          }else {
            return false;
          }
        }
      } else {
        echo 'You are not logged in';
      }
    }



    /**
     * Retrieve user quiz results
     * @param $post_id
     */

    static function get_quiz_result($post_id)
    {
      if( is_user_logged_in() )
      {
        $user = wp_get_current_user();
        $user_id = $user->ID;

        $usermeta = get_user_meta( $user_id, 'user_quiz', false );
        $quizmeta = $usermeta[0][$post_id];
        var_dump($quizmeta);
        echo self::display_gabarito( $quizmeta );

        $result = self::calculate_grade( $quizmeta );

        return $result;
      }

    }

    static function calculate_grade( $quizmeta ){

      $total = $rights = 0;

      foreach( $quizmeta as $question ){
        $total++;
        $rights += $question['multiplier'];
      }

      //round up percentage to nearest integer
      $percent = ceil( ($rights/$total)*100 );
      return $percent . ' %';
    }


    static function display_gabarito( $quizmeta )
    {
      $count = 0;

      //loop through all questions

      $questions = array(203, 229);

      foreach( $questions as $question_id ) {
        $postmeta = get_post_meta( $question_id, 'dynamic_fields', false );

        $count++;
        echo $count .'. ' . get_the_title($question_id);
        echo '<ul>';
          foreach( $postmeta[0] as $key => $value ){
            if( $value['opt_letter'] == $quizmeta[$question_id]['user_answer'] ){
              if( $quizmeta[$question_id]['multiplier'] == 1 ){
                echo '<li style="color: green">';
              } else {
                  echo '<li style="color: red">';
              }
            } else {
              echo '<li>';
            }
            echo $value['opt_letter'] . ') ' . $value['content'] . '</li>';
          }
        echo '</ul>';
      }

    }

    /**
     * Delete user quiz results
     * @param $post_id
     */

    static function retake_quiz($post_id)
    {
      if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
      {
        if( is_user_logged_in() )
        {
          $user = wp_get_current_user();
          $user_id = $user->ID;

          delete_user_meta( $user_id, 'user_quiz' );
        }
      }
      wp_die();
    }



    /**
     * Get all questions related to the quiz
     * @param $post_id
     */

    static function get_the_questions( $post_id )
    {
      // TODO get number of questions
      $postmeta = get_post_meta( $post_id, 'num_questions', false);
      var_dump($postmeta);
      $num_questions = ( isset( $postmeta ) && ! empty( $postmeta ) ) ? $postmeta : 10 ;
      var_dump($num_questions);

      $args = array(
        'post_parent'    => $post_id,
        'post_type'      => 'question',
        'numberposts'    => -1,
        'posts_per_page' => $num_questions,
        'post_status'    => 'publish',
        'orderby'        => 'rand'
      );

      $children = new WP_Query( $args );

      // Loop
      if( $children->have_posts() )
      {
        echo '<div class="quiz-questions">';

        while ($children->have_posts()) : $children->the_post();

          //save all questions generated for this quiz by ID
          array_push( static::$questions, $children->post->ID );

          get_template_part( 'template-parts/single-question', get_post_format() );

        endwhile;

        echo '</div>';


        echo '<div class="">';
        echo '<input type="submit" name="save_quiz" id="save_quiz" class="btn btn-accent" value="Submit Quiz"/>';
        echo '</div>';

      } else {

        echo 'There are no questions associated with this quiz.';

      }
    }


    static function setup_questions( $question_id ){

      $postmeta = get_post_meta( $question_id, 'dynamic_fields', false );
      $quiz_id = wp_get_post_parent_id( $question_id );

      echo '<ul>';

          foreach( $postmeta[0] as $key => $value )
          {
            echo '<li>';
            echo '<input type="radio" name="user_quiz_' . $quiz_id . '_' . $question_id . '" value="' . $value['opt_letter'] . '">  ';
            echo $value['opt_letter'] . ') ' . $value['content'];
            echo '</li>';
          }

       echo '</ul>';
    }



    /**
     * Save user quiz results
     *
     */

    static function save_the_questions()
    {

      $bla = array();
      //$bla = static::$questions; NOT WORKING
      // TODO add nonce field ???

      if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {

        if( is_user_logged_in() )
        {
          $user = wp_get_current_user();
          $user_id = $user->ID;
          $user_quiz = array();
          array_push( $bla, 203, 229);

          foreach( $bla as $key => $value ) {

            $quiz_id = wp_get_post_parent_id( $value );
            $post_question = 'user_quiz_' . $quiz_id . '_' . $value;

            $user_answer = isset($_POST[$post_question ] ) ? $_POST[$post_question] : '0';

            $correct = self::grade_quiz( $value );

            if( $user_answer == $correct ){
              $grade = 1;
            } else {
              $grade = 0;
            }

            //add question points

            $user_quiz[$quiz_id][$value] = array(
                'user_answer' => $user_answer,
                'template_answer' => $correct,
                'multiplier' => $grade
            );

          }

          update_user_meta( $user_id, 'user_quiz', $user_quiz );
        }
      }
      wp_die();

    }


    static function grade_quiz( $value ) {

      $postmeta = get_post_meta( $value, 'dynamic_fields', true );
      $correct = '';

      if( isset( $postmeta ) ) {
        foreach( $postmeta as $meta ){
          if( $meta['correct'] ){
            $correct = $meta['opt_letter'];
          }
        }
      }
      return $correct;
    }
  }



}

add_action( 'wp_ajax_nopriv_retake_user_quiz', 'SoundlushCustomPostQuiz::retake_quiz' );
add_action( 'wp_ajax_retake_user_quiz', 'SoundlushCustomPostQuiz::retake_quiz' );


add_action( 'wp_ajax_nopriv_save_user_quiz', 'SoundlushCustomPostQuiz::save_the_questions' );
add_action( 'wp_ajax_save_user_quiz', 'SoundlushCustomPostQuiz::save_the_questions' );


add_action( 'wp_ajax_nopriv_start_user_quiz', 'SoundlushCustomPostQuiz::get_the_questions' );
add_action( 'wp_ajax_start_user_quiz', 'SoundlushCustomPostQuiz::get_the_questions' );
