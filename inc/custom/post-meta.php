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

  /**
   * Saving locations
   * @var string
   */
  public static $upload_dir;
  public static $upload_subdir;
  public static $upload_url;



  /**
  *  Construct
  *  Initial code upon object creation.
  *  @param string $posttype_name
  */
  public function __construct($posttype_name)
  {
    $this->posttype_name = $posttype_name;

    self::$upload_dir    = wp_upload_dir();
    self::$upload_subdir = trailingslashit( self::$upload_dir['basedir'] ) . 'soundlush_uploads/postmeta/';
    self::$upload_url    = trailingslashit( self::$upload_dir['baseurl'] ) . 'soundlush_uploads/postmeta/';
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


      $defaults = array(
          'context' => 'normal',
          'priority'=> 'default',
          'repeater'=>  false
      );

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
      $postmeta = get_post_meta( $post->ID, SoundlushHelpers::uglify( $field['id']), true );
      $prefix = '_';
       echo $this->create_html_markup( $field, $postmeta );
    }

    echo '</table>';

    //var_dump($this->allfields);

  }


  /**
   *  Create Posttype Custom Fields Markup
   *  Outputs html for each custom field.
   *  @param array  $fields
   *  @param mixed  $postmeta
   */

  public function create_html_markup( $args, $postmeta, $prefix='', $suffix='', $pre_id='' )
  {

    $defaults = array(
        'label'       => '',                                    // (string)   all
        'type'        => 'text',                                // (string)   all
        'desc'        => '',                                    // (string)   all
        'std'         => '',                                    // (mixed)    all
        'required'    => false,                                 // (boolean)  all BUT checkbox
        'allow_tags'  => false,                                 // (boolean)  text & textarea
        'min'         => 0,                                     // (int)      number
        'max'         => 10,                                    // (int)      number
        'step'        => 1,                                     // (int)      number
        'posttype'    => '',                                    // (string)   relation
        'options'     => '',                                    // (array)    select & radio
        'accept'      => '.png, .jpg, .jpeg, .wav, .mp3, .ogg'  // (string)   file
    );

    $field = wp_parse_args( $args, $defaults );


    $label       = !empty( $field['label'] ) ? $field['label'] : $field['id'];
    $label       = SoundlushHelpers::beautify( $label );
    $id          = $pre_id . SoundlushHelpers::uglify( $field['id'] );
    $name        = $prefix . SoundlushHelpers::uglify( $field['id'] ) . $suffix;
    $required    = $field['required'] ? ' required' : '';
    $description = !empty( $field['desc'] ) ? '<span class="description">'.$field['desc'].'</span>' : '';


    // check if there is saved metadata for the field, if not use default value
    $meta  = !empty( $postmeta ) ? $postmeta : $field['std'];
    $html  = '';

    switch ( $field['type'] )
    {
      case 'text':

          $html = '<tr><th scope="row"><label for="' . $id . '">' . $label . ': </label></th><td><input type="text" class="widefat" name="' . $name . '" id="' . $id . '" value="' . $meta  . '"' . $required . ' />' . $description . '</td></tr>';
          break;

      case 'number':

          $html = '<tr><th scope="row"><label for="' . $id . '">' . $label . ': </label></th><td><input type="number" name="' . $name . '" id="' . $id . '" value="' . $meta . '"' . $required . ' min="' . $field['min'] . '" max="' . $field['max'] . '" step="' . $field['step'] . '" /></br>' . $description . '</td></tr>';
          break;

      case 'file':

          $filename = isset( $meta['name'] ) ? $meta['name'] : '';
          $filetype = isset( $meta['type'] ) ? $meta['type'] : '';

          if ( strpos( $filetype, 'image') !== false) {
            $preview = '<img src="'. self::$upload_url . $filename . '" width="150" height="150" >';
          }
          elseif ( strpos( $filetype, 'audio') !== false) {
            $preview = '<audio controls> <source src="'. self::$upload_url . $filename . '" type="'. $filetype .' ">Your browser does not support the audio element.</audio>';
          }
          else {
            $preview = '';
          }

          $html = '<tr><th scope="row"><label for="' . $id . '">' . $label . ': </label></th><td>' . $preview . '<p>' . $filename . '</p><input type="file" class="widefat" name="' . $name . '" id="' . $id . '" value="' . $filename  . '"' . $required . ' accept="' . $field['accept'] . '" multiple="false"/>' . $description . '</td></tr>';
          break;

      case 'textarea':

          $html = '<tr><th scope="row"><label for="' . $id . '">' . $label . ': </label></th><td><textarea class="widefat" name="' . $name . '" id="' . $id . '" cols="60" rows="4" style="width:96%"' . $required . ' >' . $meta . '</textarea>'. $description . '</td></tr>';
          break;

      case 'editor':

          $settings = array(
            'wpautop'          => false,
            'media_buttons'    => false,
            'textarea_name'    => $name,
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

          $html = '<tr><th scope="row"><label for="' . $id . '">' . $label . ': </label></th><td>';
          $html .= ob_get_clean(); //store the contents of the buffer in the variable
          $html .= $description .'</td></tr>';
          break;

      case 'checkbox':

          //TODO fieldset ???
          $html = '<tr><th scope="row"><legend>Click me:</legend></th><td><input type="checkbox" name="' . $name . '" id="' . $id . '"' . ( $meta ? ' checked="checked"' : '') . ' /><label for="' . $id . '">' . $label . ' </label></br>' . $description . '</td></tr>';
          break;

      case 'select':

          $html = '<tr><th scope="row"><label for="' . $id . '" >' . $label . ': </label></th><td><select name="' . $name . '" id="' . $id . '" >';
          $html .= '<option value="' . $field['std'] . '"' . ( $meta == $field['std'] ? 'selected="selected"' : '' ) . '>-- Select an option --</option>';
          foreach ( $field['options'] as $option ) {
            $html .= '<option value="' . $option['value'] . '"' . ( $meta == $option['value'] ? ' selected="selected"' : '' ) . '>' . $option['label'] . '</option>';
          }
          $html .= '</select></br>' . $description . '</td></tr>';
          break;

      case 'radio':

          $html = '<tr><th scope="row"><label>' . $label . ': </label></th><td><ul>';
          foreach ( $field['options'] as $option ) {
            $html .= '<li><input type="radio" name="' . $name . '" id="' . $pre_id . $option['value'] . '" value="' . $option['value'] . '"' . ( $meta == $option['value'] ? ' checked="checked"' : '' ) . $required . '/><label for="' . $pre_id . $option['value'] . '">' . $option['label'] . '</label></li>';
          }
          $html .= '</ul>' . $description . '</td></tr>';
          break;

      case 'relation':

          $posttype = post_type_exists( $field['posttype'] ) ? $field['posttype'] : '' ;
          $items    = query_posts(array('post_type' => $posttype, 'post_status' => 'publish') );

          $html = '<tr><th scope="row"><label for="' . $id . '" >' . $label . ': </label></th><td><select name="' . $name . '" id="' . $id . '" >';
          $html .= '<option value="' . $field['std'] . '"' . ( $meta == $field['std'] ? ' selected="selected"' : '') . '>-- Select an option --</option>';
          foreach ( $items as $item ) {
            $html .= '<option value="' . $item->ID . '" ' . ( $meta == $item->ID ? '" selected="selected"' : '' ) . '>' . $item->post_title . '</option>';
          }
          $html .= '</select></br>' . $description . '</td></tr>';
          break;

      default:
          break;
    }

    return $html;

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

      // get the saved meta as an array
      $postmeta = get_post_meta( $post->ID, '_repeater', false );

      // repeater index counter
      $c = 0;

      //if there is saved postdata for the post
      if( is_array( $postmeta ) && ( ! empty( $postmeta ) && isset( $postmeta ) ) )
      {
          foreach( $postmeta[0] as $key => $value )
          {
            echo '<div class="repeater" style="padding: 0 1em 2em; border-bottom: 1px solid #ccc ">';
            echo '<table class="form-table">';

            foreach( $fields as $field )
            {
              $prefix = '_repeater['.$c .'][';
              $suffix = ']';
              $pre_id = 'repeater_'.$c .'_';
              $data   = $value[ $field['id'] ];

              echo $this->create_html_markup( $field, $data, $prefix, $suffix, $pre_id );
            }

            $c = $c +1;

            echo '</table>';
            echo '<button class="remove button-secondary">' .  __( 'Remove Item' ) .  '</button>';
            echo '</div>';
          }

      }


      $output = '';

      //Add new blank set of fields
      foreach( $fields as $field )
      {

        $prefix = '_repeater[count_variable][';
        $suffix = ']';
        $pre_id = 'repeater_count_variable';
        $data   = '';

        $output .= $this->create_html_markup( $field, $data, $prefix, $suffix, $pre_id );

      }

      ?>
      <span id="here" style="display: block;"></span>
      <button class="add button-primary" style="margin: 2em 0;"><?php _e( 'Add Item' ); ?></button>


      <?php $this->add_script( $c, $output ); ?>

      </div> <!-- #meta_inner -->
  <?php
  }



  /**
   * Add Script
   * Includes javascript in code
   * @param integer $c
   * @param string  $output
   */
  public function add_script($c, $output)
  { ?>
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

        // PROVISORIO
        if( isset( $_POST['_repeater'] ) ){
          update_post_meta( $post->ID, _repeater,  $_POST['_repeater']  );
        }

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


            // check if we are trying to uploaded a file
            if (!empty($_FILES[ $field['id'] ]) && $_FILES[ $field['id'] ]['error'] == UPLOAD_ERR_OK)
            {
                // create custom upload dir
                 wp_mkdir_p( self::$upload_subdir );

                // make sure we're dealing with an upload
                if (is_uploaded_file($_FILES[ $field['id'] ]['tmp_name']) === false){
                    throw new \Exception('Error on upload: Invalid file definition');
                }

                // rename file
                $uploadName = sanitize_file_name( $_FILES[ $field['id'] ]['name'] );
                $filename   = round( microtime( true ) ) . '_' . $uploadName;
                $_FILES[ $field['id'] ]['name'] = $filename;

                // upload file
                $source      = $_FILES[ $field['id']]['tmp_name'];
                $destination = self::$upload_subdir .$filename;
                $upload      = move_uploaded_file( $source, $destination);

                // insert file meta into database
                if($upload) update_post_meta( $post->ID, $field['id'], $_FILES[$field['id']] );

            }

          }
        }
      }

  }

}
}
