<?php

if( !class_exists( 'SoundlushPostMeta' ) )
{
class SoundlushPostMeta
{

  /**
   * The name for the PostType
   * @var string
   */
  public $posttype_name;



  /**
   * List of custom fields
   * @var array
   */
  public $allfields;



  public function __construct($posttype_name){
    $this->posttype_name = $posttype_name;
  }



  /**
  *  Add
  *  Register custom field metabox for Posttype.
  *  @param array  $metabox
  */
  public function add( $metabox )
  {
      // test if there are metabox arguments
      if( empty( $metabox ) ) return;

      // metabox variables
      $box_id         = SoundlushHelpers::uglify( $metabox['id'] );
      $box_title      = SoundlushHelpers::beautify( $metabox['title'] );
      $box_context    = isset( $metabox['context'] )  ? $metabox['context']  : 'normal';
      $box_priority   = isset( $metabox['priority'] ) ? $metabox['priority'] : 'default';
      $fields         = $metabox['fields'];
      $repeater       = ( isset( $metabox['repeater'] ) && $metabox['repeater'] == true ) ? true : false;


      // update global list of fields
      foreach( $fields as $field ){
        $this->allfields[] = $field;
      }

      // register metabox
      add_action( 'add_meta_boxes', function() use( $box_id, $box_title, $box_context, $box_priority, $fields, $repeater )
      {
        add_meta_box(
          $box_id,
          $box_title,
          ( $repeater ) ? array( &$this, 'displayRepeaterMetabox' ) : array( &$this, 'displayMetabox' ) ,
          $this->posttype_name,
          $box_context,
          $box_priority,
          $fields
        );
      }, 10, 1 );
  }



  /**
  *  Display Metabox
  *  Display html for custom field metabox for Posttype Callback Function.
  *  @param object $post
  *  @param array  $data
  */
  public function displayMetabox( $post, $data )
  {
    // get fields from data array
    $fields = $data['args'];

    // create nonce field
    wp_nonce_field( basename( __FILE__ ), 'custom_post_type_nonce' );


    echo '<table class="form-table">';

    // loop through fields
    foreach( $fields as $field )
    {
      $html        = '';
      $id          = SoundlushHelpers::uglify( $field['id'] );
      $name        = SoundlushHelpers::beautify( $field['name'] );
      $type        = isset( $field['type'] ) ? $field['type'] : 'text';
      $required    = ( isset( $field['required'] ) && $field['required'] ) ? ' required' : '';
      $description = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : '';
      $standard    = isset( $field['std'] ) ? $field['std'] : '';


      // check if there is saved metadata for the field, if not use default value
      $postmeta = get_post_meta( $post->ID, $id, true );
      $meta     = !empty( $postmeta ) ? $postmeta : $standard;


      switch ( $type )
      {
        case 'text':

            $html = '<tr><th scope="row"><label for="' . $id . '">' . $name . ': </label></th><td><input type="text" class="widefat" name="' . $id . '" id="' . $id . '" value="' . $meta  . '"' . $required . ' />' . $description . '</td></tr>';
            break;

        case 'number':

            $min  = isset( $field['min'] ) ? ' min="' . $field['min'] . '" ' : '';
            $max  = isset( $field['max'] ) ? ' max="' . $field['max'] . '" ' : '';
            $step = isset( $field['step'] ) ? ' step="' . $field['step'] . '" ' : '';

            $html = '<tr><th scope="row"><label for="' . $id . '">' . $name . ': </label></th><td><input type="number" name="' . $id . '" id="' . $id . '" value="' . $meta . '"' . $required . $min . $max . $step . ' /></br>' . $description . '</td></tr>';
            break;

        case 'file':

            $upload_dir    = wp_upload_dir();
            $upload_subdir = trailingslashit( $upload_dir['baseurl'] ) . 'soundlush_uploads/postmeta';

            $accept = ' accept="' . $field['accept'] . '"';
            $preview = '';

            if ( strpos( $meta['type'], 'image') !== false)
              $preview = '<img src="'. trailingslashit( $upload_subdir ) . $meta['name'] . '" width="150" height="150" >';
            if ( strpos( $meta['type'], 'audio') !== false)
              $preview = '<audio controls> <source src="'. trailingslashit( $upload_subdir ) . $meta['name'] . '" type="'.$meta['type'].' ">Your browser does not support the audio element.</audio>';


            $html = '<tr><th scope="row"><label for="' . $id . '">' . $name . ': </label></th><td>' . $preview . '<p>' . $meta['name'] . '</p><input type="file" class="widefat" name="' . $id . '" id="' . $id . '" value="' . $meta['name']  . '"' . $required . $accept . ' multiple="false"/>' . $description . '</td></tr>';
            break;

        case 'textarea':

            $html = '<tr><th scope="row"><label for="' . $id . '">' . $name . ': </label></th><td><textarea class="widefat" name="' . $id . '" id="' . $id . '" cols="60" rows="4" style="width:96%"' . $required . ' >' . $meta . '</textarea>'. $description . '</td></tr>';
            break;

        case 'editor':

            $settings = array(
              'wpautop'          => false,
              'media_buttons'    => false,
              'textarea_name'    => $id,
              'textarea_rows'    =>  get_option('default_post_edit_rows', 10),
              'tabindex'         => '',
              'editor_css'       => '',
              'editor_class'     => '',
              'editor_height'    => '',
              'teeny'            => false,
              'dfw'              => false,
              'tinymce'          => true,
              'quicktags'        => true,
              'drag_drop_upload' => false
            );

            ob_start(); //create buffer & echo the editor to the buffer
            wp_editor( htmlspecialchars_decode( $meta ), $id, $settings );

            $html = '<tr><th scope="row"><label for="' . $id . '">' . $name . ': </label></th><td>';
            $html .= ob_get_clean(); //store the contents of the buffer in the variable
            $html .= $description .'</td></tr>';
            break;

        case 'checkbox':

            //WHat to write here!!! fieldset ???
            $html = '<tr><th scope="row"><legend>Click me:</legend></th><td><input type="checkbox" name="' . $id . '" id="' . $id . '"' . ( $meta ? ' checked="checked"' : '') . ' /><label for="' . $id . '">' . $name . ' </label></br>' . $description . '</td></tr>';
            break;

        case 'select':

            $html = '<tr><th scope="row"><label for="' . $id . '" >' . $name . ': </label></th><td><select name="' . $id . '" id="' . $id . '" >';
            foreach ( $field['options'] as $option ) {
              $html .= '<option value="' . $option['value'] . '" ' . ( $meta == $option['value'] ? '" selected="selected"' : '' ) . '>' . $option['label'] . '</option>';
            }
            $html .= '</select></br>' . $description . '</td></tr>';
            break;

        case 'radio':

            $html = '<tr><th scope="row"><label>' . $name . ': </label></th><td><ul>';
            foreach ( $field['options'] as $option ) {
              $html .= '<li><input type="radio" name="' . $id . '" id="' . $option['value'] . '" value="' . $option['value'] . '"' . ( $meta == $option['value'] ? ' checked="checked"' : '' ) . $required . '/><label for="' . $option['value'] . '">' . $option['label'] . '</label></li>';
            }
            $html .= '</ul>' . $description . '</td></tr>';
            break;

        case 'relation':

            $posttype  = ( isset( $field['posttype'] ) && post_type_exists( $field['posttype'] ) ) ? $field['posttype'] : '' ;

            $items = query_posts(array('post_type' => $posttype) );

            $html = '<tr><th scope="row"><label for="' . $id . '" >' . $name . ': </label></th><td><select name="' . $id . '" id="' . $id . '" >';
            foreach ( $items as $item ) {
              $html .= '<option value="' . $item->ID . '" ' . ( $meta == $item->ID ? '" selected="selected"' : '' ) . '>' . $item->post_title . '</option>';
            }
            $html .= '</select></br>' . $description . '</td></tr>';
            break;

        default:
            break;
      }

      echo $html;
    }

    echo '</table>';

    //var_dump($this->allfields);

  }



  /**
  *  Add Repeater
  *  Register repeater custom field metabox for Posttype.
  *  @param array  $metabox
  */
  // public function addRepeater( $metabox )
  // {
  //     // test if there are metabox arguments
  //     if( empty( $metabox ) ) return;
  //
  //     // metabox variables
  //     $box_id         = SoundlushHelpers::uglify( $metabox['id'] );
  //     $box_title      = SoundlushHelpers::beautify( $metabox['title'] );
  //     $box_context    = isset( $metabox['context'] ) ? $metabox['context']  : 'normal';
  //     $box_priority   = isset( $metabox['priority']) ? $metabox['priority'] : 'default';
  //     $fields         = $metabox['fields'];
  //
  //
  //     // register metabox
  //     add_action( 'add_meta_boxes', function() use( $box_id, $box_title, $box_context, $box_priority, $fields )
  //     {
  //       add_meta_box(
  //         $box_id,
  //         $box_title,
  //         array( &$this, 'displayRepeaterMetabox' ),
  //         $this->posttype_name,
  //         $box_context,
  //         $box_priority,
  //         $fields
  //       );
  //     }, 10, 1 );
  // }



  /**
  *  Display Repeater Metabox
  *  Display html for repeater custom field metabox for Posttype Callback Function.
  *  @param object $post
  *  @param array  $data
  */
  public function displayRepeaterMetabox( $post, $data )
  {

      // get fields from data array
      $fields = $data['args'];

      // create nonce field
      wp_nonce_field( basename( __FILE__ ), 'custom_post_type_nonce' );

      echo '<div id="meta_inner">';

      //get the saved meta as an array
      $postmeta = get_post_meta( $post->ID, 'dynamic_fields', false );

      $c = 0;
      $output = '';

      //if there is saved postdata for the post
      if( is_array( $postmeta ) && ( ! empty( $postmeta ) && isset( $postmeta ) ) )
      {
        foreach( $postmeta as $key =>$values )
        {
            foreach( $values as $index => $answer )
            {
              echo '<div class="repeater" style="padding: 0 1em 2em; border-bottom: 1px solid #ccc ">';
              echo '<table class="form-table">';

              $output = '';

              foreach( $fields as $field )
              {
                $html        = '';
                $id          = SoundlushHelpers::uglify( $field['id'] );
                $name        = SoundlushHelpers::beautify( $field['name'] );
                $type        = isset( $field['type'] ) ? $field['type'] : 'text';
                $required    = ( isset( $field['required'] ) && $field['required'] ) ? ' required' : '';
                $description = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : '';
                $standard    = isset( $field['std'] ) ? $field['std'] : '';

                //check if there is saved metadata for the field, if not use default value
                $meta        = isset( $answer[ $field['id'] ] ) ? $answer[ $field['id'] ] : $standard ;

                switch ( $type )
                {
                  case 'text':

                    echo '<tr><th scope="row"><label for="_repeater_' . $c . '_' . $id . '">' . $name . ': </label></th><td><input type="text" class="widefat" name="_repeater_' . $c . $id . '" id="_repeater_' . $c . $id . '" value="' . $meta  . '"' . $required . ' />' . $description . '</td></tr>';
                    break;

                  case 'number':

                    $min  = isset( $field['min'] ) ? ' min="' . $field['min'] . '" ' : '';
                    $max  = isset( $field['max'] ) ? ' max="' . $field['max'] . '" ' : '';
                    $step = isset( $field['step'] ) ? ' step="' . $field['step'] . '" ' : '';

                    echo '<tr><th scope="row"><label for="_repeater_' . $c .$id . '">' . $name . ': </label></th><td><input type="number" name="_repeater_' . $c . $id . '" id="_repeater_' . $c . $id . '" value="' . $meta . '"' . $required . $min . $max . $step . ' /></br>' . $description . '</td></tr>';
                    break;

                  case 'audio':

                    echo '<tr><th scope="row"><label for="_repeater_' . $c . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="_repeater_' . $c . $id . '" id="_repeater_' . $c . $id . '" value="' . $meta  . '"' . $required . ' accept=".mp3, .wav, .ogg" />' . $description . '</td></tr>';
                    break;

                  case 'image':

                    // echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><img href="' . $meta . '"/><input type="file" class="widefat" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" value="' . $meta  . '"' . $required . 'accept=".jpg, .jpeg, .png, .gif" />' . $description . '</td></tr>';

                    echo '<tr><th scope="row"><label for="_repeater_' . $c . $id . '">' . $name . ': </label></th><td>' .  $meta . '<input type="file" class="widefat" name="_repeater_' . $c . $id . '" id="_repeater_' . $c . $id . '" value="' . $meta  . '"' . $required . ' accept=".jpg, .jpeg, .png, .gif" />' . $description . '</td></tr>';
                    break;

                  case 'textarea':

                    echo '<tr><th scope="row"><label for="_repeater_' . $c . $id . '">' . $name . ': </label></th><td><textarea class="widefat" name="_repeater_' . $c . $id . '" id="_repeater_' . $c . $id . '" cols="60" rows="4" style="width:96%"' . $required . ' >' . $meta . '</textarea>'. $description . '</td></tr>';
                    break;


                  case 'checkbox':

                    echo '<tr><th scope="row"><legend>Click me:</legend></th><td><input type="checkbox" name="_repeater_' . $c . $id . '" id="_repeater_' . $c . $id . '"' . ( $meta ? ' checked="checked"' : '') . ' /><label for="_repeater_' . $c . $id . '">' . $name . ' </label></br>' . $description . '</td></tr>';
                    break;

                  case 'select':

                    echo '<tr><th scope="row"><label for="_repeater_' . $c . $id . '" >' . $name . ': </label></th><td><select name="_repeater_' . $c . $id . '" id="_repeater_' . $c . $id . '" >';
                    foreach ( $field['options'] as $option ) {
                      echo '<option value="' . $option['value'] . '" ' . ( $meta == $option['value'] ? '" selected="selected"' : '' ) . '>' . $option['label'] . '</option>';
                    }
                    echo '</select></br>' . $description . '</td></tr>';
                    break;

                  case 'radio':

                    echo '<tr><th scope="row"><label>' . $name . ': </label></th><td><ul>';
                    foreach ( $field['options'] as $option ) {
                      echo '<li><input type="radio" name="_repeater_' . $c . $id . '" id="_repeater_' . $c . $option['value'] . '" value="' . $option['value'] . '"' . ( $meta == $option['value'] ? ' checked="checked"' : '' ) . $required . '/><label for="_repeater_' . $c . $option['value'] . '">' . $option['label'] . '</label></li>';
                    }
                    echo '</ul>' . $description . '</td></tr>';
                    break;

                  default:
                    break;
                }
              }
              $c = $c +1;
              echo '</table>';
              echo '<button class="remove button-secondary">' .  __( 'Remove Item' ) .  '</button>';
              echo '</div>';
            }
          }
        }

        //Add new
        foreach( $fields as $field )
        {
          $id          = SoundlushHelpers::uglify( $field['id'] );
          $name        = SoundlushHelpers::beautify( $field['name'] );
          $type        = isset( $field['type'] ) ? $field['type'] : 'text';
          $required    = ( isset( $field['required'] ) && $field['required'] ) ? ' required' : '';
          $description = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : '';
          $standard    = isset( $field['std'] ) ? $field['std'] : '';

          //TODO add value = standart

          switch ( $type )
          {
            case 'text':

              $output .=  '<tr><th scope="row"><label for="_repeater_count_variable' . $id . '">' . $name . ': </label></th><td><input type="text" class="widefat" name="_repeater_count_variable' . $id . '" id="_repeater_count_variable' . $id . '"' . $required . ' />' . $description . '</td></tr>';
              break;

            case 'number':

              $min  = isset( $field['min'] ) ? ' min="' . $field['min'] . '" ' : '';
              $max  = isset( $field['max'] ) ? ' max="' . $field['max'] . '" ' : '';
              $step = isset( $field['step'] ) ? ' step="' . $field['step'] . '" ' : '';

              $output .= '<tr><th scope="row"><label for="_repeater_count_variable' . $id . '">' . $name . ': </label></th><td><input type="number" name="_repeater_count_variable' . $id . '" id="_repeater_count_variable' . $id . '"' . $required . $min . $max . $step . ' /></br>' . $description . '</td></tr>';
              break;

            case 'audio':

              $output .= '<tr><th scope="row"><label for="_repeater_count_variable' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="_repeater_count_variable' . $id . '" id="_repeater_count_variable' . $id . '"' . $required . 'accept=".mp3, .wav, .ogg" />' . $description . '</td></tr>';
              break;

            case 'image':

              $output .= '<tr><th scope="row"><label for="_repeater_count_variable' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="_repeater_count_variable' . $id . '" id="_repeater_count_variable' . $id . '"' . $required . 'accept=".jpg, .jpeg, .png, .gif" />' . $description . '</td></tr>';
              break;

            case 'textarea':

              $output .= '<tr><th scope="row"><label for="_repeater_count_variable' . $id . '">' . $name . ': </label></th><td><textarea class="widefat" name="_repeater_count_variable' . $id . '" id="_repeater_count_variable' . $id . '" cols="60" rows="4" style="width:96%"' . $required . ' ></textarea>'. $description . '</td></tr>';
              break;

            case 'checkbox':

              $output .= '<tr><th scope="row"><legend>Click me:</legend></th><td><input type="checkbox" name="_repeater_count_variable' . $id . '" id="_repeater_count_variable' . $id . '" /><label for="_repeater_count_variable' . $id . '">' . $name . ' </label></br>' . $description . '</td></tr>';
              break;

            case 'select':

              $output .= '<tr><th scope="row"><label for="_repeater_count_variable' . $id . '" >' . $name . ': </label></th><td><select name="_repeater_count_variable' . $id . '" id="_repeater_count_variable' . $id . '" >';
              foreach ( $field['options'] as $option ) {
                $output .= '<option value="' . $option['value'] . '">' . $option['label'] . '</option>';
              }
              $output .= '</select></br>' . $description . '</td></tr>';
              break;

            case 'radio':

              $output .= '<tr><th scope="row"><label>' . $name . ': </label></th><td><ul>';
              foreach ( $field['options'] as $option ) {
                $output .= '<li><input type="radio" name="_repeater_count_variable' . $id . '" id="_repeater_count_variable' . $option['value'] . '" value="' . $option['value'] . '"' . $required . '/><label for="_repeater_count_variable' . $option['value'] . '">' . $option['label'] . '</label></li>';
              }
              $output .= '</ul>' . $description . '</td></tr>';
              break;
          }
        }


      ?>
      <span id="here" style="display: block;"></span>
      <button class="add button-primary" style="margin: 2em 0;"><?php _e( 'Add Item' ); ?></button>

      <script>
          var $ =jQuery.noConflict();
          $( document ).ready( function()
          {
            var count = <?php echo $c; ?>;
            var output = '<?php echo $output; ?>';


            $( ".add" ).click( function()
            {
                count = count + 1;

                //substitute placeholder by the count variable
                var res = output.replace(/count_variable/g, count);

                $('#here').append( '<div class="repeater"><table class="form-table">' + res + '</table><button class="remove button-secondary">Remove Answer</button></div>' );

                return false;
            });


            $( ".remove" ).live( 'click', function() {
                $( this ).parent().remove();
            });

          });

      </script>
      </div> <!-- #meta_inner -->
  <?php
  }



  /**
  *  Save Custom Fields
  *  Save custom fields for Posttype.
  *  @param string $posttype
  */
  public function saveCustomFields( $posttype_name )
  {
      global $post;

      $fields = $this->allfields;

      // deny WordPress autosave function
      if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

      // verify nonce
      if( !isset($_POST['custom_post_type_nonce']) || !wp_verify_nonce( $_POST['custom_post_type_nonce'], basename(__FILE__) ) ) return;

      // check permissions
      if('page' == $_POST['custom_post_type_nonce'] && ( !current_user_can('edit_page', $post->ID ) || !current_user_can('edit_post', $post->ID  )  ) ) return;


      // save custom fields
      if( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == $posttype_name )
      {
        if( $fields  && ! empty( $fields ) )
        {
          foreach( $fields as $field )
          {
            // non-upload fields
            if( isset( $_POST[$field['id']]) )
            {
                // sanitize fields
                switch( $field['type'] )
                {
                  case 'editor':
                    $new = htmlspecialchars( $_POST[ $field['id'] ] );
                    break;

                  case 'text':
                    $new = sanitize_text_field( $_POST[ $field['id'] ] );
                    break;

                  case 'textarea':
                    $new = sanitize_textarea_field( $_POST[ $field['id'] ] );
                    break;

                  default:
                    $new = $_POST[ $field['id'] ];
                    break;
                }
                update_post_meta( $post->ID, $field['id'],  $new );
            }


            // upload fields
            // if( isset( $_FILES[$field['id']] ))
            // {
            //
            //   // sanitize file name
            //   $_FILES[ $field['id'] ]['name'] = sanitize_file_name( $_FILES[ $field['id'] ]['name'] );
            //
            //
            //  $upload = wp_upload_bits( $_FILES[ $field['id'] ]['name'], null, @file_get_contents( $_FILES[ $field['id'] ]['tmp_name'] ) );
             // if( isset( $upload['error'] ) && $upload['error'] != 0 )
             // {
             //    wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
             // } else {
             //   update_post_meta( $post->ID, $field['id'], $_FILES[$field['id']] );
             // }
            // }


            // check if we are trying to uploaded a file
            if (!empty($_FILES[ $field['id'] ]) && $_FILES[ $field['id'] ]['error'] == UPLOAD_ERR_OK)
            {
                // create custom upload dir
                $upload_dir    = wp_upload_dir();
                $upload_subdir = trailingslashit( $upload_dir['basedir'] ) . 'soundlush_uploads/postmeta';
                 wp_mkdir_p( $upload_subdir );

                // make sure we're dealing with an upload
                if (is_uploaded_file($_FILES[ $field['id'] ]['tmp_name']) === false){
                    throw new \Exception('Error on upload: Invalid file definition');
                }

                // rename file
                $uploadName = sanitize_file_name( $_FILES[ $field['id'] ]['name'] );
                //$ext        = strtolower( substr( $uploadName, strripos( $uploadName, '.' ) +1 ) );
                //$filename   = round( microtime( true ) ) . mt_rand() . '.' . $ext;

                $filename   = round( microtime( true ) ) . '_' . $uploadName;
                $_FILES[ $field['id'] ]['name'] = $filename;

                // upload file
                $source      = $_FILES[ $field['id']]['tmp_name'];
                $destination = trailingslashit( $upload_subdir ).$filename;
                $upload      = move_uploaded_file( $source, $destination);

                // insert file meta into database
                if($upload){
                  update_post_meta( $post->ID, $field['id'], $_FILES[$field['id']] );
                }
            }

          }
        }
      }

  }

}
}
