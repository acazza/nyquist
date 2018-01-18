<?php

if( !class_exists( 'SoundlushRadioTaxonomy' ) ) {

  class SoundlushRadioTaxonomy {

    public static $taxonomy;
    public static $post_type;

    public function __construct( $post_type_slug, $taxonomy_slug )
    {
      self::$taxonomy = $taxonomy_slug;
      self::$post_type = $post_type_slug;

      if ( empty( self::$post_type ) || empty( self::$taxonomy ) ){
        return;
      //} elseif( is_object_in_taxonomy(self::$post_type, self::$taxonomy) ) { //not working
      } else {
        //$this->load();
      }
    }

    function load()
    {
        add_action( 'admin_menu', array( &$this, 'remove_meta_box' ) );
        add_action( 'add_meta_boxes', array( &$this, 'add_meta_box' ) );
    }

    //Remove taxonomy meta box
    function remove_meta_box()
    {
        //The taxonomy metabox ID. This is different for non-hierarchical taxonomies
        $tax_mb_id = self::$taxonomy.'div';
        remove_meta_box($tax_mb_id, self::$post_type, 'normal');
    }

    //Add new taxonomy meta box
    function add_meta_box()
    {
        add_meta_box( 'my_tax', self::$taxonomy, array( &$this, 'populate_meta_box' ), self::$post_type ,'side','core');
    }

    //Callback to set up metabox
    function populate_meta_box( $post )
    {
        //Get taxonomy and terms
        $taxonomy = self::$taxonomy;
        $tax = get_taxonomy($taxonomy);
        $name = 'tax_input[' . $taxonomy . ']';
        $terms = get_terms( $taxonomy ,array('hide_empty' => 0));

        //Get current terms
        $postterms = get_the_terms( $post->ID,$taxonomy );
        $current = ($postterms ? array_pop($postterms) : false);
        $current = ($current ? $current->term_id : 0);
        ?>

        <!-- Output html -->
        <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">

            <!-- Display taxonomy terms -->
            <div id="<?php echo $taxonomy; ?>-all" class="tabs-panel">
                <ul id="<?php echo $taxonomy; ?>checklist" class="list:<?php echo $taxonomy?> categorychecklist form-no-clear">
                  <?php   foreach($terms as $term){
                      $id = $taxonomy.'-'.$term->term_id;
                      echo "<li id='$id'><label class='selectit'>";
                      echo "<input type='radio' id='in-$id' name='{$name}'".checked($current,$term->term_id,false)."value='$term->term_id' />$term->name<br />";
                      echo "</label></li>";
                  }?>
                </ul>
            </div>
        </div>
        <?php
    }
  }
}
