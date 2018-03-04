<?php

/**
 * Soundlush Custom Post Types and Taxonomy
 *
 * @link http://codex.wordpress.org/Function_Reference/register_post_type
 *
 * @package com.soundlush.theme.v1
 */

/* Courses Post Type */
function soundlush_course_posttype()
{
  $labels = array(
      'name'                  => _x( 'Courses', 'Post type general name', 'soundlush' ),
      'singular_name'         => _x( 'Course', 'Post type singular name', 'soundlush' ),
      'menu_name'             => _x( 'Courses', 'Admin Menu text', 'soundlush' ),
      'name_admin_bar'        => _x( 'Course', 'Add New on Toolbar', 'soundlush' ),
      'add_new'               => __( 'Add New', 'soundlush' ),
      'add_new_item'          => __( 'Add New Course', 'soundlush' ),
      'new_item'              => __( 'New Course', 'soundlush' ),
      'edit_item'             => __( 'Edit Course', 'soundlush' ),
      'view_item'             => __( 'View Course', 'soundlush' ),
      'all_items'             => __( 'All Courses', 'soundlush' ),
      'search_items'          => __( 'Search Courses', 'soundlush' ),
      'parent_item_colon'     => __( 'Parent Courses:', 'soundlush' ),
      'not_found'             => __( 'No courses found.', 'soundlush' ),
      'not_found_in_trash'    => __( 'No courses found in Trash.', 'soundlush' ),
      'featured_image'        => _x( 'Course cover image', 'soundlush' ),
      'set_featured_image'    => _x( 'Set cover image', 'soundlush' ),
      'remove_featured_image' => _x( 'Remove cover image', 'soundlush' ),
      'use_featured_image'    => _x( 'Use as cover image', 'soundlush' ),
      'archives'              => _x( 'Course archives', 'soundlush' ),
      'insert_into_item'      => _x( 'Insert into course', 'soundlush' ),
      'uploaded_to_this_item' => _x( 'Uploaded to this course', 'soundlush' ),
      'filter_items_list'     => _x( 'Filter courses list', 'soundlush' ),
      'items_list_navigation' => _x( 'Courses list navigation', 'soundlush' ),
      'items_list'            => _x( 'Courses list', 'soundlush' ),
  );

  $args = array(
    'labels'             => $labels,
    'label'              => 'Courses',
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'courses' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => true,
    'menu_position'      => 5,
    'menu_icon'          => 'dashicons-welcome-learn-more',
    'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt'),
  );

  $post_type = 'course';
  register_post_type( $post_type, $args );
}


/* Lessons Post Type */
function soundlush_lesson_posttype()
{
  $labels = array(
    'name'                  => _x( 'Lessons', 'Post type general name', 'soundlush' ),
    'singular_name'         => _x( 'Lesson', 'Post type singular name', 'soundlush' ),
    'menu_name'             => _x( 'Lessons', 'Admin Menu text', 'soundlush' ),
    'name_admin_bar'        => _x( 'Lesson', 'Add New on Toolbar', 'soundlush' ),
    'add_new'               => __( 'Add New', 'soundlush' ),
    'add_new_item'          => __( 'Add New Lesson', 'soundlush' ),
    'new_item'              => __( 'New Lesson', 'soundlush' ),
    'edit_item'             => __( 'Edit Lesson', 'soundlush' ),
    'view_item'             => __( 'View Lesson', 'soundlush' ),
    'all_items'             => __( 'All Lessons', 'soundlush' ),
    'search_items'          => __( 'Search Lessons', 'soundlush' ),
    'parent_item_colon'     => __( 'Parent Lessons:', 'soundlush' ),
    'not_found'             => __( 'No lessons found.', 'soundlush' ),
    'not_found_in_trash'    => __( 'No lessons found in Trash.', 'soundlush' ),
    'featured_image'        => _x( 'Lesson cover image', 'soundlush' ),
    'set_featured_image'    => _x( 'Set cover image', 'soundlush' ),
    'remove_featured_image' => _x( 'Remove cover image', 'soundlush' ),
    'use_featured_image'    => _x( 'Use as cover image', 'soundlush' ),
    'archives'              => _x( 'Lesson archives', 'soundlush' ),
    'insert_into_item'      => _x( 'Insert into lesson', 'soundlush' ),
    'uploaded_to_this_item' => _x( 'Uploaded to this lesson', 'soundlush' ),
    'filter_items_list'     => _x( 'Filter lessons list', 'soundlush' ),
    'items_list_navigation' => _x( 'Lessons list navigation', 'soundlush' ),
    'items_list'            => _x( 'Lessons list', 'soundlush' ),
  );

  $args = array(
    'labels'             => $labels,
    'label'              => 'Lessons',
    'public'             => true,
    'publicly_queryable' => true,
    'show_ui'            => true,
    'show_in_menu'       => true,
    'query_var'          => true,
    'rewrite'            => array( 'slug' => 'lessons' ),
    'capability_type'    => 'post',
    'has_archive'        => true,
    'hierarchical'       => true,
    'menu_position'      => 6,
    'menu_icon'          => 'dashicons-edit',
    'supports'           => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'page-attributes'),
  );

  $post_type = 'lesson';
  register_post_type( $post_type, $args );
}


/* Alow courses to be parents of lessons */
function lesson_attributes_dropdown_pages_args( $dropdown_args )
{
  global $post;
  if ( 'lesson' == $post->post_type ):
    $dropdown_args['post_type'] = 'course';
  endif;
  return $dropdown_args;
}
// Perform filter on 'Edit Lesson' page
add_filter( 'page_attributes_dropdown_pages_args', 'lesson_attributes_dropdown_pages_args', 10, 2 );
// Also perform the same filter when doing a 'Quick Edit'
add_filter( 'quick_edit_dropdown_pages_args', 'lesson_attributes_dropdown_pages_args', 10, 2 );



/* Clean up permalink */
function lesson_query( $query )
{
  // run this code only when we are on the public archive
  if( ( isset($query->query_vars['post_type']) && 'lesson' != $query->query_vars['post_type']) || ! $query->is_main_query() || is_admin() ):
    return;
  endif;
  // fix query for hierarchical lesson permalinks
  if ( isset( $query->query_vars['name']) && isset( $query->query_vars['lesson'] ) ):
    // remove the parent name
    $query->set( 'name', basename( untrailingslashit( $query->query_vars['name'] ) ));
    // unset this
    $query->set( 'lesson', null );
  endif;
}
add_filter( 'pre_get_posts', 'lesson_query' );



/* Create Custom Taxonomies */
function soundlush_custom_taxonomies()
{
	// Subject (hierarchical)
	$labels = array(
		'name'              => _x( 'Subjects', 'taxonomy general name', 'soundlush' ),
		'singular_name'     => _x( 'Subject', 'taxonomy singular name', 'soundlush' ),
		'search_items'      => __( 'Search Subjects', 'soundlush' ),
		'all_items'         => __( 'All Subjects', 'soundlush' ),
		'parent_item'       => __( 'Parent Subject', 'soundlush' ),
		'parent_item_colon' => __( 'Parent Subject:', 'soundlush' ),
		'edit_item'         => __( 'Edit Subject', 'soundlush' ),
		'update_item'       => __( 'Update Subject', 'soundlush' ),
		'add_new_item'      => __( 'Add New Subject', 'soundlush' ),
		'new_item_name'     => __( 'New Subject Name', 'soundlush' ),
		'menu_name'         => __( 'Subject', 'soundlush' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
		'rewrite'           => array( 'slug' => 'subject' ),
	);

	register_taxonomy( 'subject', array( 'course' ), $args );



  // Module (hierarchical)
	$labels = array(
		'name'              => _x( 'Modules', 'taxonomy general name', 'soundlush' ),
		'singular_name'     => _x( 'Module', 'taxonomy singular name', 'soundlush' ),
		'search_items'      => __( 'Search Modules', 'soundlush' ),
		'all_items'         => __( 'All Modules', 'soundlush' ),
		'parent_item'       => __( 'Parent Module', 'soundlush' ),
		'parent_item_colon' => __( 'Parent Module:', 'soundlush' ),
		'edit_item'         => __( 'Edit Module', 'soundlush' ),
		'update_item'       => __( 'Update Module', 'soundlush' ),
		'add_new_item'      => __( 'Add New Module', 'soundlush' ),
		'new_item_name'     => __( 'New Module Name', 'soundlush' ),
		'menu_name'         => __( 'Module', 'soundlush' ),
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'query_var'         => true,
    'public'            => false,
		'rewrite'           => false //array( 'slug' => 'module' ),
	);

	register_taxonomy( 'module', array( 'lesson' ), $args );
}



/* Call all functions that create Custom Post Types & Taxonomies */
function soundlush_custom_posttype()
{
  soundlush_course_posttype();
  soundlush_lesson_posttype();
  soundlush_custom_taxonomies();
}
add_action( 'init', 'soundlush_custom_posttype' );



/* Flush rewrite rules for custom post types on theme activation. */
add_action( 'after_switch_theme', 'flush_rewrite_rules' );



/* Check if a post belongs to a specific custom post type $type*/
function is_post_type( $type )
{
    global $wp_query;
    if($type == get_post_type($wp_query->post->ID)) return true;
    return false;
}


// function buildTaxonomyTree($items) {
//   $childs = array();
//   foreach($items as &$item) {
//     $childs[$item->parent][] = &$item;
//   }
//   unset($item);
//
//   foreach($items as &$item) {
//     if (isset($childs[$item->term_id])){
//       $item->children = $childs[$item->term_id];
//     }
//   }
//   return $childs[0];
// }



/* Format Taxonomy Tree - Recursive Function */
function soundlush_format_taxonomy_tree( $taxonomies, $level = 0) {
  $output = '';
  foreach( $taxonomies as $taxonomy ):
    if( $taxonomy->parent == $level ):
      $output .= '<li>';
      $output .= $taxonomy->name;
      $output .= soundlush_format_post_tree( $taxonomy->term_id );
      $output .= soundlush_format_taxonomy_tree( $taxonomies, $taxonomy->term_id );
      $output .= '</li>';
    endif;
  endforeach;

  return ( $output == '' ? '' : $output = '<ul>' . $output . '</ul>');
}



/* Format Post Tree - Complementary */
function soundlush_format_post_tree($term_ID){

  global $post;
  if( is_post_type( 'course' ) ){
  $parent_ID = $post->ID;
  }elseif( is_post_type( 'lesson' ) ){
    $parent_ID = get_post_ancestors( $post->ID );
  }else{
    return;
  }

  // WP_Query arguments
  // Check if module have lessons that are children of current course
  $args = array (
    'post_type'       => 'lesson',
    'post_parent'     => $parent_ID,
    'fields'          => 'ids',
    'post_status'     => 'publish',
    'posts_per_page'  => '-1',
    'order'           => 'ASC',
    'orderby'         => 'menu_order',
    'tax_query'       => array(
      array(
        'taxonomy' => 'module',
        'field'    => 'term_id',
        'terms' => $term_ID,
      ),
    ),
  );

  // The Query
  $query = new WP_Query( $args );
  $output = '';

  // The Loop
  if ( $query->have_posts() ):
    while ( $query->have_posts() ):
      $query->the_post();

      $post_id = get_the_ID();
      $post_name = get_the_title();
      $post_link = get_the_permalink();

      // If module has children modules, only assign lesson to the lowest level module
      $post_term = soundlush_get_lowest_level_taxonomy( $post_id );
      if( $term_ID == $post_term ) {
        $output .= '<li><a href="' . $post_link . '">'. $post_name . '</a></li>';
      }
    endwhile;
  endif;

  wp_reset_postdata();

  return ( $output == '' ? '' : $output = '<ul>' . $output . '</ul>');
}



/* Get lowest taxonomy level term associated with a post */
function soundlush_get_lowest_level_taxonomy($post_id){

  // Get all taxonomy terms from a lesson
  $post_terms = get_the_terms( $post_id,'module' );
  $parents = array();

  if( $post_terms ){
  	foreach ( $post_terms as $post_term ){
      // Save all ids in a array
  		$all[] = $post_term->term_id;
      // Save all parents in another array
  		if ( $post_term->parent ) $parents[] = $post_term->parent;
  	}
  }

  // Remove parents from original array
  $lowest_term =  array_diff( $all, $parents );
  // Format to list
  $lowest_term_id = array_shift( $lowest_term );

  return $lowest_term_id;
}



/* Returns all lessons of a course grouped by a hierarchy of module */
function soundlush_generate_course_index()
{
  echo '<h4>'. __( 'Course Outline', 'soundlush' ) . '</h4>';

  global $post;

  if( is_post_type('course') ) {
    $parent = $post->ID;
    $children = get_children( array(
    	'post_parent' => $parent,
    	'post_type'   => 'lesson',
    	'numberposts' => 1,
    	'post_status' => 'publish'
      )
    );
    $post_id = current($children)->ID;

  } elseif( is_post_type( 'lesson' ) ){
    $post_id = $post->ID;
  } else {
    return;
  }

  // Get lowest level term
  $lowest_term_id = soundlush_get_lowest_level_taxonomy($post_id);

  // Get term ascestors from lowest level term
  $term_ancestors = get_ancestors($lowest_term_id, 'module');
  //TODO loop
  // foreach ($ancestor as $ancestor):
  //    if( !( $ancestor->parent > 0 ) ){
  //        top_ancestor_id = $ancestor->id;
  //        break;
  //    }
  // endforeach;


  // Get all top level terms
  $top_level_terms = get_terms( 'module', array(
    'parent'        => 0,
    'fields'        => 'ids',
    'hide_empty'    => false,
  ));

  // Compare arrays and retrieve top level term ancestor for post
  $top_ancestor = array_intersect( $term_ancestors, $top_level_terms );
  $top_ancestor_id =array_shift($top_ancestor);

  // Get child terms from top ancestor
  $taxonomies = get_terms('module', array(
      'orderby'   => 'parent',
      'order'     => 'ASC',
      'child_of'   => $top_ancestor_id,
    )
  );

  // Call recursive function to generate indented list (Modules and Lessons)
  print soundlush_format_taxonomy_tree( $taxonomies, $top_ancestor_id );
}


/**
 * Re-order lessons by menu_order and filters per parent
 * for previous/next post navigation
 */
function soundlush_lesson_post_nav() {

	if ( is_singular( 'lesson' ) ) {
		global $post, $wpdb;

		// Section 1.
		function filter_next_post_sort( $sort ) {
			$sort = 'ORDER BY p.menu_order ASC LIMIT 1';
				return $sort;
		}

		function filter_next_post_where( $where ) {
			global $post, $wpdb;

			$where = $wpdb->prepare( "WHERE p.menu_order > '%s' AND p.post_type = 'lesson' AND p.post_status = 'publish' AND p.post_parent = '%s'",$post->menu_order, $post->post_parent);
			return $where;
		}

		function filter_previous_post_sort( $sort ) {
			$sort = 'ORDER BY p.menu_order DESC LIMIT 1';
				return $sort;
		}

		function filter_previous_post_where($where) {
			global $post, $wpdb;

			$where = $wpdb->prepare( "WHERE p.menu_order < '%s' AND p.post_type = 'lesson' AND p.post_status = 'publish'AND p.post_parent = '%s'",$post->menu_order, $post->post_parent);
			return $where;
		}

		add_filter( 'get_next_post_sort',   'filter_next_post_sort' );
		add_filter( 'get_next_post_where',  'filter_next_post_where' );

		add_filter( 'get_previous_post_sort',  'filter_previous_post_sort' );
		add_filter( 'get_previous_post_where', 'filter_previous_post_where' );

		// Section 2.
		$previous_post = get_previous_post();
		$next_post = get_next_post();

		echo '<div class="adjacent-entry-pagination pagination">';

		if ( $previous_post ) {
			echo '<div class="pagination-previous"><a href="' .get_permalink( $previous_post->ID ). '">&laquo; ' .$previous_post->post_title. '</a></div>';
		} else {
			echo '<div class="pagination-previous"><a href="' .get_post_type_archive_link( 'book-post' ). '">---</a></div>';
			}

		if ( $next_post ) {
  			echo '<div class="pagination-next"><a href="' .get_permalink( $next_post->ID ). '">' .$next_post->post_title. ' &raquo;</a></div>';
		} else {
			echo '<div class="pagination-next"><a href="' .get_post_type_archive_link( 'book-post' ). '">---</a></div>';
		}

		echo '</div>';

	}

}


/* Lesson Navigation Section */

//function soundlush_get_lesson_navigation()
//{
//    require( get_template_directory() . '/inc/templates/soundlush-lesson-nav.php' );
//}
