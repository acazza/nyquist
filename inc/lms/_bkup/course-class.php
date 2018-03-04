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

      // Create Custom Taxonomy
      $genre_args = array(
        'hierarchical' => true,
      );

      $this->add_taxonomy( 'author', 'radio' , $authors_args );
      $this->add_taxonomy( 'genre', 'check' , $genre_args );


      // Create Custom Fields
      $this->add_custom_fields(
        array(
          'id'        => 'video-meta-box',
          'title'     => __( 'Video' ),
          'fields'    => array(
            array(
                'name'      => 'Write stuff here',
                'desc'      => 'Write your stuff.',
                'id'        => 'aam_write_stuff',
                'std'       => 'Default value here.', //default or placeholder ???
                'type'      => 'editor',
                'required'  => true
            ),
            array(
                'name'      => 'Select Video Host',
                'desc'      => 'Enter video host here.',
                'id'        => 'aam_video_select',
                'std'       => 'Default value here.',
                'type'      => 'text',
                'required'  => true
            ),
            array(
                'name'      => 'Description',
                'desc'      => 'Enter video description here.',
                'id'        => 'aam_video_description',
                'std'       => 'Default value here.',
                'type'      => 'textarea',
                'required'  => true
            ),
            array(
                'name'      => 'URL',
                'desc'      => 'Enter video url here.',
                'id'        => 'amm_video_url',
                'std'       => 'Default value here.',
                'type'      => 'text',
                'required'  => true
            ),
            array(
                'name'      => 'Important',
                'desc'      => 'Is it important?',
                'id'        => 'amm_important',
                'std'       => 'Default value here.',
                'type'      => 'checkbox',
                'required'  => false
            ),
            array(
              'name'      => 'Select Video Host from list',
              'desc'      => 'Select video host from list here.',
              'id'        => 'aam_video_list',
              'std'       => 'Default value here.',
              'type'      => 'radio',
              'required'  => true,
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
              'id'        => 'aam_video_cb',
              'std'       => 'Default value here.',
              'type'      => 'select',
              'required'  => true,
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

      // define the columns to appear on the admin edit screen
      $this->columns(array(
          'cb'          => '<input type="checkbox" />',
          'title'       => __('Title'),
          'post_parent'      => __('Enciclopedia'),
          //'author'      => __('Author'),
          'genre'       => __('Genre'),
          'date'        => __('Date')
      ));


      // populate the price column
      $this->populate_column('post_parent', function($column, $post) {
          echo get_the_title( $post->post_parent);
      });

      // make rating and price columns sortable
      $this->sortable(array(
        //'post_parent' => array('post_parent', false),
        //  'author' => array('author', true),
        'genre' => array('genre', false)
      ));

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

$encyclopedia->add_dynamic_custom_fields(
  array(
    'id'        => 'encyclopedia_info',
    'title'     => __( 'Enciclopedia Info' ),
    'fields'    => array(
      array(
          'name'      => 'ComboBox Test',
          'id'        => 'combotest',
          'type'      => 'select',
          'options'   => array(
            array(
              'label' => 'Combo 1',
              'value' => 'combo1'
            ),
            array(
              'label' => 'Combo 2',
              'value' => 'combo2'
            )
          )
      ),
      array(
          'name'      => 'RadioTest',
          'id'        => 'radiotest',
          'type'      => 'radio',
          'options'   => array(
            array(
              'label' => 'Radio 1',
              'value' => 'radio1'
            ),
            array(
              'label' => 'Radio 2',
              'value' => 'radio2'
            )
          )
      ),
    ),
    'context'   => 'normal',
    'priority'  => 'default',
  )
);

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
          'std'       => 10,
          'required'  => true,
          'type'      => 'number',
          'min'       => 5,
          'max'       => 15,
          'step'      => 1
      ),
      array(
          'name'      => 'Upload an audio file',
          'desc'      => 'Upload an audio file.',
          'id'        => 'audio_file',
          'std'       => '',
          'type'      => 'audio',
      ),
      array(
          'name'      => 'Upload an image file',
          'desc'      => 'Upload an image file.',
          'id'        => 'image_file',
          'std'       => '',
          'type'      => 'image',
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
