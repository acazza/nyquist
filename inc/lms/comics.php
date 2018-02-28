<?php

// create a Comic Book Post Type
$comics = new SoundlushPostType('comic_book');

// add the Volume Taxonomy
$comics->taxonomy('volume');

// hide the date and author columns
$comics->columns()->hide(['date', 'author']);

// add a price and rating column
$comics->columns()->add([
    'rating' => __('Rating'),
    'price'  => __('Price')
]);

// populate the custom column
$comics->columns()->populate('rating', function($column, $post_id) {
    $postmeta = get_post_meta($post_id, 'static_fields', true);
    echo isset( $postmeta['meta_mycustom'] ) ? $postmeta['meta_mycustom'] : '';
});

// populate the custom column
$comics->columns()->populate('price', function($column, $post_id) {
    $postmeta = get_post_meta($post_id, 'static_fields', true);
    echo isset( $postmeta['meta_mycustom_2'] ) ? $postmeta['meta_mycustom_2'] : '';
});

// set sortable columns
$comics->columns()->sortable([
    'rating' => [ 'static_fields[meta_mycustom]', true],
    'price'  => [ 'static_fields[meta_mycustom_2]', true]
]);

// set Books menu icon
$comics->icon('dashicons-book-alt');

// set custom field metabox for Books
$comics->customfields()->add( array(
  'id'        => 'mymetabox_3',
  'title'     => __( 'MyMetabox 3' ),
  'fields'    => array(
    array(
        'name'      => 'mycustom_3',
        'desc'      => 'This is my second custom field.',
        'id'        => 'meta_mycustom_3',
        'std'       => 'Default value here.',
        'type'      => 'text',
        'required'  => false
    ),
    array(
      'name'      => 'mycustom_4',
      'desc'      => 'This is my forth custom field.',
      'id'        => 'meta_mycustom_4',
      'type'      => 'relation',
      'posttype'  => 'comic_book'
    )
  )
));

$comics->customfields()->add(array(
  'id'        => 'mymetabox',
  'title'     => __( 'MyMetabox' ),
  'fields'    => array(
    array(
        'name'      => 'mycustom',
        'desc'      => 'This is my first custom field.',
        'id'        => 'meta_mycustom',
        'std'       => 'Default value here.',
        'type'      => 'text',
        'required'  => false
    )),
));

$comics->customfields()->addRepeater(array(
    'id'        => 'encyclopedia_info',
    'title'     => __( 'Enciclopedia Info' ),
    'fields'    => array(
      array(
          'name'      => 'Upload an image file',
          'desc'      => 'Upload an image file.',
          'id'        => 'image_file',
          'std'       => '',
          'type'      => 'image',
      ),
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

// register the PostType to WordPress
$comics->register();



// create the genre Taxonomy
$volume = new SoundlushTaxonomy('volume');

// set custom fields for Volumes
$volume->customfields()->add(array(
    array(
        'name'      => 'my example',
        'desc'      => 'Just an example.',
        'id'        => 'custom_term_meta',
        'std'       => 'Default value here.',
        'type'      => 'text',
        'required'  =>  false
    ),
    array(
        'name'      => 'my course',
        'desc'      => 'Select the course this term is associated to.',
        'id'        => 'meta_mycourse',
        'std'       => 'Default value here.',
        'type'      => 'relation',
        'posttype'  => 'comic_book',
        'required'  =>  false
    ),
));

// filter terms to be displayed on Edit Post Page
//$volume->filterTerms('meta_mycourse', 'post_parent');

// modify metabox on Edit Post Page ('radio' or 'select')
$volume->modifyMetabox('radio', 'comic_book');

// hide the date and author columns
$volume->columns()->hide(['description', 'slug']);

// add a popularity column to the genre taxonomy
$volume->columns()->add([
     'course' => 'Course'
]);

// populate the new column
$volume->columns()->populate('popularity', function($content, $column, $term_id) {
   $termmeta = get_term_meta($term_id, 'term_meta', true);
   echo isset( $termmeta['meta_mycourse'] ) ? $termmeta['meta_mycourse'] : '';
});

// register the taxonomy to WordPress
$volume->register();
