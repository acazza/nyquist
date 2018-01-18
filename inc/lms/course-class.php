<?php


if( !class_exists( 'SoundlushBook') ){

  class SoundlushBook extends SoundlushCustomPostType {

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

          // array(
          //     'label' => 'Select Box',
          //     'desc'  => 'A description for the field.',
          //     'id'    => $prefix.'select',
          //     'type'  => 'select',
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

$book = new SoundlushBook('Book');
$book->create_book();
$mb = new SoundlushRadioTaxonomy('book', 'author' );
