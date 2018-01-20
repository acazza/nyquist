<?php

/**
 * Soundlush Custom Post Types Relantionships Class
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 * @package soundlush
 */

if( !class_exists( 'SoundlushCustomPostRelationships') )
{

  class SoundlushCustomPostRelationships
  {

    public $post_type_parent;
    public $post_type_children;


    public function __construct( $parent, $children )
    {

      // Set some important variables
      $this->post_type_parent    = SoundlushHelpers::uglify( $parent );
      $this->post_type_children  = SoundlushHelpers::uglify( $children );


      // Add filter to children post type parent attribute, if all post types exist
      // TODO Check if post types exist
      // Perform filter on 'Edit $child' page
      add_filter( 'page_attributes_dropdown_pages_args', array( &$this, 'relate' ), 10, 2 );
      // Also perform the same filter when doing a 'Quick Edit'
      add_filter( 'quick_edit_dropdown_pages_args', array( &$this, 'relate' ), 10, 2 );
      // Clean up permalink
      add_filter( 'pre_get_posts', array( &$this, 'clean_permalink' ) );
    }



    public function relate($dropdown_args){

        global $post;

        if ( $this->post_type_children == $post->post_type ){
          $dropdown_args['post_type'] = $this->post_type_parent;
        }
        return $dropdown_args;

    }



    public function clean_permalink( $query )
    {
      // run this code only when we are on the public archive
      if( ( isset($query->query_vars['post_type']) && $this->post_type_children != $query->query_vars['post_type']) || ! $query->is_main_query() || is_admin() ):

        return;

      endif;

      // fix query for hierarchical lesson permalinks
      if ( isset( $query->query_vars['name']) && isset( $query->query_vars[$this->post_type_children] ) ):

        // remove the parent name
        $query->set( 'name', basename( untrailingslashit( $query->query_vars['name'] ) ));

        // unset this
        $query->set( $this->post_type_children, null );

      endif;

    }

  }

}
