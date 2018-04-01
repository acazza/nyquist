<?php


if( ! class_exists('SoundlushWalkerRadioTaxonomy') )
{
class SoundlushWalkerRadioTaxonomy extends Walker
{
  var $tree_type = 'category';
  var $db_fields = array( 'parent'=>'parent', 'id'=>'term_id' );

  function start_lvl( &$output, $depth = 0, $args = array() )
  {
    $indent  = str_repeat( "\t", $depth );
    $output .= "$indent<ul class='children'>\n";
  }

  function end_lvl( &$output, $depth = 0, $args = array() )
  {
    $indent  = str_repeat( "\t", $depth );
    $output .= "$indent</ul>\n";
  }

  function start_el( &$output, $category, $depth = 0, $args = array(), $current_object_id = 0 )
  {
    extract( $args );
    if ( empty( $taxonomy ) )
      $taxonomy = 'category';

    if( $taxonomy == 'category' ) :
      $name = 'post_category';
    else :
      $name = 'tax_input[' . $taxonomy . ']';
    endif;

    $output .= "\n<ul>";
    $output .= "<li id='{$taxonomy}-{$category->term_id}'>";
    $output .= '<label class="selectit"><input value="' . $category->term_id . '" type="radio" name="'.$name.'" id="in-'.$taxonomy.'-' . $category->term_id . '"' . checked( in_array( $category->term_id, $selected_cats ), true, false ) . ' />'. esc_html( apply_filters('the_category', $category->name ));
    $output .= '</label></li></ul>';
  }

  function end_el( &$output, $category, $depth = 0, $args = array() )
  {
    $output .= "</li>\n";
  }

}
}
