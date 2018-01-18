<?php

//Remove original meta box
add_action( 'admin_menu', 'remove_original_meta_box');

function remove_original_meta_box(){
  remove_meta_box('mytaxonomydiv', 'post', 'normal');
}

//Add new taxonomy meta box
add_action( 'add_meta_boxes', 'add_custom_meta_box');

function add_custom_meta_box() {
  add_meta_box( 'mytaxonomy_id', 'My Radio Taxonomy','create_radio_taxonomy_metabox','post' ,'side','core');
}

function create_radio_taxonomy_metabox( $post ) {

    //Get taxonomy and terms
    $taxonomy = 'mytaxonomy';

    //Set up the taxonomy object and get terms
    $tax = get_taxonomy($taxonomy);
    $terms = get_terms($taxonomy,array('hide_empty' => 0));

    //Name of the form
    $name = 'tax_input[' . $taxonomy . ']';

    $postterms = get_the_terms( $post->ID,$taxonomy );
    $current = ($postterms ? array_pop($postterms) : false);
    $current = ($current ? $current->term_id : 0);
    ?>

      <!-- Display taxonomy terms -->
      <div id="taxonomy-<?php echo $taxonomy; ?>" class="categorydiv">
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
