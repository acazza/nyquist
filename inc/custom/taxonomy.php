<?php

if( !class_exists( 'SoundlushTaxonomy' ) )
{
class SoundlushTaxonomy
{
    /**
     * The names passed to the Taxonomy
     * @var mixed
     */
    public $names;

    /**
     * The Taxonomy name
     * @var string
     */
    public $name;

    /**
     * The singular label for the Taxonomy
     * @var string
     */
    public $singular;

    /**
     * The plural label for the Taxonomy
     * @var string
     */
    public $plural;

    /**
     * The Taxonomy slug
     * @var string
     */
    public $slug;

    /**
     * Custom options for the Taxonomy
     * @var array
     */
    public $options;

    /**
     * Custom labels for the Taxonomy
     * @var array
     */
    public $labels;

    /**
     * PostTypes to register the Taxonomy to
     * @var array
     */
    public $posttypes = [];

    /**
     * The column manager for the Taxonomy
     * @var mixed
     */
    public $columns;



    public $metakey;
    public $metavalue;



    /**
     * Create a Taxonomy
     * @param mixed $names The name(s) for the Taxonomy
     */
    public function __construct($names, $options = [], $labels = [])
    {
        $this->names($names);
        $this->options($options);
        $this->labels($labels);
    }



    /**
     * Set the names for the Taxonomy
     * @param  mixed $names The name(s) for the Taxonomy
     * @return $this
     */
    public function names($names)
    {
        if (is_string($names)) {
            $names = ['name' => $names];
        }
        $this->names = $names;
        // create names for the Taxonomy
        $this->createNames();
        return $this;
    }



    /**
     * Set options for the Taxonomy
     * @param  array $options
     * @return $this
     */
    public function options(array $options = [])
    {
        $this->options = $options;
        return $this;
    }



    /**
     * Set the Taxonomy labels
     * @param  array  $labels
     * @return $this
     */
    public function labels(array $labels = [])
    {
        $this->labels = $labels;
        return $this;
    }



    /**
     * Assign a PostType to register the Taxonomy to
     * @param  string $posttype
     * @return $this
     */
    public function posttype($posttype)
    {
        $this->posttypes[] = $posttype;
        return $this;
    }



    /**
     * Get the Column Manager for the Taxonomy
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
     * Get the Custom Field Manager for the Taxonomy
     * @return Custom Fields
     */
    public function customfields()
    {
        if (!isset($this->customfields)) {
            $this->customfields = new SoundlushTaxonomyMeta($this->name);
        }
        return $this->customfields;
    }



    /**
     * Register the Taxonomy to WordPress
     * @return void
     */
    public function register()
    {
        // register the taxonomy, set priority to 9, so taxonomies are registered before PostTypes
        add_action('init', [&$this, 'registerTaxonomy'], 9);
        // assign taxonomy to post type objects
        add_action('init', [&$this, 'registerTaxonomyToObjects']);
        if( isset($this->columns) )
        {
            // modify the columns for the Taxonomy
            add_filter("manage_edit-{$this->name}_columns", [&$this, 'modifyColumns']);
            // populate the columns for the Taxonomy
            add_filter("manage_{$this->name}_custom_column", [&$this, 'populateColumns'], 10, 3);
            // set custom sortable columns
            add_filter("manage_edit-{$this->name}_sortable_columns", [&$this, 'setSortableColumns']);
            // run action that sorts columns on request
            add_action('parse_term_query', [&$this, 'sortSortableColumns']);
        }
        if( isset( $this->customfields ) )
        {
            // listen for the create Taxonomy hook to save custom fields
            add_action( 'create_'.$this->name, [&$this, 'save_taxonomy_custom_meta'], 10, 2 );
            // listen for the edit Taxonomy hook to save custom fields
            add_action( 'edited_'.$this->name, [&$this, 'save_taxonomy_custom_meta'], 10, 2 );
        }
    }



    /**
     * Register the Taxonomy to WordPress
     * @return void
     */
    public function registerTaxonomy()
    {
        if (!taxonomy_exists($this->name)) {
            // create options for the Taxonomy
            $options = $this->createOptions();
            // register the Taxonomy with WordPress
            register_taxonomy($this->name, null, $options);
        }
    }



    /**
     * Register the Taxonomy to PostTypes
     * @return void
     */
    public function registerTaxonomyToObjects()
    {
        // register Taxonomy to each of the PostTypes assigned
        if (!empty($this->posttypes)) {
            foreach ($this->posttypes as $posttype) {
                register_taxonomy_for_object_type($this->name, $posttype);
            }
        }
    }



    /**
     * Create names for the Taxonomy
     * @return void
     */
    public function createNames()
    {
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
     * Create options for Taxonomy
     * @return array Options to pass to register_taxonomy
     */
    public function createOptions()
    {
        // default options
        $options = [
            'hierarchical' => true,
            'show_admin_column' => true,
            'rewrite' => [
                'slug' => $this->slug,
            ],
        ];
        // replace defaults with the options passed
        $options = array_replace_recursive($options, $this->options);
        // create and set labels
        if (!isset($options['labels'])) {
            $options['labels'] = $this->createLabels();
        }
        return $options;
    }



    /**
     * Create labels for the Taxonomy
     * @return array
     */
    public function createLabels()
    {
        // default labels
        $labels = [
            'name'                        => $this->plural,
            'singular_name'               => $this->singular,
            'menu_name'                   => $this->plural,
            'all_items'                   => "All {$this->plural}",
            'edit_item'                   => "Edit {$this->singular}",
            'view_item'                   => "View {$this->singular}",
            'update_item'                 => "Update {$this->singular}",
            'add_new_item'                => "Add New {$this->singular}",
            'new_item_name'               => "New {$this->singular} Name",
            'parent_item'                 => "Parent {$this->plural}",
            'parent_item_colon'           => "Parent {$this->plural}:",
            'search_items'                => "Search {$this->plural}",
            'popular_items'               => "Popular {$this->plural}",
            'separate_items_with_commas'  => "Seperate {$this->plural} with commas",
            'add_or_remove_items'         => "Add or remove {$this->plural}",
            'choose_from_most_used'       => "Choose from most used {$this->plural}",
            'not_found'                   => "No {$this->plural} found",
        ];
        return array_replace($labels, $this->labels);
    }



    /**
     * Modify the columns for the Taxonomy
     * @param  array  $columns  The WordPress default columns
     * @return array
     */
    public function modifyColumns($columns)
    {
        $columns = $this->columns->modifyColumns($columns);
        return $columns;
    }



    /**
     * Populate custom columns for the Taxonomy
     * @param  string $content
     * @param  string $column
     * @param  int    $term_id
     */
    public function populateColumns($content, $column, $term_id)
    {
        if (isset($this->columns->populate[$column])) {
            $content = call_user_func_array($this->columns()->populate[$column], [$content, $column, $term_id]);
        }
        return $content;
    }



    /**
     * Make custom columns sortable
     * @param array $columns Default WordPress sortable columns
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
     * @param WP_Term_Query $query
     */
    public function sortSortableColumns($query)
    {
        // don't modify the query if we're not in the post type admin
        if (!is_admin() || !in_array($this->name, $query->query_vars['taxonomy'])) {
            return;
        }
        // check the orderby is a custom ordering
        if (isset($_GET['orderby']) && array_key_exists($_GET['orderby'], $this->columns()->sortable)) {
            // get the custom sorting options
            $meta = $this->columns()->sortable[$_GET['orderby']];
            // check ordering is not numeric
            if (is_string($meta)) {
                $meta_key = $meta;
                $orderby = 'meta_value';
            } else {
                $meta_key = $meta[0];
                $orderby = 'meta_value_num';
            }
            // set the sort order
            $query->query_vars['orderby'] = $orderby;
            $query->query_vars['meta_key'] = $meta_key;
        }
    }



    /**
    *  Filter Terms
    *  Filters the terms to be displayed based on Term Metadata.
    *  @param string $metakey
    *  @param mixed  $metavalue
    */
    function filterTerms( $metakey, $metavalue, $posttype)
    {

        // filter for selects
        add_filter( 'wp_terms_dropdown_args', function( $args ) use( $metakey, $metavalue, $posttype )
        {
          globa; $post;




          return $args;

        });

        // filter for checkboxes and radio
        add_filter( 'wp_terms_checklist_args', function( $args ) use( $metakey, $metavalue, $posttype )
        {
            global $post;

            // check for posttype and add get_terms filter
            if( get_post_type() == $posttype )
            {
                $this->metakey   = $metakey;
                $this->metavalue = $post->post_parent;
                add_filter( 'get_terms', [&$this, 'get_filtered_terms'], 10, 4 );
            }

            // don't affect wp_terms_checklist $args
            return $args;
        });
    }



    /**
    *  Get Filtered Terms
    *  Change get_terms query args inside wp_terms_checklist Callback Function.
    *  @param object  $terms
    *  @param array   $taxonomies
    *  @param array   $args
    *  @param object  $query
    *  @return object $terms
    */
    function get_filtered_terms( $terms, $taxonomies, $args, $term_query )
    {
        // remove filter after 1st run
        remove_filter( current_filter(), __FUNCTION__, 10, 4 );

        $args['meta_query'] = array(
            array(
              'key'     => $this->metakey,
              'value'   =>  sprintf(':"%s";', $this->metavalue),
              'compare' => 'LIKE',
            )
        );
        $terms = $term_query->query( $args );
        return $terms;
    }



    /**
    *  Modify Taxonomy Metabox
    *  Modify input type for Taxonomy metabox.
    *  @param string $type
    *  @param string $posttype
    */
    function modifyMetabox($type, $posttype)
    {
        //remove taxonomy meta box
        add_action( 'admin_menu', function() use( $posttype ){
          remove_meta_box( $this->name.'div', $posttype, 'normal');
        });

        //add taxonomy meta box
        add_action( 'add_meta_boxes', function() use( $type, $posttype ){
            add_meta_box( $this->name.'_id', $this->plural, [&$this, 'add_taxonomy_metabox'], $posttype, 'side', 'core', $type );
        });
    }



    /**
    *  Add Taxonomy Metabox
    *  Output Taxonomy metabox based on input type.
    *  @param object $post
    *  @param object $data
    */
    public function add_taxonomy_metabox( $post, $data )
    {
        // set name of the form
        $name      = 'tax_input[' . $this->name . ']';

        // get all terms
        $postterms = get_the_terms( $post->ID, $this->name );
        $current   = ($postterms ? array_pop($postterms) : false);
        $current   = ($current ? $current->term_id : 0);

        // get input type
        $type = $data['args'];

        // check taxonomy input type and display taxonomy terms
        switch( $type )
        {
          case 'radio': ?>
            <div id="taxonomy-<?php echo $this->name; ?>" class="categorydiv">
              <div id="<?php echo $this->name; ?>-all" class="tabs-panel">
                <ul id="<?php echo $this->name; ?>checklist" class="list:<?php echo $this->name ?> categorychecklist form-no-clear">
                  <?php
                  wp_terms_checklist( $post->ID, array(
                      'descendants_and_self'  => 0,
                      'popular_cats'          => false,
                      'echo'                  => true,
                      'taxonomy'              => $this->name,
                      'selected_cats'         => $current,
                      'checked_ontop'         => false,
                      'walker'                => new SoundlushWalkerRadioTaxonomy
                    ) );
                  ?>
                </ul>
              </div>
            </div>
            <?php
            break;

          case 'select': ?>
            <div id="taxonomy-<?php echo $this->name; ?>" class="categorydiv">
              <div id="<?php echo $this->name; ?>-all" class="tabs-panel">
                  <?php
                  //filtered included terms
                  $included_terms = get_terms(
                      array(
                          'taxonomy'   => $this->name,
                          'hide_empty' => '0',
                          'fields'     => 'ids',
                          'meta_query' => array(
                              array(
                                'key'     => 'term_meta',
                                'value'   =>  sprintf(':"%s";', $this->metavalue),
                                'compare' => 'LIKE',
                          ))
                      )
                  );

                  wp_dropdown_categories( array(
                      'show_option_all'    => '',
                      'show_option_none'   => 'Choose a '. $this->singular,
                      'orderby'            => 'slug',
                      'order'              => 'ASC',
                      'show_count'         => 0,
                      'hide_empty'         => 0,
                      'child_of'           => 0,
                      'exclude'            => '',
                      'include'            => $included_terms,
                      'echo'               => 1,
                      'selected'           => $current,
                      'hierarchical'       => 1,
                      'name'               => $name,
                      'id'                 => $name,
                      'class'              => 'widefat form-no-clear', //add class to fix style="margin: 1em 0"
                      'depth'              => 0,
                      'tab_index'          => 0,
                      'taxonomy'           => $this->name,
                      'hide_if_empty'      => false
                  ) );
                  ?>
              </div>
            </div>
            <?php
            break;
        }
    }



    /**
    *  Save Taxonomy Custom Meta
    *  Save extra taxonomy fields callback function.
    *  @param int $term_id
    */
    function save_taxonomy_custom_meta( $term_id )
    {
        $this->customfields->save_taxonomy_custom_meta($term_id);
    }


}
}
