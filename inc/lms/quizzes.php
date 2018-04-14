<?php

/**
 * ==============================
 *  POSTTYPE: Question
 * ==============================
 */

$options = array(
    'hierarchical' => false,
    //'rewrite'      => array( 'slug'=> 'anthology' ), // set parent slug
    'show_in_rest' => true,
    'rest_base'    => 'question',
    'rest_controller_class' => 'WP_REST_Posts_Controller',
    'supports'     => array( 'title', 'editor', 'author', 'page-attributes' )
);

$question = new SoundlushPostType('question', $options);

// add the Quiz Pool Taxonomy
$question->taxonomy('quiz_pool');

// set Question menu icon
$question->icon('dashicons-editor-help');

$question->customfields()->add(array(
  'id'        => 'multiple-choice',
  'title'     => __( 'Multiple Choice' ),
  'fields'    => array(
    array(
        'label'     => 'Option',
        'id'        => 'soundlush_answer_option',
        'type'      => 'number',
        'std'       => '',
        'min'       => 1,
        'max'       => 5,
        'step'      => 1,
        'required'  => true
    ),
    array(
        'label'     => 'Statement',
        'id'        => 'soundlush_answer_statement',
        'type'      => 'text',
        'required'  => true,
        'allow_tags'=> true,
    ),
    // array(
    //     'label'     => 'Check if correct',
    //     'id'        => 'soundlush_answer_key',
    //     'type'      => 'checkbox',
    // )
  ),
  'context'   => 'normal',
  'priority'  => 'default',
  'repeater'  => true,
  )
);

$question->customfields()->add(array(
  'id'        => 'question-level',
  'title'     => __( 'Question Level' ),
  'fields'    => array(
    array(
        'label'     => 'Level',
        'id'        => 'soundlush_question_level',
        'type'      => 'select',
        'std'       => '2',
        'options'   => array(
          array(
            'label' => 'Easy',
            'value' => '1'
          ),
          array(
            'label' => 'Normal',
            'value' => '2'
          ),
          array(
            'label' => 'Hard',
            'value' => '3'
          )
        )
    ),
    array(
        'label'     => 'Question Key',
        'id'        => 'soundlush_question_key',
        'type'      => 'number',
        'std'       => '',
        'min'       => 1,
        'max'       => 5,
        'step'      => 1,
        'required'  => true
    )
  ),
  'context'   => 'normal',
  'priority'  => 'default',
  )
);

// register the PostType to WordPress
$question->register();


/**
 * ==============================
 *  TAXONOMY: Quiz Pool
 * ==============================
 */


// create the Quiz Taxonomy
$quiz_pool = new SoundlushTaxonomy('quiz_pool');

// register the taxonomy to WordPress
$quiz_pool->register();



/**
 * ==============================
 *  POSTTYPE: Quiz
 * ==============================
 */

$options = array(
    'hierarchical' => false,
    //'rewrite'      => array( 'slug'=> 'anthology' ), // set parent slug
    'supports'     => array( 'title', 'editor', 'author' )
);

$quiz = new SoundlushPostType('quiz', $options);

// set Quiz menu icon
$quiz->icon('dashicons-welcome-write-blog');

// hide the date and author columns
$quiz->columns()->hide(['author', 'date']);

// add a price and rating column
$quiz->columns()->add([
    'username'        => __('Username'),
    'quiz'            => __('Quiz'),
    'submission_date' => __('Submission Date'),
    'grade'           => __('Grade'),
]);


$quiz->columns()->populate('username', function($column, $post_id) {
    $user_id = get_post_meta( $post_id, '_soundlush_quiz_user_id', true  );
    $user = get_user_by( 'id', $user_id );
    echo $user->first_name . ' ' . $user->last_name;
});

$quiz->columns()->populate('quiz', function($column, $post_id) {
    $quiz_id = get_post_meta( $post_id, '_soundlush_quiz_id', true  );
    echo get_the_title( $quiz_id );
});

$quiz->columns()->populate('submission_date', function($column, $post_id) {
    echo get_the_time( 'Y/m/d g:i A (T)', $post_id );
});

$quiz->columns()->populate('grade', function($column, $post_id) {
    $quiz_grade = get_post_meta( $post_id, '_soundlush_quiz_grade', true  );
    echo $quiz_grade;
});

// set sortable columns
$quiz->columns()->sortable([
    'username'        => [ '_soundlush_quiz_user_id', true ],
    'quiz'            => [ '_soundlush_quiz_id', true],
    'submission_date' => [ 'date', true ],
]);

// set custom field metabox for Quiz
$quiz->customfields()->add( array(
  'id'        => 'quiz_summary',
  'title'     => __( 'Quiz Summary' ),
  'fields'    => array(
    array(
        'label'     => 'User ID',
        'desc'      => 'This is the User ID',
        'std'       => '',
        'id'        => '_soundlush_quiz_user_id',
        'type'      => 'text',
        //'readonly'  => true
    ),
    array(
        'label'     => 'Quiz ID',
        'desc'      => 'This is the Quiz ID',
        'std'       => '',
        'id'        => '_soundlush_quiz_id',
        'type'      => 'text',
        //'readonly'  => true
    ),
    array(
        'label'     => 'Grade',
        'desc'      => 'This is the Quiz Grade',
        'std'       => '',
        'id'        => '_soundlush_quiz_grade',
        'type'      => 'text',
        //'readonly'  => true
    ),
  )
));


$quiz->customfields()->add(array(
  'id'        => 'quiz_details',
  'title'     => __( 'Quiz Details' ),
  'fields'    => array(
    array(
        'label'     => 'Question ID',
        'id'        => 'soundlush_question_id',
        'type'      => 'text',
        'std'       => '',
        //'readonly'  => true
    ),
    array(
        'label'     => 'Question Level',
        'id'        => 'soundlush_question_level',
        'type'      => 'text',
        'std'       => '',
        //'readonly'  => true
    ),
    array(
        'label'     => 'Question Key',
        'id'        => 'soundlush_question_key',
        'type'      => 'text',
        'std'       => '',
        //'readonly'  => true
    ),
    array(
        'label'     => 'User Answer',
        'id'        => 'soundlush_user_answer',
        'type'      => 'text',
        'std'       => '',
        //'readonly'  => true
    ),
    array(
        'label'     => 'Multiplier',
        'id'        => 'soundlush_multiplier',
        'type'      => 'text',
        'std'       => '0',
        //'readonly'  => true
    ),
  ),
  'repeater'  => true,
  )
);

// register the PostType to WordPress
$quiz->register();




/**
 * ==============================
 *  SHORTCODE: Quiz Generation
 * ==============================
 */

add_shortcode( 'quiz', 'soundlush_generate_quiz' );

function soundlush_generate_quiz( $atts, $content = null ){

   // Get the attributes
    $atts = shortcode_atts(
        array(
           'pool'     => '',  // quiz pool term id?
           'qty'      => '5', // qty of total questions
           'easy'     => '4', // qty of easy questions
           'normal'   => '4', // qty of normal questions
           'hard'     => '3', // qty of hard questions
           'duration' => '10' // quiz duration in minutes
        ),
        $atts,
        'quiz'
    );

    if( is_user_logged_in() ) // and user has product and user have not upload it yet
    {

        ob_start();
        include(dirname(__FILE__) . "/../templates/soundlush-quiz-generation.php");
        return ob_get_clean();

    } else {
        return 'You have to be logged in to access the quiz';
    }

}


/**
 * ==============================
 *  AJAX CALLBACK: Save Quiz
 * ==============================
 */

add_action( 'wp_ajax_nopriv_save_user_quiz', 'save_user_quiz' );
add_action( 'wp_ajax_save_user_quiz', 'save_user_quiz' );

function save_user_quiz()
{

    if ( defined( 'DOING_AJAX' ) && DOING_AJAX )
    {

        if( is_user_logged_in() )
        {

            // check nonce before doing anything
            check_ajax_referer( 'frontend_nonce', 'nonce' );

            // retrive values and decode JSON
            $user_id   = $_POST[ 'user_id' ];
            $quiz_id   = $_POST[ 'post_id' ];
            $answers   = json_decode( stripslashes( $_POST[ 'answers' ] ), true);
            $questions = json_decode( stripslashes( $_POST[ 'questions' ] ), true);

            $quiz = array();
            $i    = 0;


            foreach( $questions as $question ){
              if( isset( $answers[$question] ) ){
                  $obj[$question] = $answers[$question];
              } else {
                  $obj[$question] = "blank";
              }
            }


            foreach( $obj as $key => $value )
            {
                $question_key   = get_post_meta( $key, 'soundlush_question_key', true);
                $question_level = get_post_meta( $key, 'soundlush_question_level', true);
                $multiplier     = ($question_key == $value ) ? '1' : '0';

                $quiz[$i] = array(
                    'soundlush_question_id'    => $key,
                    'soundlush_question_key'   => $question_key,
                    'soundlush_question_level' => $question_level,
                    'soundlush_user_answer'    => $value,
                    'soundlush_multiplier'     => $multiplier
                );

                $i++;
            }


            // prepare $args to insert new quiz submission post
            $args = array(
                'post_title'   => get_the_title( $quiz_id ),
                'post_content' => '',
                'post_author'  => 1,
                'post_type'    => 'quiz',
                'post_status'  => 'publish',
                'meta_input'   => array(
                    '_soundlush_quiz_user_id' => $user_id,                  // User ID
                    '_soundlush_quiz_id'      => $quiz_id,                  // Quiz ID
                    '_soundlush_quiz_grade'   =>  calculate_grade( $quiz ), // Quiz Grade
                    '_repeater'               => $quiz                      // Quiz Result
                    //TODO change from repeater
                )
            );

            $post_id = wp_insert_post( $args );

            return $post_id;

            wp_die();
        }
    }
}




function calculate_grade( $quiz ){

    $total = $corrects = 0;

    foreach( $quiz as $question )
    {
      $total  += $question['soundlush_question_level'];
      $corrects += $question['soundlush_question_level'] * $question['soundlush_multiplier'];
    }

    $percent = round( ( $corrects/$total ) * 100, 2 );
    return $percent;
}


 /**
  * ==============================
  *  HELPER FUNCTIONS
  * ==============================
  */

function get_last_quiz()
{

    $user_id = get_current_user_id();
    $post_id = get_the_id();

    $args = array(
        'posts_per_page'   => 1,
        'orderby'          => 'post_date',
        'order'            => 'DESC',
        'post_type'        => 'quiz',
        'post_status'      => 'publish',
        'meta_query' => array(
            'relation'    => 'AND',
            array(
                'key'     => '_soundlush_quiz_user_id',
                'value'   => $user_id,
                'compare' => 'LIKE',
            ),
            array(
                'key'     => '_soundlush_quiz_id',
                'value'   => $post_id,
                'compare' => 'LIKE',
            ),
        ),
    );

    // retrieves last instance of the quiz for this user
    $lastquiz = get_posts( $args );

    // if results, return results
    if( !empty( $lastquiz ) ){
        return $lastquiz;
    }
    return false;

}
