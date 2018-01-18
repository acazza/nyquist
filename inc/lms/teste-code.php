<?php

$meta_boxes = array();

//Video meta box
$meta_boxes[] = array(
    'id'        => 'video-meta-box',
    'title'     => __( 'Video', 'iamtheme' ),
    'pages'     => 'post',
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
        array(
            'name'      => 'Select Video Host',
            'id'        => PTHEME . '_video_select',
            'type'      => 'select',
            'options'   => array( 'Youtube', 'Vimeo', 'Self hosted' )
        ),
        array(
            'name'      => 'URL',
            'desc'      => 'Enter video url here.',
            'id'        => PTHEME . '_video_url',
            'std'       => 'Default value here.',
            'type'      => 'text'
        )
    )
);

//Link meta box
$meta_boxes[] = array(
    'id'        => 'link-meta-box',
    'title'     => __( 'Link', 'iamtheme' ),
    'pages'     => 'post',
    'context'   => 'normal',
    'priority'  => 'high',
    'fields'    => array(
        array(
            'name'      => 'Link',
            'desc'      => 'Enter your url here.',
            'id'        => PTHEME . '_link',
            'std'       => 'Default value here.',
            'type'      => 'text'
        )
    )
);

/**
*
*/
class Custom_Meta_Boxes{

    public $_meta_box;

    public function __construct( $meta_box ){

        $this->_meta_box = $meta_box;
        add_action( 'add_meta_boxes', array( $this, 'iam_add_meta_box' ) );
        add_action( 'save_post', array( $this, 'iam_save_meta_box_data' ) );

    }

    /**
     * Adds a meta box to the post editing screen
     */
    public function iam_add_meta_box(){

        add_meta_box(
            $this->_meta_box['id'],
            $this->_meta_box['title'],
            array( &$this, 'iam_display_custom_meta_box' ),
            $this->_meta_box['pages'],
            $this->_meta_box['context'],
            $this->_meta_box['priority']
        );

    }

    /**
     * Render Meta Box content.
     */
    public function iam_display_custom_meta_box() {

        global $post;

        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'iam_nonce_check', 'iam_nonce_check_value' );

        echo '<div class="metabox-wrapper">';

            foreach ( $this->_meta_box['fields'] as $field) {

                
                // get current post meta data
                $meta = get_post_meta( $post->ID, $field['id'], true );

                echo '<div class="metabox-fields metabox_' , $field['type'] , '">';

                    echo '<label for="', $field['id'] , '">', $field['name'] , '</label>';

                    switch ( $field['type'] ) {

                        case 'text':
                        echo '<input type="text" name="', $field['id'] , '" id="', $field['id'] , '" value="', $meta , '" />';
                        echo '<p class="meta-desc">' , $field['desc'] , '</p>';
                        break;

                        case 'textarea':
                        echo '<textarea name="', $field['id'], '" id="', $field['id'], '" cols="60" rows="4" style="width:97%">', $meta , '</textarea>';
                        echo '<p class="meta-desc">' , $field['desc'] , '</p>';
                        break;

                        case 'select':
                        echo '<select name="', $field['id'], '" id="', $field['id'], '">';
                        foreach ( $field['options'] as $option ) {
                            echo '<option', $meta == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                        }
                        echo '</select>';
                        break;

                        case 'radio':
                        foreach ( $field['options'] as $option ) {
                            echo '<input type="radio" name="', $field['id'], '" value="', $option['value'], '"', $meta == $option['value'] ? ' checked="checked"' : '', ' />', $option['name'];
                        }
                        break;

                        case 'checkbox':
                        echo '<input type="checkbox" name="', $field['id'], '" id="', $field['id'], '"', $meta ? ' checked="checked"' : '', ' />';
                        break;

                    }

                echo '</div>';

            }

        echo '</div>';
    }

    /**
     * Save the meta when the post is saved.
     */
    public function iam_save_meta_box_data( $post_id ){

        if( $this->iam_user_can_save( $post_id, 'iam_nonce_check_value' ) ) {

            // Checks for input and sanitizes/saves if needed
            foreach ( $this->_meta_box['fields'] as $field ) {

                $old = get_post_meta( $post_id, $field['id'], true );
                $new = sanitize_text_field( $_POST[ $field['id'] ] );

                if ( $new && $new != $old ) {

                    update_post_meta( $post_id, $field['id'], $new );

                } elseif ( '' == $new && $old ) {

                    delete_post_meta( $post_id, $field['id'], $old );

                }
            }
        }

    }

    /**
     * Determines whether or not the current user has the ability to save meta
     * data associated with this post.
     *
     * @param       int     $post_id    The ID of the post being save
     * @param       bool                Whether or not the user has the ability to save this post.
    */
    public function iam_user_can_save( $post_id, $nonce ){

        // Checks save status
        $is_autosave = wp_is_post_autosave( $post_id );
        $is_revision = wp_is_post_revision( $post_id );
        $is_valid_nonce = ( isset( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], 'iam_nonce_check' ) ) ? 'true' : 'false';

        // Return true if the user is able to save; otherwise, false.
        return ! ( $is_autosave || $is_revision ) && $is_valid_nonce;

    }

}

// Instantiate theme
if ( class_exists( 'Custom_Meta_Boxes' ) ){
    foreach ( $meta_boxes as $meta_box ) {
        $my_box = new Custom_Meta_Boxes( $meta_box );
    }
}

?>
