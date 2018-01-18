<?php
/**
 * Nyquist Shortcodes
 *
 * @link https://codex.wordpress.org/Shortcode_API
 *
 * @package Nyquist
 */

function nyquist_shortcode_button( $atts, $content = null ){
  // Get the attributes
  $atts = shortcode_atts(
    array(
        'slug'      => '#',
        'type'      => 'default',
        'outlined'  => false
    ),
    $atts,
    'button'
  );

  $outlined = ( $atts[ 'outlined' ] ) ? ' btn-outlined' : '';

  // Return html
  $output = '<a href="' . get_home_url() . '/' . $atts[ 'slug' ] . '/" class="btn btn-' . $atts[ 'type' ] . $outlined . '">';
  $output .= $content;
  $output .= '</a>';
  return $output;

}

add_shortcode( 'button', 'nyquist_shortcode_button' );
// [button slug="articles" type="primary" outlined=true]Click me[/button]



function nyquist_shortcode_card( $atts, $content = null ){
  // Get the attributes
  $atts = shortcode_atts(
    array(
        'cards_per_row' => 3, //Flex ???
        'icon'           => '',
        'title'          => '',
    ),
    $atts,
    'card'
  );
  $width = ( 100 / $atts[ 'cards_per_row' ] );

  // Return html
  $output .= '<div class = "individual-card" width = "' . $width . '%">';
  $output .= '<div class = "card-icon"' . nyquist_print_svg( $atts[ 'icon' ] ) . '</div>';
  $output .= '<h4>' . $atts[ 'title' ] . '</h4>';
  $output .= '<p>' . $content . '</p>';
  $output .= '</div> <!-- .individual-card --> ';
  return $output;
}

add_shortcode( 'card', 'nyquist_shortcode_card' );
// [card cards_per_row=3 icon="link" title="Conectivity"]We are the best ones[/card]


function nyquist_shortcode_backtotop( $atts, $content = null ){
  ( empty( $content ) ) ? 'Back to Top' : $content;
  // Return html
  $output = '<a href="#" class="back-to-top">' . $content . '</a>';
  return $output;
}

add_shortcode( 'backtotop', 'nyquist_shortcode_backtotop' );
// [backtotop]Back to Top[/backtotop]
