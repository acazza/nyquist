<?php

if( ! class_exists( 'SoundlushTaxonomyMeta' ) )
{
  class SoundlushTaxonomyMeta
  {

    /**
     * The name for the Taxonomy
     * @var string
     */
    public $taxonomy_name;

    /**
     * Array of Custom Field
     * @var array
     */
    public $customfields;




    public function __construct($taxonomy_name){
      $this->taxonomy_name = $taxonomy_name;
    }



    /**
    *  Add
    *  Add Hooks for Taxonomy Meta Field.
    *  @param object $term
    */
    function add( $customfields )
    {
        $this->customfields = $customfields;

        // add custom field(s) to add new Taxonomy page
        add_action( "{$this->taxonomy_name}_add_form_fields", [&$this, 'add_new_taxonomy_meta_field'], 10, 2 );
        // add custom field(s) to edit Taxonomy page
        add_action( "{$this->taxonomy_name}_edit_form_fields", [&$this, 'edit_taxonomy_meta_field'], 10, 2 );
    }



    /**
    *  Add New Taxonomy Meta Field
    *  Add the custom meta field to the add new term page.
    *  @param object $term
    */
    function add_new_taxonomy_meta_field()
    {
      $customfields = $this->customfields;

      wp_nonce_field( basename( __FILE__ ), 'custom_taxmeta_nonce' );

      foreach( $customfields as $customfield )
      {
        $name        = SoundlushHelpers::beautify( $customfield['name'] );
        $id          = SoundlushHelpers::uglify( $customfield['id'] );
        $type        = isset( $customfield['type'] ) ? $customfield['type'] : 'text';
        $required    = ( isset( $customfield['required'] ) && $customfield['required'] ) ? ' required' : '';
        $description = isset( $customfield['desc'] ) ? '<span class="description">' . $customfield['desc'] . '</span>' : '';
        $standard    = isset( $customfield['std'] ) ? $customfield['std'] : '';

        $html        = '<div class="form-field">';

        switch( $type )
        {
          case 'text':
          	$html .= '<label for="term_meta_' . $id . '">' . $name . '</label>';
          	$html .= '<input type="text" name="term_meta['. $id . ']" id="term_meta_'. $id . '" value="">';
          	$html .= $description;
            break;

          case 'relation':
            $posttype  = ( isset( $customfield['posttype'] ) && post_type_exists( $customfield['posttype'] ) ) ? $customfield['posttype'] : '' ;
            $items = query_posts(array('post_type' => $posttype) );

            $html .= '<label for="term_meta_' . $id . '">' . $name . '</label>';
            $html .= '<select class="postform" name="term_meta['. $id . ']" id="term_meta_'. $id . '">';
              $html .= '<option value="0" >Choose a(n) ' . SoundlushHelpers::beautify($posttype) . '</option>';
            foreach ( $items as $item ) {
              $html .= '<option value="' . $item->ID . '" >' . $item->post_title . '</option>';
            }
            $html .= '</select></br>' . $description;
            break;

          default:
            break;
        }

        $html .= '</div>';
        echo $html;
      }

    }



    /**
    *  Edit Taxonomy Meta Field
    *  Edit term page.
    *  @param object $term
    */

    function edit_taxonomy_meta_field($term)
    {

      $customfields = $this->customfields;

      wp_nonce_field( basename( __FILE__ ), 'custom_taxmeta_nonce' );

      // put the term ID into a variable
      $t_id = $term->term_id;

      // retrieve the existing value(s) for this meta field. This returns an array
      $term_meta = get_term_meta( $t_id, 'term_meta', false );

      foreach( $customfields as $customfield )
      {
        $name        = SoundlushHelpers::beautify( $customfield['name'] );
        $id          = SoundlushHelpers::uglify( $customfield['id'] );
        $type        = isset( $customfield['type'] )? $customfield['type'] : 'text';
        $standard    = isset( $customfield['std'] )? $customfield['std'] : '';
        $required    = ( isset( $customfield['required'] ) && $customfield['required'] ) ? ' required' : '';
        $description = isset( $customfield['desc'] ) ? '<span class="description">' . $customfield['desc'] . '</span>' : '';


        // check if there is saved metadata for the field, if not use default value
        $meta        = isset( $term_meta[0][ $id ] )? $term_meta[0][ $id ] : $standard ;

        $html        = '<tr class="form-field">';

        switch ($type) {
          case 'text':
            $html .= '<th scope="row" valign="top">';
            $html .= '<label for="term_meta_' . $id . '">' .$name . '</label>';
            $html .= '</th><td>';
            $html .= '<input type="text" name="term_meta[' .$id . ']" id="term_meta_' . $id . '" value="' . (isset( $meta ) ? esc_attr( $meta ) : '') . '">';
            $html .= '</br>' . $description . '</td>';
            break;

          case 'relation':
            $posttype  = ( isset( $customfield['posttype'] ) && post_type_exists( $customfield['posttype'] ) ) ? $customfield['posttype'] : '' ;
            $items = query_posts(array('post_type' => $posttype) );

            $html .= '<th scope="row" valign="top">';
            $html .= '<label for="term_meta_' . $id . '">' .$name . '</label>';
            $html .= '</th><td>';
            $html .= '<select class="postform" name="term_meta['. $id . ']" id="term_meta_'. $id . '">';
              $html .= '<option value="0"' . ( $meta == 0 ? '" selected="selected"' : '' ) .  ' >Choose a(n) ' . SoundlushHelpers::beautify($posttype) . '</option>';
            foreach ( $items as $item ) {
              $html .= '<option value="' . $item->ID . '"' . ( $meta == $item->ID ? '" selected="selected"' : '' ) .  ' >' . $item->post_title . '</option>';
            }
            $html .= '<select></br>' . $description . '</td>';

            break;


          default:
            break;
        }

        $html .= '</tr>';
        echo $html;

      }
    }



    /**
    *  Save Taxonomy Custom Meta
    *  Save extra taxonomy fields callback function.
    *  @param int $term_id
    */
    function save_taxonomy_custom_meta( $term_id )
    {
        // verify nonce
        if( !isset($_POST['custom_taxmeta_nonce']) || !wp_verify_nonce( $_POST['custom_taxmeta_nonce'], basename(__FILE__) ) ) return;

        $t_id = $term_id;

        // save term metadata
        if( isset( $_POST['term_meta'] ) && ! empty( $t_id ) )
        {
          update_term_meta( $t_id, 'term_meta', $_POST['term_meta'] );
        }
    }


  }
}
