<?php


if( !class_exists( 'SoundlushBook') )
{

  class SoundlushBook extends SoundlushCustomPostType
  {

    public function create_book()
    {

      // Create Custom Taxonomy
      $authors_args = array(
        'hierarchical' => true,
      );

      $this->add_taxonomy( 'author', 'radio' , $authors_args );



      // Create Custom Fields
      $this->add_custom_fields(
        array(
          'id'        => 'video-meta-box',
          'title'     => __( 'Video' ),
          'fields'    => array(
            array(
                'name'      => 'Select Video Host',
                'desc'      => 'Enter video host here.',
                'id'        =>  'aam_video_select',
                'std'       => 'Default value here.',
                'type'      => 'text',
                //'options'   => array( 'Youtube', 'Vimeo', 'Self hosted' )
            ),
            array(
                'name'      => 'URL',
                'desc'      => 'Enter video url here.',
                'id'        => 'amm_video_url',
                'std'       => 'Default value here.',
                'type'      => 'text'
            )
          ),
          'context'   => 'normal',
          'priority'  => 'default',
          //'pages'     => 'post',
        )
      );


      $this->add_custom_fields(
        array(
          'id'        => 'summary-meta-box',
          'title'     => __( 'Summary' ),
          'fields'    => array(
            array(
                'name'      => 'Book Summary',
                'desc'      => 'Enter your book summary here.',
                'id'        =>  'aam_book_summary',
                'std'       => 'Type here.',
                'type'      => 'editor',
            ),
          ),
          'context'   => 'normal',
          'priority'  => 'default',
          //'pages'     => 'post',
        )
      );

          //     'options' => array (
          //         'one' => array (
          //             'label' => 'Option One',
          //             'value' => 'one'
          //         ),
          //         'two' => array (
          //             'label' => 'Option Two',
          //             'value' => 'two'
          //         ),
          //         'three' => array (
          //             'label' => 'Option Three',
          //             'value' => 'three'
          //         )
          //     )
          // )

    }




  }
}



// Create Books Post Type
$book_args = array(
  'hierarchical' => true,
  'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'page-attributes')
);
$book = new SoundlushBook('Book', $book_args);
$book->create_book();


// Create Enciclopedia Post Type
$encyc_args = array(
  'hierarchical' => true,
  'supports'     => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'page-attributes')
);
$encyclopedia = new SoundlushCustomPostType('Encyclopedia', $encyc_args);


// Create Parent-Child Relationship
$rel = new SoundlushCustomPostRelationships( 'Encyclopedia', 'Book' );


/**
 *
 */

if( !class_exists( 'SoundlushQuestions') )
{

  class SoundlushQuestions extends SoundlushCustomPostType
   {

    public function create_questions() {

      $this->add_custom_fields(
        array(
          'id'        => 'question_type',
          'title'     => __( 'Question Type' ),
          'fields'    => array(
            array(
                'name'      => 'Question Input Type',
                'desc'      => 'Select the type of question.',
                'id'        => 'aam_question_type',
                'std'       => 'Type here.',
                'type'      => 'select',
                'options'   => array( 'True or False', 'Multiple Choice' )
            ),
          ),
          'context'   => 'normal',
          'priority'  => 'default',
        )
      );


      $this->add_custom_fields(
        array(
          'id'        => 'answer_info',
          'title'     => __( 'Answer Info' ),
          'fields'    => $this->create_answers(),
          'context'   => 'normal',
          'priority'  => 'default',
        )
      );

    }

    public function create_answers() {

      global $post;

      //$question_type = get_post_meta( '203', 'aam_question_type', true);
      var_dump($post);
      //var_dump($question_type);
      $question_type = 'Multiple Choice';

      if( $question_type == 'True or False' ){
        $j = 2;
      }elseif( $question_type == 'Multiple Choice' ){
        $j = 5;
      }else{
        return '';
      }

      $options = [];
      $answers  = [];

      for ( $i = 1; $i <= $j; $i++ ) {
        $option = array(
          'name'      => 'Answer ' . $i . ': ',
          'id'        => 'aam_answer' . $i,
          'desc'      => '',
          'type'      => 'textarea',
        );
        array_push( $options, $option );
        array_push( $answers, 'Answer ' . $i );
      }

      $correct = array(
          'name'      => 'Correct Answer: ',
          'desc'      => 'Select the correct answer',
          'id'        => 'aam_correct_answer',
          'std'       => 'Type here.',
          'type'      => 'select',
          'options'   => $answers
      );
      array_push( $options, $correct );

      return $options;
    }


  }
}



// Create Quiz Post Type
$quiz_args = array(
  'hierarchical' => true,
  'supports'     => array( 'title', 'editor', 'page-attributes')
);
$quiz = new SoundlushCustomPostType('Quiz', $quiz_args);


// Create Question Post Type
$question_args = array(
  'hierarchical' => true,
  'supports'     => array( 'title', 'editor', 'page-attributes')
);
$question = new SoundlushCustomPostType('Question', $question_args);
//$question = new SoundlushQuestions('Question', $question_args);
//$question->create_answers();

$question->add_dynamic_custom_fields(
  array(
    'id'        => 'answer_info',
    'title'     => __( 'Answer Info' ),
    'fields'    => "",
    'context'   => 'normal',
    'priority'  => 'default',
  )
);

$rel1 = new SoundlushCustomPostRelationships( 'Quiz', 'Question' );
