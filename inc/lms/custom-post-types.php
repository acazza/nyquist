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

      // Set some important variables
      $this->post_type_name    = SoundlushHelpers::uglify( $name );
      $this->post_type_args    = $args;
      $this->post_type_labels  = $labels;

      // Add action to register the post type, if the post type does not already exist
      if( ! post_type_exists( $this->post_type_name ) )
      {
        add_action( 'init', array( &$this, 'register_post_type' ) );
      }

      SoundlushHelpers::activate();

      // Listen for the save post hook
      $this->save();
    }



    /**
     * Method which registers the post type
     * @param
     */

    public function register_post_type()
    {
      //Capitilize the words and make it plural
      $name       = SoundlushHelpers::beautify( $this->post_type_name );
      $plural     = SoundlushHelpers::pluralize( $name );

      // We set the default labels based on the post type name and plural. We overwrite them with the given labels.
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

      // Same principle as the labels. We set some defaults and overwrite them with the given arguments.
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

      // Register the post type
      register_post_type( $this->post_type_name, $args );

      // Update post type messages
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
      $name = SoundlushHelpers::beautify( $post_type_name );

      $messages[$post_type_name] = array(
          0 => '', // Unused. Messages start at index 1.
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
        // We need to know the post type name, so the new taxonomy can be attached to it.
        $post_type_name = $this->post_type_name;

        // Taxonomy properties
        $taxonomy_name      = SoundlushHelpers::uglify( $name );
        $taxonomy_labels    = $labels;
        $taxonomy_args      = $args;
      }

      if( ! taxonomy_exists( $taxonomy_name ) )
      {
        // Create taxonomy and attach it to the object type (post type)

        //Capitilize the words and make it plural
        $name       = SoundlushHelpers::beautify( $name );
        $plural     = SoundlushHelpers::pluralize( $name );

        // Default labels, overwrite them with the given labels.
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

        // Default arguments, overwritten with the given arguments
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

        // Add the taxonomy to the post type
        add_action( 'init',
          function() use( $taxonomy_name, $post_type_name, $args )
          {
            register_taxonomy( $taxonomy_name, $post_type_name, $args );
          }
        );
      }
      else
      {
        // The taxonomy already exists. We are going to attach the existing taxonomy to the object type (post type)
        add_action( 'init',
          function() use( $taxonomy_name, $post_type_name )
          {
            register_taxonomy_for_object_type( $taxonomy_name, $post_type_name );
          }
        );
      }

      // Generate custom metabox input type
      if ($type != 'check' ) $this->setup_custom_metabox($taxonomy_name, $post_type_name, $type);
    }


    /**
     * Generate custom metabox input type
     * @param $type: check, radio, select
     */

    public function setup_custom_metabox($taxonomy_name, $post_type_name, $type ){

      //Remove taxonomy meta box
      add_action( 'admin_menu',
        function() use( $taxonomy_name, $post_type_name )
        {
          $tax_mb_id = $taxonomy_name.'div';
          remove_meta_box($tax_mb_id, $post_type_name, 'normal');
        }
      );

      //Add custom meta box
      add_action( 'add_meta_boxes',
        function() use( $taxonomy_name, $post_type_name, $type )
        {
          $name = SoundlushHelpers::beautify( $taxonomy_name );
          add_meta_box( 'mytaxonomy_id', $name,
            function() use( $taxonomy_name, $type )
            {
              global $post;

              //Set up the taxonomy object and get terms
              $taxonomy_name = $taxonomy_name;
              $tax = get_taxonomy($taxonomy_name);
              $terms = get_terms($taxonomy_name, array('hide_empty' => 0));

              //Name of the form
              $name = 'tax_input[' . $taxonomy_name . ']';

              $postterms = get_the_terms( $post->ID, $taxonomy_name );
              $current = ($postterms ? array_pop($postterms) : false);
              $current = ($current ? $current->term_id : 0);

              // Check taxonomy input type
              switch($type){
                case 'radio': ?>
                  <!-- Display taxonomy terms -->
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
              }
            }
            ,$post_type_name ,'side','core');
        }
      );
    }



    /**
     * Attaches custom field meta boxes to the post type
     * @param $custom_fields
     */

    public function add_custom_fields( $custom_array )
    {
      if( ! empty( $custom_array ) )
      {
        // We need to know the Post Type name again
        $post_type_name = $this->post_type_name; // book

        global $fields;

        // Meta variables
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

              wp_nonce_field( basename( __FILE__ ), 'custom_post_type_nonce' );

              // Check the array and loop through it
              if( ! empty( $fields ) ) {

                  /* Loop through $fields */
                  foreach ( $fields as $field)
                  { // TODO FALLBACK FOR ALL FIELDS

                    $meta = get_post_meta( $post->ID, $field['id'], true );

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

                      case 'editor':
                        $settings = array(
                          'wpautop'       => false,
                          'media_buttons' => false,
                          'textarea_name' => $field['id'],
                        );
                        wp_editor( htmlspecialchars_decode( $meta ), $field['id'], $settings );
                        break;

                      default:
                        break;
                    }
                  }
              }
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
      * @param $custom_fields
      */

    function add_dynamic_custom_fields( $custom_array )
    {
      if( ! empty( $custom_array ) )
      {
        // We need to know the Post Type name again
        $post_type_name = $this->post_type_name; // question

        global $fields;

        // Meta variables
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

              wp_nonce_field( basename( __FILE__ ), 'custom_post_type_nonce' ); ?>

              <div id="meta_inner">

              <?php
              //get the saved meta as an array
              $meta = get_post_meta( $post->ID, 'answers', false );

              $c = 0;
              if( is_array($meta) )
              {
                foreach($meta as $answers )
                {
                  foreach( $answers as $answer )
                  {
                    if( isset( $answer['content'] ) || isset( $answer['correct'] ) )
                    {
                      printf( '<div><label for="answers[%1$s][content]">Answer: </label><input type="text" name="answers[%1$s][content]" value="%2$s" /><label for="answers[%1$s][correct]"><input type="checkbox" name="answers[%1$s][correct]" %3$s />Correct</label><button class="remove">%4$s</button></div>', $c, $answer['content'], isset($answer['correct']) ? 'checked' : '', __( 'Remove Answer' ) );
                      $c = $c +1;
                    }
                  }
                }
              }
              ?>
              <span id="here"></span>
              <button class="add"><?php _e('Add Answers'); ?></button>

              <script>
                  var $ =jQuery.noConflict();
                  $(document).ready(function() {
                    var count = <?php echo $c; ?>;
                    $(".add").click(function() {
                        count = count + 1;
                        if( count < 6 ){
                          $('#here').append('<div><label for="answers['+count+'][content]">Answer: </label><input type="text" name="answers['+count+'][content]" value="" /> <label for ="answers['+count+'][correct]"><input type="checkbox" name="answers['+count+'][correct]" />Correct</label><button class="remove">Remove Answer</button></div>' );
                          return false;
                        }
                    });
                    $(".remove").live('click', function() {
                        $(this).parent().remove();
                    });
                  });
              </script>
              </div>
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
     * @param
     */

    public function save()
    {
      // Need the post type name again
      $post_type_name = $this->post_type_name;

      add_action( 'save_post',
        function() use( $post_type_name )
        {

          global $post;

          // Deny the WordPress autosave function
          if( defined('DOING_AUTOSAVE') && DOING_AUTOSAVE ) return; //return $post->ID;


          // Verify nonce
          if ( !isset($_POST['custom_post_type_nonce']) || !wp_verify_nonce( $_POST['custom_post_type_nonce'], basename(__FILE__) ) )
          {
            return;
          }


          // Check permissions
          if ('page' == $_POST['custom_post_type_nonce'])
          {
            if ( !current_user_can('edit_page', $post->ID ) || !current_user_can('edit_post', $post->ID  ) )
            {
              return;
            }
          }

          // Save Static Custom Fields
          if( isset( $_POST ) && isset( $post->ID ) && get_post_type( $post->ID ) == $post_type_name )
          {
            global $fields;


            if( $fields  && ! empty( $fields ) ){

              // Checks for input and sanitizes/saves if needed
              foreach ( $fields as $field )
              {
                if( isset( $_POST[$field['id']] ) )
                {
                  if( $field['type'] = 'editor' ) {
                    $new = htmlspecialchars( $_POST[ $field['id'] ] );
                  } else {
                    $new = sanitize_text_field( $_POST[ $field['id'] ] );
                  }
                  update_post_meta( $post->ID, $field['id'],  $new );
                }
              }
            };
          }

          update_post_meta( $post->ID, 'answers', $_POST['answers']);
        }
      );

    }

  }
}
