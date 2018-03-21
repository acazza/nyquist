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

      foreach( $customfields as $customfield )
      {
        $wrapper  = 'tr';
        $start_th = '<th scope="row" valign="top">';
        $end_th   = '</th>';
        $start_td = '<td>';
        $end_td   = '</td>';

        // retrieve the existing value for this meta field.
        $term_meta = get_term_meta( $t_id, SoundlushHelpers::uglify( $customfield['id']), true );

        echo $this->create_html_markup($customfield, $wrapper, $term_meta, $start_th, $end_th, $start_td, $end_td  );
      }
    }



    /**
    *  Create Taxonomy Custom Fields Markup
    *  Outputs html for each custom field.
    *  @param
    */
    function create_html_markup( $args, $wrapper, $term_meta='', $start_th='', $end_th='', $start_td='', $end_td='' )
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

      $customfield = wp_parse_args( $args, $defaults );


      $label       = !empty( $customfield['label'] ) ? $customfield['label'] : $customfield['id'];
      $label       = SoundlushHelpers::beautify( $label );
      $id          = SoundlushHelpers::uglify( $customfield['id'] );
      $name        = '_' . $id;

      $required    = $customfield['required'] ? ' required' : '';
      $description = !empty( $customfield['desc'] ) ? '<p class="description">'.$customfield['desc'].'</p>' : '';

      // check if there is saved metadata for the field
      // if not use default value
      $meta        = isset( $term_meta )? $term_meta : $customfield['std'] ;


      $html = '<'. $wrapper .' class="form-field">';
      $html.= $start_th;
      $html.= '<label for="' . $id . '">' . $label . '</label>';
      $html.= $end_th . $start_td;

      switch( $customfield['type'] )
      {
        case 'text':

            $html.= '<input type="text" name="'.$name.'" id="'.$id.'" value="'.(isset( $meta ) ? esc_attr( $meta ) : '').'">';

            break;

        case 'relation':

            $posttype = post_type_exists( $customfield['posttype'] ) ? $customfield['posttype'] : '' ;
            $items    = query_posts( array( 'post_type' => $posttype, 'post_status' => 'publish' ) );

            $html.= '<select class="postform" name="'.$name.'" id="'.$id.'">';
            $html.= '<option value="0"'.( $meta == 0 ? '" selected="selected"' : '' ).' >Select a(n) '.SoundlushHelpers::beautify($posttype).'</option>';
            foreach ( $items as $item )
            {
              $html.= '<option value="'.$item->ID.'"'.( $meta == $item->ID ? '" selected="selected"' : '' ).' >'.$item->post_title.'</option>';
            }
            $html.= '</select>';
            break;

        default:
            break;
      }

      $html.= $description;
      $html.= $end_td;
      $html.= '</'. $wrapper .'>';

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

        // save custom fields
        if( isset( $_POST ) && ! empty( $t_id ) )
        {
          $fields = $this->customfields;

          if( $fields  && ! empty( $fields ) )
          {
            // sanitize fields
            foreach( $fields as $field )
            {
              if( isset( $_POST[$field['id']]) )
              {
                switch( $field['type'] )
                {
                  case 'text':
                    $new = sanitize_text_field( $_POST[$field['id']] );
                    break;
                  default:
                    $new = $_POST[$field['id']];
                    break;
                }
                // save term metadata
                update_term_meta($t_id, $field['id'], $new);
              }
            }
          }
        }
    }
  }
}
