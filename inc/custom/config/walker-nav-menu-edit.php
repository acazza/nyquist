<?php
/**
 * Soundlush Wordpress Custom Walker for Nav Menu Editor
 * @used-by ../admin.php
 * @package com.soundlush.theme.v1
 */


class MegaMenu_Walker_Nav_Menu_Edit extends Walker_Nav_Menu_Edit{


  /**
   * Start the element output.
   * We're injecting our custom fields after the div.submitbox
   *
   * @see Walker_Nav_Menu::start_el()
   * @since 1.0.0
   */

  function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ){
    $item_output = '';

    parent::start_el( $item_output, $item, $depth, $args, $id );

    // NOTE: Check this regex from time to time!
    $output .= preg_replace( '/(?=<(fieldset|p)[^>]+class="[^"]*field-move)/', $this->get_fields( $item, $depth, $args ), $item_output );
  }


  /**
   * Get custom fields
   *
   * @access protected
   * @since 1.0.0
   * @uses add_action() Calls 'menu_item_custom_fields' hook
   * @return string Form fields
   */

  protected function get_fields( $item, $depth, $args = array(), $id = 0 ){
    // whatever we write in between ob methods will not be echoed right away
    // it will be injected in the regular class code
    ob_start();

    do_action( 'wp_nav_menu_item_custom_fields', $item->ID, $item, $depth, $args, $id  );

    return ob_get_clean();
  }

}
