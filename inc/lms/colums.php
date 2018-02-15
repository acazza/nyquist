<?php

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
 *
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
 *
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
