<?php
/**
 * Soundlush Icon Functions
 *
 * @package com.soundlush.theme.v1
 */

// add svg sprite at the bottom of the document
 function soundlush_include_svg_icons(){
    $svg_icons = get_template_directory() . '/assets/icon/inline.svg.php';
    if( file_exists( $svg_icons ) ):
      require_once( $svg_icons );
    endif;
 }
 add_action( 'wp_footer', 'soundlush_include_svg_icons', 9999 );



 function soundlush_get_svg( $args ) {

  // Make sure $args are an array.
	if ( empty( $args ) ):
		return __( 'Please define default parameters in the form of an array.', 'soundlush' );
	endif;

  // Define an icon.
	if ( false === array_key_exists( 'icon', $args ) ) {
		return __( 'Please define an SVG icon filename.', 'soundlush' );
	}
	// Set defaults.
	$defaults = array(
		'icon'        => '',
		'class'       => '',
	);
	// Parse args.
	$args = wp_parse_args( $args, $defaults );
	// Set aria hidden.
	$aria_hidden = ' aria-hidden="true"';
	// Set ARIA.
	$aria_labelledby = '';

	// Begin SVG markup.
	$output = '<svg class="icon icon-soundlush icon-' . esc_attr( $args['icon'] ) . ' ' . esc_attr( $args['class'] ) .'"' . $aria_hidden . $aria_labelledby . ' role="img">';


	/*
	 * Display the icon.
	 * The whitespace around `<use>` is intentional - it is a work around to a keyboard navigation bug in Safari 10.
	 * See https://core.trac.wordpress.org/ticket/38387.
	 */
	$output .= ' <use href="#icon-' . esc_html( $args['icon'] ) . '" xlink:href="#icon-' . esc_html( $args['icon'] ) . '"></use> ';
	$output .= '</svg>';
	return $output;
}


// subsitute - not sanitized
function soundlush_print_svg( $suffix ){
  if( $suffix ):
    $output = '<svg class="icon icon-soundlush icon-' . $suffix . '" aria-hidden="true" role="img"> <use href="#icon-' . $suffix . '" xlink:href="#icon-' . $suffix . '"></use></svg>';
    return $output;
  else:
    return;
  endif;
}
