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



    /**
    *  Construct
    *  Initial code upon object creation.
    *  @param string $taxonomy_name
    */
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
        $wrapper = 'div';
         echo $this->create_html_markup($customfield, $wrapper);
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
        $wrapper  = 'tr';
        $start_th = '<th scope="row" valign="top">';
        $end_th   = '</th>';
        $start_td = '<td>';
        $end_td   = '</td>';

        echo $this->create_html_markup($customfield, $wrapper, $term_meta, $start_th, $end_th, $start_td, $end_td  );
      }
    }



    /**
    *  Create Taxonomy Custom Fields Markup
    *  Outputs html for each custom field.
    *  @param
    */
    function create_html_markup( $customfield, $wrapper, $term_meta='', $start_th='', $end_th='', $start_td='', $end_td='' )
    {

      $id          = SoundlushHelpers::uglify( $customfield['id'] );
      $name        = SoundlushHelpers::beautify( $customfield['name'] );
      $type        = isset( $customfield['type'] ) ? $customfield['type'] : 'text';
      $required    = ( isset( $customfield['required'] ) && $customfield['required'] ) ? ' required' : '';
      $description = isset( $customfield['desc'] ) ? '<p class="description">' . $customfield['desc'] . '</p>' : '';
      $standard    = isset( $customfield['std'] ) ? $customfield['std'] : '';

      // check if there is saved metadata for the field, if not use default value
      $meta        = isset( $term_meta[0][ $id ] )? $term_meta[0][ $id ] : $standard ;

      $html        = '<'. $wrapper .' class="form-field">';

      switch( $type )
      {
        case 'text':
            $html .= $start_th;
            $html .= '<label for="term_meta_' . $id . '">' . $name . '</label>';
            $html .= $end_th . $start_td;
            $html .= '<input type="text" name="term_meta['. $id . ']" id="term_meta_'. $id . '" value="' . (isset( $meta ) ? esc_attr( $meta ) : '') . '">';
            $html .= $description;
            $html .= $end_td;
            break;

        case 'relation':
            $posttype = ( isset( $customfield['posttype'] ) && post_type_exists( $customfield['posttype'] ) ) ? $customfield['posttype'] : '' ;
            $items = query_posts(array('post_type' => $posttype) );

            $html .= $start_th;
            $html .= '<label for="term_meta_' . $id . '">' . $name . '</label>';
            $html .= $end_th . $start_td;
            $html .= '<select class="postform" name="term_meta['. $id . ']" id="term_meta_'. $id . '">';
            $html .= '<option value="0"' . ( $meta == 0 ? '" selected="selected"' : '' ) .  ' >Choose a(n) ' . SoundlushHelpers::beautify($posttype) . '</option>';
            foreach ( $items as $item ) {
              $html .= '<option value="' . $item->ID . '"' . ( $meta == $item->ID ? '" selected="selected"' : '' ) .  ' >' . $item->post_title . '</option>';
            }
            $html .= '</select>' . $description;
            $html .= $end_td;
            break;

        default:
            break;
      }

      $html .= '</'. $wrapper .'>';

      return $html;

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

        // get term id
        $t_id = $term_id;


        if( isset( $_POST['term_meta'] ) && ! empty( $t_id ) )
        {
          $term_meta = $_POST['term_meta'];
          $fields = $this->customfields;

          // sanitize fields
          foreach( $fields as $field )
          {
              switch( $field['type'] )
              {
                case 'text':
                 $new = sanitize_text_field( $term_meta[ $field['id'] ] );
                 $term_meta[ $field['id'] ] = $new;
                  break;
                default:
                  break;
              }
            //update_term_meta($t_id, $field['id'], 'orange');
          }
          // save term metadata
          update_term_meta( $t_id, 'term_meta', $term_meta );

          //update_term_meta( $t_id, 'term_meta', $_POST['term_meta'] );

        }


    }
  }
}
