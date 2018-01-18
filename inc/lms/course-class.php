<?php


if( !class_exists( 'SoundlushBook') ){

  class SoundlushBook extends SoundlushCustomPostType {

    public function create_book()
    {

      // Create custom taxonomy
      $authors_args = array(
        'hierarchical' => true,
      );

      $this->add_taxonomy( 'author', 'radio' , $authors_args );


      $this->add_meta_box(
          'Book Info',
          array(
              'Year' => 'text',
              'Genre' => 'text'
          )
      );

      $this->add_meta_box(
          'Author Info',
          array(
              'Name' => 'text',
              'Nationality' => 'text',
              'Birthday' => 'text'
          )
      );






      // $prefix = '_soundlush_meta_';
      //
      // $this->add_meta_box(
      //     'Video',
      //     array(
      //       array(
      //           'name'      => 'Select Video Host',
      //           'id'        => $prefix . '_video_select',
      //           'type'      => 'select',
      //           'options'   => array( 'Youtube', 'Vimeo', 'Self hosted' )
      //       ),
      //       array(
      //           'name'      => 'URL',
      //           'desc'      => 'Enter video url here.',
      //           'id'        => $prefix . '_video_url',
      //           'std'       => 'Default value here.',
      //           'type'      => 'text'
      //       )
      //     )
      // )

      // Create custom field
      // $prefix = '_soundlush_meta_';
      //
      // $this->add_meta_box(
      //   'Book Info',
      //   array(
      //     array(
      //         'label' => __('Year'),
      //         'desc'  => __('Ano de publicacao'),
      //         'id'    => $prefix.'year',
      //         'type'  => 'text'
      //     ),
      //     array(
      //         'label' => __('Genre'),
      //         'desc'  => __('Genero do livro'),
      //         'id'    => $prefix.'genre',
      //         'type'  => 'text'
      //     )
      //   ),
      //   'normal',
      //   'default'
      // );
      //
      // $this->add_meta_box(
      //   'Test',
      //   array(
      //     'nome' => array(
      //         'label' => 'Teste Nome',
      //         'desc'  => 'Deixe seu nome.',
      //         'id'    => $prefix.'nome',
      //         'type'  => 'text'
      //     ),
      //     'mensagem' => array(
      //         'label' => 'Teste Mensagem',
      //         'desc'  => 'Deixe sua mensagem.',
      //         'id'    => $prefix.'mensagem',
      //         'type'  => 'textarea'
      //       ),
      //     'normal',
      //     'default'

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


        //)
      //);
    }
  }
}

$book = new SoundlushBook('Book');
$book->create_book();
$mb = new SoundlushRadioTaxonomy('book', 'author' );
