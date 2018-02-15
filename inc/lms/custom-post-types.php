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

  /**
   * Public vars
   *
   * @var string  $post_type_name           Holds the name of the post type.
   * @var array   $post_type_args
   * @var array   $post_type_labels
   * @var string  $taxonomy_name;
   * @var array   $columns                  Columns visible in admin edit screen.
   * @var array   $custom_populate_columns  User functions to populate columns.
   * @var array   $sortable                 Define which columns are sortable on the admin edit screen.
   * @var string  $textdomain               Used for internationalising.
   */

    public $post_type_name;
    public $post_type_args;
    public $post_type_labels;
    public $taxonomy_name;
    public $columns;
    public $custom_populate_columns;
    public $sortable;
    public $textdomain = 'soundlush';

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
     * Add Action
     * Helper function to add add_action WordPress filters.
     *
     * @param string    $action          Name of the action.
     * @param string    $function        Function to hook that will run on action.
     * @param integer   $priority        Order in which to execute the function, relation to other functions hooked to this action.
     * @param integer   $accepted_args   The number of arguments the function accepts.
     */

    function add_action( $action, $function, $priority = 10, $accepted_args = 1 )
    {
        //pass variables into WordPress add_action function
        add_action( $action, $function, $priority, $accepted_args );
    }


    /**
     * Add Filter
     * Create add_filter WordPress filter.
     *
     * @see http://codex.wordpress.org/Function_Reference/add_filter
     *
     * @param  string  $action           Name of the action to hook to, e.g 'init'.
     * @param  string  $function         Function to hook that will run on @action.
     * @param  int     $priority         Order in which to execute the function, relation to other function hooked to this action.
     * @param  int     $accepted_args    The number of arguements the function accepts.
     */

    function add_filter( $action, $function, $priority = 10, $accepted_args = 1 )
    {
        //pass variables into Wordpress add_action function
        add_filter( $action, $function, $priority, $accepted_args );
    }



    /**
     * Register Post Type
     * Registers a new post type
     *
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

      //rewrite post type update messages
      add_filter( 'post_updated_messages', array( &$this, 'updated_messages' ) );
      add_filter( 'bulk_post_updated_messages', array( &$this, 'bulk_updated_messages' ), 10, 2 );
    }




    /**
     * Callback for WordPress 'post_updated_messages' filter.
     * Internal function that modifies the post type names in updated messages
     *
     * @param  array $messages an array of updated messages.
     * @return array $messages an array of updated messages.
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
     * Callback for WordPress 'bulk_updated_messages' filter
     * Internal function that modifies the post type names in bulk updated messages
     *
     * @param array $bulk_messages an array of bulk updated messages
     */

    function bulk_updated_messages( $bulk_messages, $bulk_counts ) {

      $post_type_name = $this->post_type_name;
      $name           = SoundlushHelpers::beautify( $post_type_name );
      $plural         = SoundlushHelpers::pluralize( $name );
      $textdomain     = SoundlushHelpers::get_textdomain();

      $bulk_messages[ $post_type_name ] = array(
          'updated'   => _n( '%s ' . $name . ' updated.', '%s ' . $plural . ' updated.', $bulk_counts['updated'] ),
          'locked'    => _n( '%s ' . $name . ' not updated, somebody is editing it.', '%s ' . $plural . ' not updated, somebody is editing them.', $bulk_counts['locked'] ),
          'deleted'   => _n( '%s ' . $name . ' permanently deleted.', '%s ' . $plural . ' permanently deleted.', $bulk_counts['deleted'] ),
          'trashed'   => _n( '%s ' . $name . ' moved to the Trash.', '%s ' . $plural . ' moved to the Trash.', $bulk_counts['trashed'] ),
          'untrashed' => _n( '%s ' . $name . ' restored from the Trash.', '%s ' . $plural . ' restored from the Trash.', $bulk_counts['untrashed'] ),
      );

      return $bulk_messages;

    }



    /**
     * Add taxonomy
     * Attach the taxonomy to the post type
     *
     * @param string $name    Taxonomy name
     * @param string $type    Accepts: check, radio or select (default: 'check')
     * @param array  $args
     * @param array  $labels
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
      //taxonomy already exists
      else
      {
        //attach the existing taxonomy to the object type (post type)
        add_action( 'init',
          function() use( $taxonomy_name, $post_type_name )
          {
            register_taxonomy_for_object_type( $taxonomy_name, $post_type_name );
          }
        );
      }

      //generate custom metabox based on input type
      if ($type != 'check' ) $this->setup_custom_metabox($taxonomy_name, $post_type_name, $type);
    }



    /**
     * Setup Custom Metabox
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
              switch( $type )
              {
                case 'radio': ?>
                  <div id="taxonomy-<?php echo $taxonomy_name; ?>" class="categorydiv">
                    <div id="<?php echo $taxonomy_name; ?>-all" class="tabs-panel">
                      <ul id="<?php echo $taxonomy_name; ?>checklist" class="list:<?php echo $taxonomy_name?> categorychecklist form-no-clear">
                        <?php   foreach($terms as $term)
                        {
                            $id = $taxonomy_name.'-'.$term->term_id;
                            echo "<li id='$id'><label class='selectit'>";
                            echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)."value='$term->term_id' />$term->name<br />";
                            echo "</label></li>";
                        }
                        ?>
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
     * Add admin columns
     * Adds columns to the admin edit screen. Function is used with add_action
     *
     * @param array     $columns      Columns to be added to the admin edit screen.
     * @return array    $columns
     */

    function add_admin_columns( $columns ) {

        // If no user columns have been specified, add taxonomies
        if ( ! isset( $this->columns ) )
        {
            $new_columns = array();

            // determine which column to add custom taxonomies after
            if ( is_array( $this->taxonomies ) && in_array( 'post_tag', $this->taxonomies ) || $this->post_type_name === 'post' ) {
                $after = 'tags';
            } elseif( is_array( $this->taxonomies ) && in_array( 'category', $this->taxonomies ) || $this->post_type_name === 'post' )
            {
                $after = 'categories';
            } elseif( post_type_supports( $this->post_type_name, 'author' ) )
            {
                $after = 'author';
            } else
            {
                $after = 'title';
            }

            // foreach exisiting columns
            foreach( $columns as $key => $title )
            {
                // add exisiting column to the new column array
                $new_columns[$key] = $title;

                // we want to add taxonomy columns after a specific column
                if( $key === $after ) {

                    // If there are taxonomies registered to the post type.
                    if ( is_array( $this->taxonomies ) ) {

                        // Create a column for each taxonomy.
                        foreach( $this->taxonomies as $tax ) {

                            // WordPress adds Categories and Tags automatically, ignore these
                            if( $tax !== 'category' && $tax !== 'post_tag' ) {

                                // Get the taxonomy object for labels.
                                $taxonomy_object = get_taxonomy( $tax );

                                // Column key is the slug, value is friendly name.
                                $new_columns[ $tax ] = sprintf( __( '%s', $this->textdomain ), $taxonomy_object->labels->name );
                            }
                        }
                    }
                }
            }

            // overide with new columns
            $columns = $new_columns;

        } else
        {
            // Use user submitted columns, these are defined using the object columns() method.
            $columns = $this->columns;
        }

        return $columns;
    }


    /**
     * Populate admin columns
     * Populate custom columns on the admin edit screen.
     *
     * @param string  $column     The name of the column.
     * @param integer $post_id    The post ID.
     */

    function populate_admin_columns( $column, $post_id )
    {
        //get wordpress $post object.
        global $post;

        //determine the column
        switch( $column ) {

            //if column is a taxonomy associated with the post type.
            case ( taxonomy_exists( $column ) ) :

                //get the taxonomy for the post
                $terms = get_the_terms( $post_id, $column );

                //if we have terms.
                if ( ! empty( $terms ) )
                {
                    $output = array();

                    //loop through each term, linking to the 'edit posts' page for the specific term.
                    foreach( $terms as $term )
                    {
                        //output is an array of terms associated with the post.
                        $output[] = sprintf(
                            //define link.
                            '<a href="%s">%s</a>',
                            //create filter url.
                            esc_url( add_query_arg( array( 'post_type' => $post->post_type, $column => $term->slug ), 'edit.php' ) ),
                            //create friendly term name.
                            esc_html( sanitize_term_field( 'name', $term->name, $term->term_id, $column, 'display' ) )
                        );
                    }
                    //join the terms, separating them with a comma.
                    echo join( ', ', $output );

                //if no terms found.
                } else
                {
                    //get the taxonomy object for labels
                    $taxonomy_object = get_taxonomy( $column );
                    //echo no terms.
                    printf( __( 'No %s', $this->textdomain ), $taxonomy_object->labels->name );
                }
                break;

            //if column is for the post ID.
            case 'post_id' :

                echo $post->ID;
                break;

            //if the column is prepended with 'meta_', this will automatically retrieve the meta values and display them.
            case ( preg_match( '/^meta_/', $column ) ? true : false ) :

                //meta_book_author (meta key = book_author)
                $x = substr( $column, 5 );
                $meta = get_post_meta( $post->ID, $x );
                echo join( ", ", $meta );
                break;

            //if the column is post thumbnail.
            case 'icon' :

                //create the edit link.
                $link = esc_url( add_query_arg( array( 'post' => $post->ID, 'action' => 'edit' ), 'post.php' ) );

                //if it post has a featured image.
                if ( has_post_thumbnail() )
                {
                    //display post featured image with edit link.
                    echo '<a href="' . $link . '">';
                        the_post_thumbnail( array(60, 60) );
                    echo '</a>';
                } else
                {
                    //display default media image with link.
                    echo '<a href="' . $link . '"><img src="'. site_url( '/wp-includes/images/crystal/default.png' ) .'" alt="' . $post->post_title . '" /></a>';
                }
                break;

            //default case checks if the column has a user function, this is most commonly used for custom fields.
            default :

                //if there are user custom columns to populate.
                if ( isset( $this->custom_populate_columns ) && is_array( $this->custom_populate_columns ) )
                {
                    //if this column has a user submitted function to run.
                    if ( isset( $this->custom_populate_columns[ $column ] ) && is_callable( $this->custom_populate_columns[ $column ] ) )
                    {
                        //run the function.
                        call_user_func_array(  $this->custom_populate_columns[ $column ], array( $column, $post ) );
                    }
                }
                break;
        }
    }



    /**
     * Columns
     * Choose columns to be displayed on the admin edit screen.
     *
     * @param array   $columns    An array of columns to be displayed.
     */

    function columns( $columns ) {

        //if columns is set.
        if( isset( $columns ) )
        {
            //assign user submitted columns to object.
            $this->columns = $columns;
        }
    }



    /**
     * Populate columns
     * Define what and how to populate a specific admin column.
     *
     * @param string  $column_name    The name of the column to populate.
     * @param mixed   $callback       An anonyous function or callable array to call when populating the column.
     */

    function populate_column( $column_name, $callback )
    {
        $this->custom_populate_columns[ $column_name ] = $callback;
    }



    /**
     * Sortable
     * Define what columns are sortable in the admin edit screen.
     *
     * @param array   $columns        An array of columns that are sortable.
     */

    function sortable( $columns = array() )
    {
        //assign user defined sortable columns to object variable.
        $this->sortable = $columns;

        //run filter to make columns sortable.
        $this->add_filter( 'manage_edit-' . $this->post_type_name . '_sortable_columns', array( &$this, 'make_columns_sortable' ) );

        //run action that sorts columns on request.
        $this->add_action( 'load-edit.php', array( &$this, 'load_edit' ) );
    }



    /**
     * Make columns sortable
     * Internal function that adds user defined sortable columns to WordPress default columns.
     *
     * @param array $columns Columns to be sortable.
     */

    function make_columns_sortable( $columns )
    {
        //for each sortable column.
        foreach ( $this->sortable as $column => $values ) {
            //make an array to merge into wordpress sortable columns.
            $sortable_columns[ $column ] = $values[0];
        }

        //merge sortable columns array into wordpress sortable columns.
        $columns = array_merge( $sortable_columns, $columns );

        return $columns;
    }



    /**
     * Load edit
     * Sort columns only on the edit.php page when requested.
     * @see http://codex.wordpress.org/Plugin_API/Filter_Reference/request
     */

    function load_edit()
    {
        //run filter to sort columns when requested
        $this->add_filter( 'request', array( &$this, 'sort_columns' ) );
    }



    /**
     * Sort columns
     * Internal function that sorts columns on request.
     * @see load_edit()
     *
     * @param array     $vars     The query vars submitted by user.
     * @return array    $vars     A sorted array.
     */

    function sort_columns( $vars )
    {

        //cycle through all sortable columns submitted by the user
        foreach ( $this->sortable as $column => $values ) {

            //retrieve the meta key from the user submitted array of sortable columns
            $meta_key = $values[0];

            //if the meta_key is a taxonomy
            if( taxonomy_exists( $meta_key ) )
            {
                //sort by taxonomy.
                $key = "taxonomy";
            } else
            {
                //else by meta key.
                $key = "meta_key";
            }


            //if the optional parameter is set and is set to true
            if ( isset( $values[1] ) && true === $values[1] )
            {
                //values needed to be ordered by integer value
                $orderby = 'meta_value_num';
            } else
            {
                //values are to be order by string value
                $orderby = 'meta_value';
            }


            //check if we're viewing this post type
            if ( isset( $vars['post_type'] ) && $this->post_type_name == $vars['post_type'] ) {
                //find the meta key we want to order posts by
                if ( isset( $vars['orderby'] ) && $meta_key == $vars['orderby'] ) {
                    //merge the query vars with our custom variables
                    $vars = array_merge(
                        $vars,
                        array(
                            'meta_key' => $meta_key,
                            'orderby' => $orderby
                        )
                    );
                }
            }
        }
        return $vars;
    }




    /**
     * Add Custom Fields
     * Attach static custom field meta boxes to the post type
     *
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
     * Add Dynamic Custom Fields
     * Attach dynamic custom field meta boxes to the post type
     *
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
                      echo '<table class="form-table">';

                      $output = '';

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
                        $meta        = isset( $answer[ $field['id'] ] ) ? $answer[ $field['id'] ] : $standard ;

                        switch ( $type )
                        {
                          case 'text':

                            echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><input type="text" class="widefat" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" value="' . $meta  . '"' . $required . ' />' . $description . '</td></tr>';
                            break;

                          case 'number':

                            $min  = isset( $field['min'] ) ? ' min="' . $field['min'] . '" ' : '';
                            $max  = isset( $field['max'] ) ? ' max="' . $field['max'] . '" ' : '';
                            $step = isset( $field['step'] ) ? ' step="'. $field['step'] . '" ' : '';

                            echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><input type="number" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" value="' . $meta . '"' . $required . $min . $max . $step . ' /></br>' . $description . '</td></tr>';
                            break;

                          case 'audio':

                            echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" value="' . $meta  . '"' . $required . 'accept=".mp3, .wav, .ogg" />' . $description . '</td></tr>';
                            break;

                          case 'image':

                            echo '<tr><th scope="row"><label for="dynamic_fields_' . $c . '_' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="dynamic_fields[' . $c . '][' . $id . ']" id="dynamic_fields_' . $c . '_' . $id . '" value="' . $meta  . '"' . $required . 'accept=".jpg, .jpeg, .png, .gif" />' . $description . '</td></tr>';
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
                  $type        = isset( $field['type'] ) ? $field['type'] : 'text'; //default = text
                  $required    = ( isset( $field['required'] ) && $field['required'] ) ? ' required' : ''; //default: false
                  $description = isset( $field['desc'] ) ? '<span class="description">' . $field['desc'] . '</span>' : ''; //Optional
                  $standard    = isset( $field['std'] ) ? $field['std'] : ''; //Optional

                  //TODO add value = standart

                  switch ( $type )
                  {
                    case 'text':

                      $output .=  '<tr><th scope="row"><label for="dynamic_fields_count_variable_' . $id . '">' . $name . ': </label></th><td><input type="text" class="widefat" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '"' . $required . ' />' . $description . '</td></tr>';
                      break;

                    case 'number':

                      $min  = isset( $field['min'] ) ? ' min="' . $field['min'] . '" ' : '';
                      $max  = isset( $field['max'] ) ? ' max="' . $field['max'] . '" ' : '';
                      $step = isset( $field['step'] ) ? ' step="'. $field['step'] . '" ' : '';

                      $output .= '<tr><th scope="row"><label for="dynamic_fields_count_variable_' . $id . '">' . $name . ': </label></th><td><input type="number" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '"' . $required . $min . $max . $step . ' /></br>' . $description . '</td></tr>';
                      break;

                    case 'audio':

                      $output .= '<tr><th scope="row"><label for="dynamic_fields_count_variable_' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '"' . $required . 'accept=".mp3, .wav, .ogg" />' . $description . '</td></tr>';
                      break;

                    case 'image':

                      $output .= '<tr><th scope="row"><label for="dynamic_fields_count_variable_' . $id . '">' . $name . ': </label></th><td><input type="file" class="widefat" name="dynamic_fields[count_variable][' . $id . ']" id="dynamic_fields_count_variable_' . $id . '"' . $required . 'accept=".jpg, .jpeg, .png, .gif" />' . $description . '</td></tr>';
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
     * Save
     * Listen for when the post type is being saved
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
            if( $fields  && ! empty( $fields ) )
            {
              // TODO Sanitizes inputs
              foreach ( $fields as $field )
              {
                if( isset( $_POST[$field['id']] ) )
                {
                  switch ( $field['type'] = 'editor' ) {
                    case 'editor':
                      //$_POST[ $field['id']] = htmlspecialchars( $_POST[ $field['id'] ] );
                      break;

                    case 'text':
                      //$_POST[ $field['id']] = sanitize_text_field( $_POST[ $field['id'] ] );
                      break;

                    case 'audio':
                    case 'image':
                      //$_POST[ $field['id']] = sanitize_file_name( $_POST[ $field['id'] ] );
                      //sanitize mime type
                      break;

                    default:
                      break;
                  }
                }
              }

              // Make sure the file array isn't empty
              // if(!empty( $_FILES['static_fields'][$field['id']] )) {
              //
              //   // Setup the array of supported file types.
              //   $supported_types = array('image/jpeg', 'image/png', image/gif);
              //
              //   // Get the file type of the upload
              //   $arr_file_type = wp_check_filetype(basename($_FILES['static_fields'][$field['id']]));
              //   $uploaded_type = $arr_file_type['type'];
              //
              //   // Check if the type is supported. If not, throw an error.
              //   if(in_array($uploaded_type, $supported_types)) {
              //
              //   // Use the WordPress API to upload the file
              //   $upload = wp_upload_bits(
              //     $_FILES['static_fields'][$field['id']],
              //     null,
              //     file_get_contents($_FILES['wp_custom_attachment']['tmp_name'])
              //   );
              //         if(isset($upload['error']) && $upload['error'] != 0) {
              //             wp_die('There was an error uploading your file. The error is: ' . $upload['error']);
              //         } else {
              //             update_post_meta($post->ID, 'wp_custom_attachment', $upload);
              //         }
              //
              //     } else {
              //         wp_die("The file type that you've uploaded is not a PDF.");
              //     }
              //
              //}

              update_post_meta( $post->ID, 'static_fields', $_POST['static_fields']);
              update_post_meta( $post->ID, 'dynamic_fields', $_POST['dynamic_fields']);
            };
          }

        }
      );

    }






  }
}
