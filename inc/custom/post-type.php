<?php

if( !class_exists( 'SoundlushPostType' ) )
{
class SoundlushPostType
{
    /**
     * The names passed to the PostType
     * @var array
     */
    public $names;

    /**
     * The name for the PostType
     * @var array
     */
    public $name;

    /**
     * The singular for the PostType
     * @var array
     */
    public $singular;

    /**
     * The plural name for the PostType
     * @var array
     */
    public $plural;

    /**
     * The slug for the PostType
     * @var array
     */
    public $slug;

    /**
     * Options for the PostType
     * @var array
     */
    public $options;

    /**
     * Labels for the PostType
     * @var array
     */
    public $labels;

    /**
     * Taxonomies for the PostType
     * @var array
     */
    public $taxonomies = [];

    /**
     * Filters for the PostType
     * @var mixed
     */
    public $filters;

    /**
     * The menu icon for the PostType
     * @var string
     */
    public $icon;

    /**
     * The column manager for the PostType
     * @var mixed
     */
    public $columns;

    /**
     * The custom field manager for the PostType
     * @var mixed
     */
    public $customfields;



    /**
     * Create a PostType
     * @param mixed $names   A string for the name, or an array of names
     * @param array $options An array of options for the PostType
     */
    public function __construct($names, $options = [], $labels = [])
    {
        // assign names to the PostType
        $this->names($names);
        // assign custom options to the PostType
        $this->options($options);
        // assign labels to the PostType
        $this->labels($labels);
    }



    /**
     * Set the names for the PostType
     * @param  mixed $names A string for the name, or an array of names
     * @return $this
     */
    public function names($names)
    {
        // only the post type name is passed
        if (is_string($names)) {
            $names = ['name' => $names];
        }
        // set the names array
        $this->names = $names;
        // create names for the PostType
        $this->createNames();
        return $this;
    }



    /**
     * Set the options for the PostType
     * @param  array $options An array of options for the PostType
     * @return $this
     */
    public function options(array $options)
    {
        $this->options = $options;
        return $this;
    }



    /**
     * Set the labels for the PostType
     * @param  array $labels An array of labels for the PostType
     * @return $this
     */
    public function labels(array $labels)
    {
        $this->labels = $labels;
        return $this;
    }



    /**
     * Add a Taxonomy to the PostType
     * @param  string $taxonomy The Taxonomy name to add
     * @return $this
     */
    public function taxonomy($taxonomy)
    {
        $this->taxonomies[] = $taxonomy;
        return $this;
    }



    /**
     * Add filters to the PostType
     * @param  array $filters An array of Taxonomy filters
     * @return $this
     */
    public function filters(array $filters)
    {
        $this->filters = $filters;
        return $this;
    }



    /**
     * Set the menu icon for the PostType
     * @param  string $icon A dashicon class for the menu icon
     * @return $this
     */
    public function icon($icon)
    {
        $this->icon = $icon;
        return $this;
    }



    /**
     * Flush rewrite rules
     * @link https://codex.wordpress.org/Function_Reference/flush_rewrite_rules
     * @param  boolean $hard
     * @return void
     */
    public function flush($hard = true)
    {
        flush_rewrite_rules($hard);
    }



    /**
     * Get the Column Manager for the PostType
     * @return Columns
     */
    public function columns()
    {
        if (!isset($this->columns)) {
            $this->columns = new SoundlushColumns;
        }
        return $this->columns;
    }


    /**
     * Get the Custom Field Manager for the PostType
     * @return Custom Fields
     */
    public function customfields()
    {
        if (!isset($this->customfields)) {
            $this->customfields = new SoundlushPostMeta($this->name);
        }
        return $this->customfields;
    }


    /**
     * Register the PostType to WordPress
     * @return void
     */
    public function register()
    {
        // register the PostType
        add_action('init', [&$this, 'registerPostType']);
        //rewrite post type update messages
        add_filter( 'post_updated_messages', array( &$this, 'updateMessages' ) );
        add_filter( 'bulk_post_updated_messages', array( &$this, 'bulkUpdateMessages' ), 10, 2 );
        // register Taxonomies to the PostType
        add_action('init', [&$this, 'registerTaxonomies']);
        // modify filters on the admin edit screen
        add_action('restrict_manage_posts', [&$this, 'modifyFilters']);
        if( isset( $this->columns ) ){
            // modify the admin edit columns.
            add_filter("manage_{$this->name}_posts_columns", [&$this, 'modifyColumns'], 10, 1);
            // populate custom columns
            add_filter("manage_{$this->name}_posts_custom_column", [&$this, 'populateColumns'], 10, 2);
            // run filter to make columns sortable.
            add_filter('manage_edit-'.$this->name.'_sortable_columns', [&$this, 'setSortableColumns']);
            // run action that sorts columns on request.
            add_action('pre_get_posts', [&$this, 'sortSortableColumns']);
        }
        if( isset( $this->customfields ) ){
            // listen for the save post hook to save custom fields
            add_action( 'save_post', [&$this,'saveCustomFields' ], 10, 1 );
        }
    }



    /**
     * Register the PostType
     * @return void
     */
    public function registerPostType()
    {
        // create options for the PostType
        $options = $this->createOptions();

        // check that the post type doesn't already exist
        if (!post_type_exists($this->name)) {
            // register the post type
            register_post_type($this->name, $options);
        }
    }



    /**
     * Create the required names for the PostType
     * @return void
     */
    public function createNames()
    {
        // names required for the PostType
        $required = [
            'name',
            'singular',
            'plural',
            'slug',
        ];
        foreach ($required as $key) {
            // if the name is set, assign it
            if (isset($this->names[$key])) {
                $this->$key = $this->names[$key];
                continue;
            }
            // if the key is not set and is singular or plural
            if (in_array($key, ['singular', 'plural'])) {
                // create a human friendly name
                $name = ucwords(strtolower(str_replace(['-', '_'], ' ', $this->names['name'])));
            }
            if ($key === 'slug') {
                // create a slug friendly name
                $name = strtolower(str_replace([' ', '_'], '-', $this->names['name']));
            }
            // if is plural or slug, append an 's'
            if (in_array($key, ['plural', 'slug'])) {
                $name .= 's';
            }
            // asign the name to the PostType property
            $this->$key = $name;
        }
    }



    /**
     * Create options for PostType
     * @return array Options to pass to register_post_type
     */
    public function createOptions()
    {
        // default options
        $options = [
            'public'    => true,
            'rewrite'   => [
                'slug'  => $this->slug
            ]
        ];

        // replace defaults with the options passed
        $options = array_replace_recursive($options, $this->options);
        // create and set labels
        if (!isset($options['labels'])) {
            $options['labels'] = $this->createLabels();
        }
        // set the menu icon
        if (!isset($options['menu_icon']) && isset($this->icon)) {
            $options['menu_icon'] = $this->icon;
        }
        return $options;
    }



    /**
     * Create the labels for the PostType
     * @return array
     */
    public function createLabels()
    {
        // default labels
        $labels = [
            'name'                => $this->plural,
            'singular_name'       => $this->singular,
            'menu_name'           => $this->plural,
            'all_items'           => $this->plural,
            'add_new'             => "Add New",
            'add_new_item'        => "Add New {$this->singular}",
            'edit_item'           => "Edit {$this->singular}",
            'new_item'            => "New {$this->singular}",
            'view_item'           => "View {$this->singular}",
            'search_items'        => "Search {$this->plural}",
            'not_found'           => "No {$this->plural} found",
            'not_found_in_trash'  => "No {$this->plural} found in Trash",
            'parent_item_colon'   => "Parent {$this->singular}:",
        ];
        return array_replace_recursive($labels, $this->labels);
    }



    /**
     * Modifies the post type names in updated messages
     * @param  array $messages an array of updated messages.
     * @return array $messages an array of updated messages.
     */

    public function updateMessages( $messages )
    {
      global $post;

      $post_ID = $post->ID;

      $posttype = $this->name;

      $messages[$posttype] = array(
          0 => '', //unused. messages start at index 1.
          1 => sprintf( __( $this->singular . ' updated. <a href="%s">View ' . $this->singular . '</a>'), esc_url( get_permalink($post_ID) ) ),
          2 => __( $this->singular . ' field updated.' ),
          3 => __( $this->singular . ' field deleted.' ),
          4 => __( $this->singular . ' updated.' ),
          5 => isset($_GET['revision']) ? sprintf( __( $this->singular . ' restored to revision from %s' ), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
          6 => sprintf( __( $this->singular . ' published. <a href="%s">View run</a>' ), esc_url( get_permalink( $post_ID ) ) ),
          7 => __( $this->singular  . ' saved.' ),
          8 => sprintf( __( $this->singular . ' submitted. <a target="_blank" href="%s">Preview run</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
          9 => sprintf( __( $this->singular . ' scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview run</a>' ), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink( $post_ID ) ) ),
          10 => sprintf( __( $this->singular . ' draft updated. <a target="_blank" href="%s">Preview post type</a>' ), esc_url( add_query_arg( 'preview', 'true', get_permalink( $post_ID ) ) ) ),
      );

      return $messages;
    }



    /**
     * Modifies the post type names in bulk updated messages
     * @param array   $bulk_messages an array of bulk updated messages
     * @return array  $bulk_messages an array of bulk updated messages
     */

    function bulkUpdateMessages( $bulk_messages, $bulk_counts )
    {
      $posttype = $this->name;

      $bulk_messages[ $posttype ] = array(
          'updated'   => _n( '%s ' . $this->singular . ' updated.', '%s ' . $this->plural . ' updated.', $bulk_counts['updated'] ),
          'locked'    => _n( '%s ' . $this->singular . ' not updated, somebody is editing it.', '%s ' . $this->plural . ' not updated, somebody is editing them.', $bulk_counts['locked'] ),
          'deleted'   => _n( '%s ' . $this->singular . ' permanently deleted.', '%s ' . $this->plural . ' permanently deleted.', $bulk_counts['deleted'] ),
          'trashed'   => _n( '%s ' . $this->singular . ' moved to the Trash.', '%s ' . $this->plural . ' moved to the Trash.', $bulk_counts['trashed'] ),
          'untrashed' => _n( '%s ' . $this->singular . ' restored from the Trash.', '%s ' . $this->plural . ' restored from the Trash.', $bulk_counts['untrashed'] ),
      );

      return $bulk_messages;
    }



    /**
     * Register Taxonomies to the PostType
     * @return void
     */
    public function registerTaxonomies()
    {
        if (!empty($this->taxonomies)) {
            foreach ($this->taxonomies as $taxonomy) {
                register_taxonomy_for_object_type($taxonomy, $this->name);
            }
        }
    }



    /**
     * Modify and display filters on the admin edit screen
     * @param  string $posttype The current screen post type
     * @return void
     */
    public function modifyFilters($posttype)
    {
        // first check we are working with the this PostType
        if ($posttype === $this->name) {
            // calculate what filters to add
            $filters = $this->getFilters();
            foreach ($filters as $taxonomy) {
                // if the taxonomy doesn't exist, ignore it
                if (!taxonomy_exists($taxonomy)) {
                    continue;
                }
                // get the taxonomy object
                $tax = get_taxonomy($taxonomy);
                // get the terms for the taxonomy
                $terms = get_terms([
                    'taxonomy'    => $taxonomy,
                    'orderby'     => 'name',
                    'hide_empty'  => false,
                ]);
                // if there are no terms in the taxonomy, ignore it
                if (empty($terms)) {
                    continue;
                }
                // start the html for the filter dropdown
                $dropdown = sprintf(' &nbsp;<select name="%s" class="postform">', $taxonomy);
                // set 'Show all' option
                $dropdown .= sprintf('<option value="0">%s</option>', "Show all {$tax->label}");
                // create option for each taxonomy tern
                foreach ($terms as $term) {
                    $selected = '';
                    // if the current term is active, add selected attribute
                    if (isset($_GET[$taxonomy]) && $_GET[$taxonomy] === $term->slug) {
                        $selected = ' selected="selected"';
                    }
                    // html for term option
                    $dropdown .= sprintf(
                        '<option value="%s"%s>%s (%s)</option>',
                        $term->slug,
                        $selected,
                        $term->name,
                        $term->count
                    );
                }
                // end the select field
                $dropdown .= '</select>&nbsp;';
                // display the dropdown filter
                echo $dropdown;
            }
        }
    }



    /**
     * Calculate the filters for the PostType
     * @return array
     */
    public function getFilters()
    {
        // default filters are empty
        $filters = [];
        // if custom filters have been set, use them
        if (!is_null($this->filters)) {
            return $this->filters;
        }
        // if no custom filters have been set, and there are taxonomies assigned to the PostType
        if (is_null($this->filters) && !empty($this->taxonomies)) {
            // create filters for each taxonomy assigned to the PostType
            return $this->taxonomies;
        }
        return $filters;
    }



    /**
     * Modify the columns for the PostType
     * @param  array  $columns  Default WordPress columns
     * @return array            The modified columns
     */
    public function modifyColumns($columns)
    {
        $columns = $this->columns->modifyColumns($columns);
        return $columns;
    }



    /**
     * Populate custom columns for the PostType
     * @param  string $column   The column slug
     * @param  int    $post_id  The post ID
     */
    public function populateColumns($column, $post_id)
    {
        if (isset($this->columns->populate[$column])) {
            call_user_func_array($this->columns()->populate[$column], [$column, $post_id]);
        }
    }



    /**
     * Make custom columns sortable
     * @param array  $columns  Default WordPress sortable columns
     */
    public function setSortableColumns($columns)
    {
        if (!empty($this->columns()->sortable)) {
            $columns = array_merge($columns, $this->columns()->sortable);
        }
        return $columns;
    }



    /**
     * Set query to sort custom columns
     * @param  WP_Query $query
     */
    public function sortSortableColumns($query)
    {
        // don't modify the query if we're not in the post type admin
        if (!is_admin() || $query->get('post_type') !== $this->name) {
            return;
        }

        $orderby = $query->get('orderby');

        // if the sorting a custom column
        if (array_key_exists($orderby, $this->columns()->sortable)) {
            // get the custom column options
            $meta = $this->columns()->sortable[$orderby];
            // determine type of ordering
            if (is_string($meta)) {
                $meta_key = $meta;
                $meta_value = 'meta_value';
            } else {
                $meta_key = $meta[0];
                $meta_value = 'meta_value_num';
            }
            // set the custom order
            $query->set('meta_key', $meta_key);
            $query->set('orderby', $meta_value);
        }
    }



    /**
     * Save Custom Fields for the PostType
     * @param  array  $name     The post type name
     */
    public function saveCustomFields()
    {
        $this->customfields->saveCustomFields($this->name);
    }
}
}
