<?php
/**
 * Nyquist Walker Class Extention
 * MegaMenu
 * @link https://codex.wordpress.org/Class_Reference/Walker
 *
 * @package Nyquist
 */

class Walker_Nav_MegaMenu extends Walker_Nav_Menu
{

  public $megaMenuID;


  public function __contruct()
  {
    $this->megaMenuID = 0;
  }


  public function start_lvl( &$output, $depth = 0, $args = array() )
  {
    $indent = str_repeat( "\t", $depth );
    $submenu = ( $depth > 0 ) ? ' sub-menu' : '';
    $output .= "\n$indent<ul class=\"dropdown-menu$submenu depth_$depth\" >\n";

    if( ( $this->megaMenuID != 0 ) && ( $depth == 0 ) ):
      $output .= "<li class=\"megamenu-column-divider\"><ul>\n";
    endif;
  }


  public function end_lvl( &$output, $depth = 0, $args = array() )
  {
    if( ( $this->megaMenuID != 0 ) && ( $depth == 0 ) ):
      $output .= "</ul></li>";
    endif;

    $output .= "</ul>";
  }


  public function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 )
  {
      // check if option is checked for current menu item
      $hasMegaMenu = get_post_meta( $item->ID, 'menu-item-' . 'megamenu', true );
      $hasColumnDivider = get_post_meta( $item->ID, 'menu-item-' . 'megamenu-column-divider', true );
      $hasInlineDivider = get_post_meta( $item->ID, 'menu-item-' . 'megamenu-divider', true );
      $hasFeaturedImage = get_post_meta( $item->ID, 'menu-item-' . 'megamenu-featured-image', true );
      $hasDescription = get_post_meta( $item->ID, 'menu-item-' . 'megamenu-description', true );

      $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

      $li_attributes = '';
      $class_names = $value = '';

      $classes = empty( $item->classes ) ? array() : ( array ) $item->classes;

      if( ( $this->megaMenuID != 0 ) && ( $this->megaMenuID != intval( $item->menu_item_parent ) ) && ( $depth == 0 ) ):
        $this->megaMenuID = 0;
      endif;

      if ( $hasColumnDivider ):
        $output .= "</ul></li><li class=\"megamenu-column-divider\"><ul>\n";
      endif;

      // managing divider: add divider class to an element to get a divider before it.
      if ( $hasInlineDivider ):
          $output .= "<li class=\"megamenu-divider\"></li>\n";
      endif;

      if( $hasMegaMenu ):
        array_push($classes, 'megamenu');
        $this->megaMenuID = $item->ID;
      endif;

      if ( $hasFeaturedImage ):
        array_push($classes, 'megamenu-featured-image');
      endif;

      // hide <a> and display only item description
      if( $hasDescription ):
        array_push($classes, 'megamenu-description');
      endif;

      $classes[] = ( $args->has_children ) ? 'dropdown' : '';
      $classes[] = ( $item->current || $item->current_item_ancestor ) ? 'active' : '';
      $classes[] = 'menu-item-'.$item->ID;
      if ( $depth && $args->has_children ):
          $classes[] = 'dropdown-submenu';
      endif;

      $class_names = implode( ' ', apply_filters( 'nav_menu_css_class', array_filter( $classes ), $item, $args ) );
      $class_names = ' class="' . esc_attr( $class_names ).'"';

      $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
      $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

      $output .= $indent . '<li' . $id . $value . $class_names . $li_attributes . '>';

      $attributes = !empty( $item->attr_title ) ? ' title="' . esc_attr( $item->attr_title ) . '"' : '';
      $attributes .= !empty( $item->target ) ? ' target="' . esc_attr( $item->target ) . '"' : '';
      $attributes .= !empty( $item->xfn ) ? ' rel="' . esc_attr( $item->xfn ) . '"' : '';
      $attributes .= !empty( $item->url ) ? ' href="' . esc_attr( $item->url ) . '"' : '';
      $attributes .= ( $args->has_children ) ? ' class="dropdown-toggle" data-toggle="dropdown"' : '';

      $item_output = $args->before;
      $item_output .= '<a' . $attributes . '>';

      // check if item has featured image
      if ( $hasFeaturedImage && $this->megaMenuID != 0 ):
          $postID = url_to_postid( $item->url );
          $item_output .= '<img alt="' . esc_attr( $item->attr_title ) .  '"src="' . get_the_post_thumbnail_url( $postID ) . '"/>';
      endif;

      $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;

      // add support for menu item title
      if ( strlen( $item->attr_title ) > 2 ):
          $item_output .= '<h3 class="tit">' . $item->attr_title . '</h3>';
      endif;

      // add support for menu item descriptions
      if ( strlen( $item->description ) > 2 ):
          $item_output .= '</a> <span class="sub">' . $item->description . '</span>';
      endif;

      $item_output .= ( ( $depth == 0 || 1 ) && $args->has_children ) ? ' <b class="caret"></b></a>' : '</a>';
      $item_output .= $args->after;

      $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }


  public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output )
  {
      if ( !$element ):
          return;
      endif;

      $id_field = $this->db_fields['id'];

      //display this element
      if ( is_array( $args[0] ) ):
          $args[0]['has_children'] = !empty( $children_elements[$element->$id_field] );
      elseif ( is_object($args[0] ) ):
          $args[0]->has_children = !empty( $children_elements[$element->$id_field] );
      endif;
      $cb_args = array_merge( array( &$output, $element, $depth ), $args );
      call_user_func_array( array( $this, 'start_el' ), $cb_args );

      $id = $element->$id_field;

      // descend only when the depth is right and there are childrens for this element
      if ( ( $max_depth == 0 || $max_depth > $depth + 1 ) && isset( $children_elements[$id] ) ):
        foreach ( $children_elements[$id] as $child ):
          if ( !isset( $newlevel ) ):
            $newlevel = true;
            //start the child delimiter
            $cb_args = array_merge( array( &$output, $depth ), $args );
            call_user_func_array( array( $this, 'start_lvl' ), $cb_args );
          endif;
          $this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
        endforeach;
        unset( $children_elements[$id] );
      endif;

      if ( isset( $newlevel ) && $newlevel ):
        //end the child delimiter
        $cb_args = array_merge( array( &$output, $depth ), $args );
        call_user_func_array( array( $this, 'end_lvl' ), $cb_args );
      endif;

      //end this element
      $cb_args = array_merge( array( &$output, $element, $depth ), $args );
      call_user_func_array( array( $this, 'end_el' ), $cb_args );
  }

}