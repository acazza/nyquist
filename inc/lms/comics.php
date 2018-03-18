<?php

/**
 * Create a Anthology Post Type
 */


$options = array(
    'hierarchical' => true,
    'supports'     => array( 'title', 'editor', 'author','thumbnail', 'excerpt', 'page-attributes' )
);

$anthology = new SoundlushPostType('anthology', $options);
$anthology->taxonomy('volume');
$anthology->register();



/**
 * Create a Comic Book Post Type
 */

$options = array(
    'hierarchical' => true,
    'rewrite'      => array( 'slug'=> 'anthology' ), // set parent slug
    'supports'     => array( 'title', 'editor', 'author','thumbnail', 'excerpt', 'page-attributes' )
);


$comics = new SoundlushPostType('comic_book', $options);

$comics->setAsParent('anthology');

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
        'name'      => 'My Custom 3',
        'desc'      => 'This is my second custom field.',
        'id'        => '_soundlush_mycustom_3',
        'std'       => 'Default value here.',
        'type'      => 'text',
        'required'  => false
    ),
    array(
        'name'      => 'My Custom 4',
        'desc'      => 'This is my forth custom field.',
        'id'        => '_soundlush_mycustom_4',
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
        'name'      => 'My Custom',
        'desc'      => 'This is my file custom field.',
        'id'        => '_soundlush_mycustom',
        'std'       => 'No saved image.',
        'type'      => 'file',
        'accept'    => '.jpg, .jpeg, .png, .gif, .mp3, .wav, .ogg',
        'required'  => false
    )),
));

//$comics->customfields()->addRepeater(array(
$comics->customfields()->add(array(
    'id'        => 'encyclopedia_info',
    'title'     => __( 'Enciclopedia Info' ),
    'fields'    => array(
      array(
          'name'      => 'ComboBox Test',
          'id'        => '_soundlush_combotest',
          'type'      => 'select',
          'options'   => array(
            array(
              'label' => 'Combo 1',
              'value' => '_soundlush_combo1'
            ),
            array(
              'label' => 'Combo 2',
              'value' => '_soundlush_combo2'
            )
          )
      ),
      array(
          'name'      => 'RadioTest',
          'id'        => '_soundlush_radiotest',
          'type'      => 'radio',
          'options'   => array(
            array(
              'label' => 'Radio 1',
              'value' => '_soundlush_radio1'
            ),
            array(
              'label' => 'Radio 2',
              'value' => '_soundlush_radio2'
            )
          )
      ),
    ),
    'context'   => 'normal',
    'priority'  => 'default',
    'repeater'  => true,
  )
);

// register the PostType to WordPress
$comics->register();



/**
 * Create a Volume Taxonomy
 */


// create the volume Taxonomy
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
        'posttype'  => 'anthology',
        'required'  =>  false
    ),
));

// filter terms to be displayed on Edit Custom Post Page
$volume->filterTerms('term_meta', 'post_parent', 'comic_book');

// modify metabox on Edit Post Page ('radio' or 'select')
$volume->modifyMetabox('radio', 'comic_book');

// hide the date and author columns
$volume->columns()->hide(['description', 'slug']);

// add a popularity column to the genre taxonomy
$volume->columns()->add([
     'course' => 'Course'
]);

// populate the new column
$volume->columns()->populate('course', function($content, $column, $term_id) {
   $termmeta = get_term_meta($term_id, 'term_meta', true);
   echo isset( $termmeta['meta_mycourse'] ) ? get_the_title( $termmeta['meta_mycourse'] ) : '';
});

// set sortable columns
$volume->columns()->sortable([
    'course' => [ 'term_meta[meta_mycourse]', true],
]);

// register the taxonomy to WordPress
$volume->register();
