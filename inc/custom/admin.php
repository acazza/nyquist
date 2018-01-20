<?php
/**
 * Soundlush Wordpress MegaMenu Admin Custom Fields
 * @uses config/walker-nav-menu-edit.php
 * @package com.soundlush.theme.v1
 */


// create fields
function soundlush_create_fields_list()
{
  //note that menu-item- gets prepended to field names
  return array(
    'megamenu'                => 'Activate MegaMenu',
    'megamenu-column-divider' => 'Column Divider',
    'megamenu-divider'        => 'Inline Divider',
    'megamenu-featured-image' => 'Featured Image',
    'megamenu-description'    => 'Description',
  );
}


// setup fields
function soundlush_setup_fields_list( $id, $item, $depth, $args )
{
  $fields = soundlush_create_fields_list();

  foreach ( $fields as $_key => $label ):
    $key = sprintf( 'menu-item-%s', $_key );
    $id = sprintf( 'edit-%s-%s', $key, $item->ID );
    $name = sprintf( '%s[%s]', $key, $item->ID );
    $value = get_post_meta( $item->ID, $key, true );
    $class = sprintf( 'field-%s', $_key );
    ?>
    <p class="description description-wide <?php echo esc_attr( $class ); ?>">
      <label for="<?php echo esc_attr( $id ); ?>">
        <input type="checkbox" id="<?php echo esc_attr( $id ); ?>" name="<?php echo esc_attr( $name ); ?>" value="1" <?php echo ( $value == 1 ) ? 'checked="checked"' : ''; ?>><?php echo esc_attr( $label ); ?></label>
    </p>
    <?php
  endforeach;
}
add_action( 'wp_nav_menu_item_custom_fields', 'soundlush_setup_fields_list', 10, 4 );


// show columns
function soundlush_show_columns( $columns )
{
  $fields = soundlush_create_fields_list();
  $columns = array_merge( $columns, $fields );
  return $columns;
}
add_filter( 'manage_nav-menus_columns', 'soundlush_show_columns', 99 );



// save/update fields
function soundlush_save_fields( $menu_id, $menu_item_db_id, $menu_item_args )
{
  // stop function if auto-save is in progress
  if( defined( 'DOING_AJAX' ) && DOING_AJAX ) :
    return;
  endif;

  // check if action is triggered from admin panel of your website
  check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );

  $fields = soundlush_create_fields_list();

  foreach ($fields as $_key => $label) :
    
    $key = sprintf( 'menu-item-%s', $_key );

    // sanitize fields (checkbox)
    if ( !empty( $_POST[$key][$menu_item_db_id] )  ) :
      $value = $_POST[$key][$menu_item_db_id];
    else :
      $value = null;
    endif;

    // update
    if( !is_null( $value ) ):
      update_post_meta( $menu_item_db_id, $key, $value );
    else :
      delete_post_meta( $menu_item_db_id, $key );
    endif;

  endforeach;
}
add_action( 'wp_update_nav_menu_item', 'soundlush_save_fields', 10, 3 );



/**
 * update Walker Nav Class
 * @uses config/walker-nav-menu-edit.php
 */

function soundlush_update_megamenu_walker_nav( $walker )
{
  $walker = 'MegaMenu_Walker_Nav_Menu_Edit';
  if( !class_exists( $walker ) ):
    require_once dirname(__FILE__) . '/config/walker-nav-menu-edit.php';
  endif;
  return $walker;
}
add_filter( 'wp_edit_nav_menu_walker', 'soundlush_update_megamenu_walker_nav', 99 );
