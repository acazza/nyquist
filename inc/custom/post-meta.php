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
      $box_context    = isset( $metabox['context'] ) ? $metabox['context']  : 'normal';
      $box_priority   = isset( $metabox['priority']) ? $metabox['priority'] : 'default';
      $fields         = $metabox['fields'];


      // register metabox
      add_action( 'add_meta_boxes', function() use( $box_id, $box_title, $box_context, $box_priority, $fields )
      {
        add_meta_box(
          $box_id,
          $box_title,
          array( &$this, 'displayMetabox' ),
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

    // get the saved meta as an array
    $postmeta = get_post_meta( $post->ID, 'static_fields', false );

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
      $meta        = isset( $postmeta[0][ $field['id'] ] ) ? $postmeta[0][ $field['id'] ] : $standard ;


      switch ( $type )
      {
        case 'text':

            $html = '<tr><th scope="row"><label for="static_fields_' . $id . '">' . $name . ': </label></th><td><input type="text" class="widefat" name="static_fields[' . $id . ']" id="static_fields_' . $id . '" value="' . $meta  . '"' . $required . ' />' . $description . '</td></tr>';
            break;

        case 'number':

            $min  = isset( $field['min'] ) ? ' min="' . $field['min'] . '" ' : '';
            $max  = isset( $field['max'] ) ? ' max="' . $field['max'] . '" ' : '';
            $step = isset( $field['step'] ) ? ' step="' . $field['step'] . '" ' : '';

            $html = '<tr><th scope="row"><label for="static_fields_' . $id . '">' . $name . ': </label></th><td><input type="number" name="static_fields[' . $id . ']" id="static_fields_' . $id . '" value="' . $meta . '"' . $required . $min . $max . $step . ' /></br>' . $description . '</td></tr>';
            break;

        case 'audio':

            $html = '<tr><th scope="row"><label for="static_fields_' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="static_fields[' . $id . ']" id="static_fields_' . $id . '" value="' . $meta  . '"' . $required . 'accept=".mp3, .wav, .ogg" />' . $description . '</td></tr>';
            break;

        case 'image':

            $html = '<tr><th scope="row"><label for="static_fields_' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="static_fields[' . $id . ']" id="static_fields_' . $id . '" value="' . $meta  . '"' . $required . 'accept=".jpg, .jpeg, .png, .gif" />' . $description . '</td></tr>';
            break;

        case 'textarea':

            $html = '<tr><th scope="row"><label for="static_fields_' . $id . '">' . $name . ': </label></th><td><textarea class="widefat" name="static_fields[' . $id . ']" id="static_fields_' . $id . '" cols="60" rows="4" style="width:96%"' . $required . ' >' . $meta . '</textarea>'. $description . '</td></tr>';
            break;

        case 'editor':

            $settings = array( //TODO all fields here
              'wpautop'       => false,
              'media_buttons' => false,
              'textarea_name' => 'static_fields[' . $id . ']',
            );
            $editor_id = 'static_fields_' . $id;

            //create buffer & echo the editor to the buffer
            ob_start();
            wp_editor( htmlspecialchars_decode( $meta ), $editor_id, $settings );

            $html = '<tr><th scope="row"><label for="' . $editor_id . '">' . $name . ': </label></th><td>';

            //store the contents of the buffer in the variable
            $html .= ob_get_clean();
            $html .= $description .'</td></tr>';
            break;

        case 'checkbox':

            //WHat to write here!!! fieldset ???
            $html = '<tr><th scope="row"><legend>Click me:</legend></th><td><input type="checkbox" name="static_fields[' . $id . ']" id="static_fields_' . $id . '"' . ( $meta ? ' checked="checked"' : '') . ' /><label for="static_fields_' . $id . '">' . $name . ' </label></br>' . $description . '</td></tr>';
            break;

        case 'select':

            $html = '<tr><th scope="row"><label for="static_fields_' . $id . '" >' . $name . ': </label></th><td><select name="static_fields[' . $id . ']" id="static_fields_' . $id . '" >';
            foreach ( $field['options'] as $option ) {
              $html .= '<option value="' . $option['value'] . '" ' . ( $meta == $option['value'] ? '" selected="selected"' : '' ) . '>' . $option['label'] . '</option>';
            }
            $html .= '</select></br>' . $description . '</td></tr>';
            break;

        case 'radio':

            $html = '<tr><th scope="row"><label>' . $name . ': </label></th><td><ul>';
            foreach ( $field['options'] as $option ) {
              $html .= '<li><input type="radio" name="static_fields[' . $id . ']" id="static_fields_' . $option['value'] . '" value="' . $option['value'] . '"' . ( $meta == $option['value'] ? ' checked="checked"' : '' ) . $required . '/><label for="static_fields_' . $option['value'] . '">' . $option['label'] . '</label></li>';
            }
            $html .= '</ul>' . $description . '</td></tr>';
            break;

        case 'relation':

            $posttype  = ( isset( $field['posttype'] ) && post_type_exists( $field['posttype'] ) ) ? $field['posttype'] : '' ;

            $items = query_posts(array('post_type' => $posttype) );

            $html = '<tr><th scope="row"><label for="static_fields_' . $id . '" >' . $name . ': </label></th><td><select name="static_fields[' . $id . ']" id="static_fields_' . $id . '" >';
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
  }



  /**
  *  Add Repeater
  *  Register repeater custom field metabox for Posttype.
  *  @param array  $metabox
  */
  public function addRepeater( $metabox )
  {
      // test if there are metabox arguments
      if( empty( $metabox ) ) return;

      // metabox variables
      $box_id         = SoundlushHelpers::uglify( $metabox['id'] );
      $box_title      = SoundlushHelpers::beautify( $metabox['title'] );
      $box_context    = isset( $metabox['context'] ) ? $metabox['context']  : 'normal';
      $box_priority   = isset( $metabox['priority']) ? $metabox['priority'] : 'default';
      $fields         = $metabox['fields'];


      // register metabox
      add_action( 'add_meta_boxes', function() use( $box_id, $box_title, $box_context, $box_priority, $fields )
      {
        add_meta_box(
          $box_id,
          $box_title,
          array( &$this, 'displayRepeaterMetabox' ),
          $this->posttype_name,
          $box_context,
          $box_priority,
          $fields
        );
      }, 10, 1 );
  }



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
      $postmeta2 = get_post_meta( $post->ID, 'dynamic_attach', false );
      var_dump($postmeta2);

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

                    echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><input type="text" class="widefat" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" value="' . $meta  . '"' . $required . ' />' . $description . '</td></tr>';
                    break;

                  case 'number':

                    $min  = isset( $field['min'] ) ? ' min="' . $field['min'] . '" ' : '';
                    $max  = isset( $field['max'] ) ? ' max="' . $field['max'] . '" ' : '';
                    $step = isset( $field['step'] ) ? ' step="' . $field['step'] . '" ' : '';

                    echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><input type="number" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" value="' . $meta . '"' . $required . $min . $max . $step . ' /></br>' . $description . '</td></tr>';
                    break;

                  case 'audio':

                    echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" value="' . $meta  . '"' . $required . ' accept=".mp3, .wav, .ogg" />' . $description . '</td></tr>';
                    break;

                  case 'image':

                    // echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><img href="' . $meta . '"/><input type="file" class="widefat" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" value="' . $meta  . '"' . $required . 'accept=".jpg, .jpeg, .png, .gif" />' . $description . '</td></tr>';

                    echo '<tr><th scope="row"><label for="dynamic_attach_' . $c . '_' . $id . '">' . $name . ': </label></th><td>' .  $meta . '<input type="file" class="widefat" name="dynamic_attach[' . $c . '][' . $id . ']" id="dynamic_attach_' . $c . '_' . $id . '" value="' . $meta  . '"' . $required . ' accept=".jpg, .jpeg, .png, .gif" />' . $description . '</td></tr>';
                    break;

                  case 'textarea':

                    echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><textarea class="widefat" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" cols="60" rows="4" style="width:96%"' . $required . ' >' . $meta . '</textarea>'. $description . '</td></tr>';
                    break;


                  case 'checkbox':

                    echo '<tr><th scope="row"><legend>Click me:</legend></th><td><input type="checkbox" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '"' . ( $meta ? ' checked="checked"' : '') . ' /><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ' </label></br>' . $description . '</td></tr>';
                    break;

                  case 'select':

                    echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '" >' . $name . ': </label></th><td><select name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" >';
                    foreach ( $field['options'] as $option ) {
                      echo '<option value="' . $option['value'] . '" ' . ( $meta == $option['value'] ? '" selected="selected"' : '' ) . '>' . $option['label'] . '</option>';
                    }
                    echo '</select></br>' . $description . '</td></tr>';
                    break;

                  case 'radio':

                    echo '<tr><th scope="row"><label>' . $name . ': </label></th><td><ul>';
                    foreach ( $field['options'] as $option ) {
                      echo '<li><input type="radio" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $option['value'] . '" value="' . $option['value'] . '"' . ( $meta == $option['value'] ? ' checked="checked"' : '' ) . $required . '/><label for="dynamic_fields_' . $c . '_' . $option['value'] . '">' . $option['label'] . '</label></li>';
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

              $output .=  '<tr><th scope="row"><label for="dynamic_fields_count_variable_' . $id . '">' . $name . ': </label></th><td><input type="text" class="widefat" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '"' . $required . ' />' . $description . '</td></tr>';
              break;

            case 'number':

              $min  = isset( $field['min'] ) ? ' min="' . $field['min'] . '" ' : '';
              $max  = isset( $field['max'] ) ? ' max="' . $field['max'] . '" ' : '';
              $step = isset( $field['step'] ) ? ' step="' . $field['step'] . '" ' : '';

              $output .= '<tr><th scope="row"><label for="dynamic_fields_count_variable_' . $id . '">' . $name . ': </label></th><td><input type="number" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '"' . $required . $min . $max . $step . ' /></br>' . $description . '</td></tr>';
              break;

            case 'audio':

              $output .= '<tr><th scope="row"><label for="dynamic_fields_count_variable_' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '"' . $required . 'accept=".mp3, .wav, .ogg" />' . $description . '</td></tr>';
              break;

            case 'image':

              $output .= '<tr><th scope="row"><label for="dynamic_attach_count_variable_' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="dynamic_attach[count_variable][' . $id . ']" id="dynamic_attach_count_variable_' . $id . '"' . $required . 'accept=".jpg, .jpeg, .png, .gif" />' . $description . '</td></tr>';
              break;

            case 'textarea':

              $output .= '<tr><th scope="row"><label for="dynamic_fields_count_variable_' . $id . '">' . $name . ': </label></th><td><textarea class="widefat" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '" cols="60" rows="4" style="width:96%"' . $required . ' ></textarea>'. $description . '</td></tr>';
              break;

            case 'checkbox':

              $output .= '<tr><th scope="row"><legend>Click me:</legend></th><td><input type="checkbox" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '" /><label for="dynamic_fields_count_variable_' . $id . '">' . $name . ' </label></br>' . $description . '</td></tr>';
              break;

            case 'select':

              $output .= '<tr><th scope="row"><label for="dynamic_fields_count_variable_' . $id . '" >' . $name . ': </label></th><td><select name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '" >';
              foreach ( $field['options'] as $option ) {
                $output .= '<option value="' . $option['value'] . '">' . $option['label'] . '</option>';
              }
              $output .= '</select></br>' . $description . '</td></tr>';
              break;

            case 'radio':

              $output .= '<tr><th scope="row"><label>' . $name . ': </label></th><td><ul>';
              foreach ( $field['options'] as $option ) {
                $output .= '<li><input type="radio" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $option['value'] . '" value="' . $option['value'] . '"' . $required . '/><label for="dynamic_fields_count_variable_' . $option['value'] . '">' . $option['label'] . '</label></li>';
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
                //substitute placeholder index by the count variable
                var res = output.replace(/count_variable/g, count);

                $('#here').append( '<div class="repeater"><table class="form-table">' + res + '</table><button class="remove button-secondary">Remove Answer</button></div>' );

                return false;
            //}
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

      //$fields = $this->fields;

      // deny WordPress autosave function
      if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

      // verify nonce
      if( !isset($_POST['custom_post_type_nonce']) || !wp_verify_nonce( $_POST['custom_post_type_nonce'], basename(__FILE__) ) ) return;

      // check permissions
      if('page' == $_POST['custom_post_type_nonce'] && ( !current_user_can('edit_page', $post->ID ) || !current_user_can('edit_post', $post->ID  )  ) ) return;

      // save custom fields
      if( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == $posttype_name )
      {
          // TODO sanitize inputs
          update_post_meta( $post->ID, 'static_fields', $_POST['static_fields']);
          update_post_meta( $post->ID, 'dynamic_fields', $_POST['dynamic_fields']);
      }

      // make sure the file array isn't empty
      if( ! empty( $_FILES['dynamic_attach'] ) )
      {
        //$arr_file_type = wp_check_filetype( basename( $_FILES['dynamic_attach'] ) );

        // use the WordPress API to upload the file
        $upload[] = wp_upload_bits( $_FILES['dynamic_attach'], null, file_get_contents( $_FILES['dynamic_attach'] ) );

        if( ( isset( $upload['error'] ) && $upload['error'] != 0 ) )
        {
            wp_die( 'There was an error uploading your file. The error is: ' . $upload['error'] );
        }
        else
        {
            update_post_meta( $post->ID, 'dynamic_attach', $upload );
        }

      }
  }


}
}
