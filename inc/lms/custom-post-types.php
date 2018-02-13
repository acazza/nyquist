<?php
/**
 * Soundlush Custom Post Types/Taxonomy Class
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 * @package soundlush
 */

if( !class_exists( 'SoundlushCustomPostType' ) )
{

  class SoundlushCustomPostType
  {

    public $post_type_name;
    public $post_type_args;
    public $post_type_labels;
    public $taxonomy_name;


    /**
     * Class constructor
     * @param
     */

    public function __construct( $name, $args = array(), $labels = array() )
    {

      //set some important variables
      $this->post_type_name    = SoundlushHelpers::uglify( $name );
      $this->post_type_args    = $args;
      $this->post_type_labels  = $labels;

      //add action to register the post type, if the post type does not already exist
      if( ! post_type_exists( $this->post_type_name ) )
      {
        add_action( 'init', array( &$this, 'register_post_type' ) );
      }

      SoundlushHelpers::activate();

      //listen for the save post hook
      $this->save();
    }



    /**
     * Method which registers the post type
     * @param
     */

    public function register_post_type()
    {
      //capitilize the words and make it plural
      $name       = SoundlushHelpers::beautify( $this->post_type_name );
      $plural     = SoundlushHelpers::pluralize( $name );
      $textdomain = SoundlushHelpers::get_textdomain();

      //we set the default labels based on the post type name and plural. We overwrite them with the given labels.
      $labels = array_merge(
        array(
            'name'                  => _x( $plural, 'post type general name' ),
            'singular_name'         => _x( $name, 'post type singular name' ),
            'add_new'               => _x( 'Add New', strtolower( $name ) ),
            'add_new_item'          => __( 'Add New ' . $name ),
            'edit_item'             => __( 'Edit ' . $name ),
            'new_item'              => __( 'New ' . $name ),
            'all_items'             => __( 'All ' . $plural ),
            'view_item'             => __( 'View ' . $name ),
            'search_items'          => __( 'Search ' . $plural ),
            'not_found'             => __( 'No ' . strtolower( $plural ) . ' found'),
            'not_found_in_trash'    => __( 'No ' . strtolower( $plural ) . ' found in Trash'),
            'parent_item_colon'     => '',
            //'parent_item_colon'     => __( 'Parent ' . $plural . ':' ),
            // 'archives'              => _x( $name . ' archives' ),
            // 'name_admin_bar'        => _x( $name, 'Add New on Toolbar' ),
            // 'menu_name'             => _x( $plural ),
            // 'items_list_navigation' => _x( $plural . ' list navigation' ),
            // 'items_list'            => _x( $plural . ' list' ),
            // 'filter_items_list'     => _x( 'Filter ' . $plural . ' list' ),
            // 'insert_into_item'      => _x( 'Insert into ' . $name ),
            // 'uploaded_to_this_item' => _x( 'Uploaded to this ' . $name  ),
        ),

        $this->post_type_labels

      );

      //same principle as the labels. we set some defaults and overwrite them with the given arguments.
      $args = array_merge(
        array(
            'label'                 => $plural,
            'labels'                => $labels,
            'public'                => true,
            'show_ui'               => true,
            'supports'              => array( 'title', 'editor' ),
            'show_in_nav_menus'     => true,
            '_builtin'              => false,
        ),

        $this->post_type_args

      );

      //register the post type
      register_post_type( $this->post_type_name, $args );

      //update post type messages
      add_filter( 'post_updated_messages', array( &$this, 'updated_messages' ) );

    }




    /**
     * Callback for WordPress 'post_updated_messages' filter.
     *
     * @param  array $messages The array of messages.
     * @return array $messages The array of messages.
     */

    public function updated_messages( $messages ){

      global $post;

      $post_ID = $post->ID;

      $post_type_name = $this->post_type_name;
      $name           = SoundlushHelpers::beautify( $post_type_name );
      $textdomain     = SoundlushHelpers::get_textdomain();

      $messages[$post_type_name] = array(
          0 => '', //unused. messages start at index 1.
          1 => sprintf( __( $name . ' updated. <a href="%s">View ' . $name . '</a>'), esc_url( get_permalink($post_ID) ) ),
          2 => __( $name . ' field updated.' ),
          3 => __( $name . ' field deleted.' ),
          4 => __( $name . ' updated.' ),
          5 => isset($_GET['revision']) ? sprintf( __( $name . ' restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
          6 => sprintf( __( $name . ' published. <a href="%s">View run</a>' ), esc_url( get_permalink( $post_ID ) ) ),
          7 => __( $name . ' saved.' ),
          8 => sprintf( __( $name . ' submitted. <a target="_blank" href="%s">Preview run</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
          9 => sprintf( __( $name . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview run</a>' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
          10 => sprintf( __( $name . ' draft updated. <a target="_blank" href="%s">Preview post type</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
      );

      return $messages;
    }




    /**
     * Method to attach the taxonomy to the post type
     * @param $type: check, radio, select
     */

    public function add_taxonomy( $name, $type = 'check', $args = array(), $labels = array() )
    {

      if( ! empty( $name ) )
      {
        //get post type name, so new taxonomy can be attached to it.
        $post_type_name = $this->post_type_name;

        //taxonomy properties
        $taxonomy_name      = SoundlushHelpers::uglify( $name );
        $taxonomy_labels    = $labels;
        $taxonomy_args      = $args;
      }

      if( ! taxonomy_exists( $taxonomy_name ) )
      {

        //capitilize the words and make it plural
        $name       = SoundlushHelpers::beautify( $name );
        $plural     = SoundlushHelpers::pluralize( $name );
        $textdomain = SoundlushHelpers::get_textdomain();

        //default labels, overwrite them with the given labels.
        $labels = array_merge(
          array(
              'name'                  => _x( $plural, 'taxonomy general name' ),
              'singular_name'         => _x( $name, 'taxonomy singular name' ),
              'search_items'          => __( 'Search ' . $plural ),
              'all_items'             => __( 'All ' . $plural ),
              'parent_item'           => __( 'Parent ' . $name ),
              'parent_item_colon'     => __( 'Parent ' . $name . ':' ),
              'edit_item'             => __( 'Edit ' . $name ),
              'update_item'           => __( 'Update ' . $name ),
              'add_new_item'          => __( 'Add New ' . $name ),
              'new_item_name'         => __( 'New ' . $name . ' Name' ),
              'menu_name'             => __( $name ),
          ),

          $taxonomy_labels
        );

        //default arguments, overwritten with the given arguments
        $args = array_merge(
          array(
              'label'                 => $plural,
              'labels'                => $labels,
              'public'                => true,
              'show_ui'               => true,
              'show_in_nav_menus'     => true,
              '_builtin'              => false,
          ),

          $taxonomy_args

        );

        //add new taxonomy to the object type (post type)
        add_action( 'init',
          function() use( $taxonomy_name, $post_type_name, $args )
          {
            register_taxonomy( $taxonomy_name, $post_type_name, $args );
          }
        );
      }
      else
      {
        //taxonomy already exists. Attach the existing taxonomy to the object type (post type)
        add_action( 'init',
          function() use( $taxonomy_name, $post_type_name )
          {
            register_taxonomy_for_object_type( $taxonomy_name, $post_type_name );
          }
        );
      }

      //generate custom metabox input type
      if ($type != 'check' ) $this->setup_custom_metabox($taxonomy_name, $post_type_name, $type);
    }



    /**
     * Generate custom metabox input type
     *
     * @param $taxonomy_name
     * @param $post_type_name
     * @param $type ( radio or select )
     */

    public function setup_custom_metabox($taxonomy_name, $post_type_name, $type ){

      //remove taxonomy meta box
      add_action( 'admin_menu',
        function() use( $taxonomy_name, $post_type_name )
        {
          $tax_mb_id = $taxonomy_name.'div';
          remove_meta_box($tax_mb_id, $post_type_name, 'normal');
        }
      );

      //add custom meta box
      add_action( 'add_meta_boxes',
        function() use( $taxonomy_name, $post_type_name, $type )
        {
          $name = SoundlushHelpers::beautify( $taxonomy_name );
          add_meta_box( 'mytaxonomy_id', $name,
            function() use( $taxonomy_name, $type )
            {
              global $post;

              //set up the taxonomy object and get terms
              $taxonomy_name = $taxonomy_name;
              $tax = get_taxonomy($taxonomy_name);
              $terms = get_terms($taxonomy_name, array('hide_empty' => 0));

              //name of the form
              $name = 'tax_input[' . $taxonomy_name . ']';

              $postterms = get_the_terms( $post->ID, $taxonomy_name );
              $current = ($postterms ? array_pop($postterms) : false);
              $current = ($current ? $current->term_id : 0);

              //check taxonomy input type and display taxonomy terms
              switch( $type ){
                case 'radio': ?>
                  <div id="taxonomy-<?php echo $taxonomy_name; ?>" class="categorydiv">
                    <div id="<?php echo $taxonomy_name; ?>-all" class="tabs-panel">
                      <ul id="<?php echo $taxonomy_name; ?>checklist" class="list:<?php echo $taxonomy_name?> categorychecklist form-no-clear">
                        <?php   foreach($terms as $term){
                            $id = $taxonomy_name.'-'.$term->term_id;
                            echo "<li id='$id'><label class='selectit'>";
                            echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)."value='$term->term_id' />$term->name<br />";
                            echo "</label></li>";
                        }?>
                      </ul>
                    </div>
                  </div>
                  <?php
                  break;

                  case 'select': ?>
                    <!-- Display taxonomy terms -->
                    <div id="taxonomy-<?php echo $taxonomy_name; ?>" class="categorydiv">
                      <div id="<?php echo $taxonomy_name; ?>-all" class="tabs-panel">
                      <?php // TODO ?>
                      </div>
                    </div>
                    <?php
                    break;
              }
            }
            ,$post_type_name ,'side','core');
        }
      );
    }



    /**
     * Attaches static custom field meta boxes to the post type
     * @param $custom_array
     */

    public function add_custom_fields( $custom_array )
    {
      if( ! empty( $custom_array ) )
      {
        //get post type name
        $post_type_name = $this->post_type_name;

        global $fields;

        //meta variables
        $box_id         = SoundlushHelpers::uglify( $custom_array['id'] ); //TODO FALLBACK
        $box_title      = SoundlushHelpers::beautify( $custom_array['title'] ); //TODO FALLBACK
        $box_context    = $custom_array['context'] ? $custom_array['context'] : 'normal';
        $box_priority   = $custom_array['priority'] ? $custom_array['priority'] : 'default';
        $fields         = $custom_array['fields'];
      }

      add_action( 'admin_init',
        function() use( $box_id, $box_title, $post_type_name, $box_context, $box_priority, $fields )
        {
          add_meta_box(
            $box_id,
            $box_title,
            function( $post, $data ) use( $fields )
            {
              global $post;

              //create nonce field
              wp_nonce_field( basename( __FILE__ ), 'custom_post_type_nonce' );
              //get the saved meta as an array
              $postmeta = get_post_meta( $post->ID, 'static_fields', false );

              echo '<table class="form-table">';

              //loop through fields
              foreach( $fields as $field )
              {
                $html        = '';
                $id          = SoundlushHelpers::uglify( $field['id'] );
                $name        = SoundlushHelpers::beautify( $field['name'] );
                $type        = isset( $field['type'] ) ? $field['type'] : 'text'; //default = text
                $required    = ( isset( $field['required'] ) && $field['required'] ) ? ' required' : ''; //default: false
                $description = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : ''; //Optional
                $standard    = isset( $field['std'] ) ? $field['std'] : ''; //Optional

                //check if there is saved metadata for the field, if not use default value
                $meta        = isset( $postmeta[0][ $field['id'] ] ) ? $postmeta[0][ $field['id'] ] : $standard ;

                switch ( $type )
                {
                  case 'text':
                    $html = '<tr><th scope="row"><label for="static_fields_' . $id . '">' . $name . ': </label></th><td><input type="text" class="widefat" name="static_fields[' . $id . ']" id="static_fields_' . $id . '" value="' . $meta  . '"' . $required . ' />' . $description . '</td></tr>';
                    break;

                  case 'number':
                    $min  = isset( $field['min'] ) ? ' min="' . $field['min'] . '" ' : '';
                    $max  = isset( $field['max'] ) ? ' max="' . $field['max'] . '" ' : '';
                    $step = isset( $field['step'] ) ? ' step="'. $field['step'] . '" ' : '';

                    $html = '<tr><th scope="row"><label for="static_fields_' . $id . '">' . $name . ': </label></th><td><input type="number" name="static_fields[' . $id . ']" id="static_fields_' . $id . '" value="' . $meta . '"' . $required . $min . $max . $step . ' /></br>' . $description . '</td></tr>';
                    break;

                  case 'textarea':
                    $html = '<tr><th scope="row"><label for="static_fields_' . $id . '">' . $name . ': </label></th><td><textarea class="widefat" name="static_fields[' . $id . ']" id="static_fields_' . $id . '" cols="60" rows="4" style="width:96%"' . $required . ' >' . $meta . '</textarea>'. $description . '</td></tr>';
                    break;

                  case 'editor':
                    $settings = array(
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

                  default:
                    break;
                }
                echo $html;
              }
              echo '</table>';
             },
             $post_type_name,
             $box_context,
             $box_priority,
             array( $fields )
           );
         }
       );
     }



    /**
     * Attaches dynamic custom field meta boxes to the post type
     * @param $custom_array
     */

    function add_dynamic_custom_fields( $custom_array )
    {
      if( ! empty( $custom_array ) )
      {
        //get post type name
        $post_type_name = $this->post_type_name;

        global $fields;

        //meta variables
        $box_id         = SoundlushHelpers::uglify( $custom_array['id'] ); //TODO FALLBACK
        $box_title      = SoundlushHelpers::beautify( $custom_array['title'] ); //TODO FALLBACK
        $box_context    = $custom_array['context'] ? $custom_array['context'] : 'normal';
        $box_priority   = $custom_array['priority'] ? $custom_array['priority'] : 'default';
        $fields         = $custom_array['fields'];
      }

      add_action( 'add_meta_boxes',
        function() use( $box_id, $box_title, $post_type_name, $box_context, $box_priority, $fields )
        {
          add_meta_box(
            $box_id,
            $box_title,
            function( $post, $data ) use( $fields )
            {
              global $post;

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
                      echo '<div class="repeater">';

                      $output = '';

                      foreach( $fields as $field )
                      {
                        switch ( $field['type'] )
                        {
                          case 'text':
                            echo '<label for="dynamic_fields_' . $c . '_' . $field['id'] . '" >' . $field['name'] . ': </label>';
                            echo '<input type="text" name="dynamic_fields[' . $c . '][' . $field['id'] . ']" id="dynamic_fields_' . $c . '_' . $field['id'] . '" value="' . $answer[ $field['id'] ] . '" />';

                            $output .= '<label for="dynamic_fields_count_variable_' . $field['id'] . '" >' . $field['name'] . ': </label><input type="text" name="dynamic_fields[count_variable][' . $field['id'] . ']" id="dynamic_fields_count_variable_' . $field['id'] . '"/>';
                            break;

                          case 'textarea':
                            echo '<label for="dynamic_fields_' . $c . '_' . $field['id'] . '" >' . $field['name'] . ': </label>';
                            echo '<textarea name="dynamic_fields[' . $c . '][' . $field['id'] . ']" id="dynamic_fields_' . $c . '_' . $field['id'] . '" cols="30" rows="4" style="width:50%" >' . $answer[ $field['id'] ] . '</textarea>';
                            //echo '<p class="meta-desc">' . $field['desc'] . '</p>';

                            $output .= '<label for="dynamic_fields_count_variable_' . $field['id'] . '" >' . $field['name'] . ': </label><textarea name="dynamic_fields[count_variable][' . $field['id'] . ']" id="dynamic_fields_count_variable_' . $field['id'] . '" cols="30" rows="4" style="width:50%" ></textarea>'; //<p class="meta-desc">' . $field['desc'] . '</p>
                            break;


                          case 'checkbox':
                            echo '<input type="checkbox" name="dynamic_fields[' . $c . '][' . $field['id'] . ']" id="dynamic_fields_' . $c . '_' . $field['id'] . '" ' . (isset($answer[ $field['id'] ]) ? "checked" : '') . '/>';
                            echo '<label for="dynamic_fields_' . $c . '_' . $field['id'] . '" >' . $field['name'] . '</label>';

                            $output .= '<input type="checkbox" name="dynamic_fields[count_variable][' . $field['id'] . ']" id="dynamic_fields_count_variable_' . $field['id'] . '"/><label for="dynamic_fields_count_variable_' . $field['id'] . '" >' . $field['name'] . '</label>';
                            break;

                          case 'select':
                            echo '<label for="dynamic_fields_' . $c . '_' . $field['id'] . '" >' . $field['name'] . ': </label>';
                            echo '<select name="dynamic_fields[' . $c . '][' . $field['id'] . ']" id="dynamic_fields_' . $c . '_' . $field['id'] . '" >';
                            foreach ( $field['options'] as $option ) {
                              echo '<option value="' . $option['value'] . '" ' . ( $answer[ $field['id'] ] == $option['value'] ? '" selected="selected"' : '' ) . '>' . $option['label'] . '</option>';
                            }
                            echo '</select>';

                            $output .= '<label for="dynamic_fields_count_variable_' . $field['id'] . '" >' . $field['name'] . ': </label>';
                            $output .= '<select name="dynamic_fields[count_variable][' . $field['id'] . ']" id="dynamic_fields_count_variable_' . $field['id'] . '" >';
                            foreach ( $field['options'] as $option ) {
                              $output .= '<option value="' . $option['value'] . '">' . $option['label'] . '</option>';
                            }
                            $output .='</select>';
                            break;

                          case 'radio':
                            echo '<ul><label>' . $field['name'] . ': </label>';
                            foreach ( $field['options'] as $option ) {
                              echo '<input type="radio" name="dynamic_fields[' . $c . '][' . $field['id'] . ']" id="dynamic_fields_' . $c . '_' . $option['value'] . '" value="' . $option['value'] . '"';
                              echo ( $answer[ $field['id'] ] == $option['value'] ? 'checked="checked"' : '') . ' />';
                              echo '</li><label for="dynamic_fields_' . $c . '_' . $option['value'] . '">' . $option['label'] . '</label></li>';
                            }
                            echo '</ul>';

                            $output .= '<ul><label>' . $field['name'] . ': </label>';
                            foreach ( $field['options'] as $option ) {
                              $output .= '<input type="radio" name="dynamic_fields[count_variable][' . $field['id'] . ']" id="dynamic_fields_count_variable_' . $option['value'] . '" value="' . $option['value'] . '" />';
                              $output .= '</li><label for="dynamic_fields_count_variable_' . $option['value'] . '">' . $option['label'] . '</label></li>';
                            }
                            $output .= '</ul>';
                            break;

                          default:
                            break;
                        }
                      }
                      $c = $c +1;
                      echo '<button class="remove button-secondary">' .  __( 'Remove Item' ) .  '</button>';
                      echo '</div>';
                    }
                  }
                } else {
                  //if there is NO saved postdata for the post
                  foreach( $fields as $field )
                  {
                    switch ( $field['type'] )
                    {
                      case 'text':
                        $output .= '<label for="dynamic_fields[count_variable][' . $field['id'] . ']" >' . $field['name'] . ': </label><input type="text" name="dynamic_fields[count_variable][' . $field['id'] . ']"/>';
                        break;

                      case 'textarea':
                        $output .= '<label for="dynamic_fields_count_variable_' . $field['id'] . '" >' . $field['name'] . ': </label><textarea name="dynamic_fields[count_variable][' . $field['id'] . ']" id="dynamic_fields_count_variable_' . $field['id'] . '" cols="30" rows="4" style="width:50%" ></textarea>'; //<p class="meta-desc">' . $field['desc'] . '</p>
                        break;

                      case 'checkbox':
                        $output .= '<label for="dynamic_fields[count_variable][' . $field['id'] . ']" ><input type="checkbox" name="dynamic_fields[count_variable][' . $field['id'] . ']"/>' . $field['name'] . '</label>';
                        break;

                      case 'select':
                        $output .= '<label for="dynamic_fields_count_variable_' . $field['id'] . '" >' . $field['name'] . ': </label>';
                        $output .= '<select name="dynamic_fields[count_variable][' . $field['id'] . ']" id="dynamic_fields_count_variable_' . $field['id'] . '" >';
                        foreach ( $field['options'] as $option ) {
                          $output .= '<option value="' . $option['value'] . '">' . $option['label'] . '</option>';
                        }
                        $output .='</select>';
                        break;

                      case 'radio':
                        $output .= '<ul><label>' . $field['name'] . ': </label>';
                        foreach ( $field['options'] as $option ) {
                          $output .= '</li><label for="static_fields_count_variable_' . $option['value'] . '">';
                          $output .= '<input type="radio" name="static_fields[count_variable][' . $field['id'] . ']" id="static_fields_count_variable_' . $option['value'] . '" value="' . $option['value'] . '" />' . $option['label'] . '</label></li>';
                        }
                        $output .= '</ul>';
                        break;

                    }
                  }

                }


              ?>
              <span id="here"></span>
              <button class="add button-primary"><?php _e( 'Add Item' ); ?></button>

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

                        $('#here').append('<div>'+ res +'<button class="remove button-secondary">Remove Answer</button></div>' );

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
              },
              $post_type_name,
              $box_context,
              $box_priority
              //array( $fields )
          );
        }
      );
    }



    /**
     * Listens for when the post type is being saved
     *
     */

    public function save()
    {
      //get post type name
      $post_type_name = $this->post_type_name;

      add_action( 'save_post',
        function() use( $post_type_name )
        {

          global $post;

          //deny WordPress autosave function
          if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return;

          //verify nonce
          if ( !isset($_POST['custom_post_type_nonce']) || !wp_verify_nonce( $_POST['custom_post_type_nonce'], basename(__FILE__) ) ) return;

          //check permissions
          if ('page' == $_POST['custom_post_type_nonce'])
          {
            if ( !current_user_can('edit_page', $post->ID ) || !current_user_can('edit_post', $post->ID  ) ) return;
          }

          //save custom fields
          if( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == $post_type_name )
          {
            global $fields;

            //check if there are custom fields
            if( $fields  && ! empty( $fields ) ){

              // TODO Checks for input (required fields) and sanitizes/saves if needed
              // for editor type => htmlspecialchars( $_POST[ $field['id'] ] );
              // for text type => sanitize_text_field( $_POST[ $field['id'] ] );

              update_post_meta( $post->ID, 'static_fields', $_POST['static_fields']);
              update_post_meta( $post->ID, 'dynamic_fields', $_POST['dynamic_fields']);
            };
          }

        }
      );

    }

  }
}
