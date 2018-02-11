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
            ),
            array(
              'name'      => 'Select Video Host from list',
              'desc'      => 'Select video host from list here.',
              'id'        =>  'aam_video_list',
              'std'       => 'Default value here.',
              'type'      => 'radio',
              'options'   => array(
                array(
                  'label' => 'Youtube',
                  'value' => 'youtube'
                ),
                array(
                  'label' => 'Vimeo',
                  'value' => 'vimeo'
                )
              )
            ),
            array(
              'name'      => 'Select Video Host from Combobox',
              'desc'      => 'Select video host from Combobox.',
              'id'        =>  'aam_video_cb',
              'std'       => 'Default value here.',
              'type'      => 'select',
              'options'   => array(
                array(
                  'label' => 'Youtube',
                  'value' => 'youtube'
                ),
                array(
                  'label' => 'Vimeo',
                  'value' => 'vimeo'
                )
              )
            ),
          ),
          'context'   => 'normal',
          'priority'  => 'default',
          //'pages'     => 'post',
        )
      );

      //
      // $this->add_custom_fields(
      //   array(
      //     'id'        => 'summary-meta-box',
      //     'title'     => __( 'Summary' ),
      //     'fields'    => array(
      //       array(
      //           'name'      => 'Book Summary',
      //           'desc'      => 'Enter your book summary here.',
      //           'id'        =>  'aam_book_summary',
      //           'std'       => 'Type here.',
      //           'type'      => 'editor',
      //       ),
      //     ),
      //     'context'   => 'normal',
      //     'priority'  => 'default',
      //     //'pages'     => 'post',
      //   )
      // );

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






// Create Quiz Post Type
$quiz_args = array(
  'hierarchical' => true,
  'supports'     => array( 'title', 'editor', 'page-attributes')
);
$quiz = new SoundlushCustomPostType('Quiz', $quiz_args);

$quiz->add_custom_fields(
  array(
    'id'        => 'quiz_settings',
    'title'     => __( 'Quiz Settings' ),
    'fields'    => array(
      array(
          'name'      => 'Number of question',
          'desc'      => 'Enter the number of questions in your quiz.',
          'id'        => 'num_questions',
          'type'      => 'text',
      )
    ),
    'context'   => 'normal',
    'priority'  => 'default',
  )
);




// Create Question Post Type
$question_args = array(
  'hierarchical' => true,
  'supports'     => array( 'title', 'editor', 'page-attributes')
);
$question = new SoundlushCustomPostType('Question', $question_args);

$question->add_dynamic_custom_fields(
  array(
    'id'        => 'answer_info',
    'title'     => __( 'Answer Info' ),
    'fields'    => array(
      array(
          'name'      => 'Option Letter',
          'id'        => 'opt_letter',
          'type'      => 'text',
      ),
      array(
          'name'      => 'Answers',
          //'desc'      => 'Enter video host here.',
          'id'        => 'content',
          //'std'       => 'Default value here.',
          'type'      => 'text',
          //'options'   => array( 'Youtube', 'Vimeo', 'Self hosted' )
      ),
      array(
          'name'      => 'Correct',
          'id'        => 'correct',
          'type'      => 'checkbox'
      )
    ),
    'context'   => 'normal',
    'priority'  => 'default',
  )
);

$rel1 = new SoundlushCustomPostRelationships( 'Quiz', 'Question' );
