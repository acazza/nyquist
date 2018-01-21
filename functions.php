<?php
/**
 * Soundlush Theme Functions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package com.soundlush.theme.v1
 */

require get_template_directory() . '/inc/core/mega-walker.php';
require get_template_directory() . '/inc/core/sidebar.php';
require get_template_directory() . '/inc/api/customizer.php';
require get_template_directory() . '/inc/api/widgets.php';
require get_template_directory() . '/inc/api/shortcodes.php';
require get_template_directory() . '/inc/setup/theme-support.php';
require get_template_directory() . '/inc/setup/enqueue.php';
require get_template_directory() . '/inc/setup/menus.php';
require get_template_directory() . '/inc/setup/custom-post-types.php';
require get_template_directory() . '/inc/custom/admin.php';
require get_template_directory() . '/inc/custom/custom.php';
require get_template_directory() . '/inc/custom/icon-functions.php';
require get_template_directory() . '/inc/lms/custom-helpers.php';
require get_template_directory() . '/inc/lms/custom-post-types.php';
require get_template_directory() . '/inc/lms/custom-radio-taxonomy.php';
require get_template_directory() . '/inc/lms/custom_post_relationship.php';
require get_template_directory() . '/inc/lms/custom_dynamic.php';
require get_template_directory() . '/inc/lms/custom_post_tree.php';
require get_template_directory() . '/inc/lms/custom_post_nav.php';
require get_template_directory() . '/inc/lms/course-class.php';


/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) :
	require get_template_directory() . '/inc/plugins/jetpack.php';
endif;

/**
 * Load WooCommerce compatibility file.
 */
if ( class_exists( 'WooCommerce' ) ) :
	require get_template_directory() . '/inc/plugins/woocommerce.php';
endif;
