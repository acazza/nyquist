<?php
/**
 * Nyquist Wordpress Customizer
 * All our sections, settings, and controls will be added here
 * @link https://codex.wordpress.org/Theme_Customization_API
 *
 * @package Nyquist
 */

function nyquist_customize_register( $wp_customize )
{

  /**
   * Header Section
   */

   $wp_customize->add_section( 'nyquist_custom_hero' , array(
    'title'      => __( 'Hero', 'nyquist' ),
    'priority'   => 30,
  ) );

  // Headline

  $wp_customize->add_setting( 'nyquist_custom_hero_headline' , array(
    'default'   => 'Create professional sounding music',
    'transport' => 'refresh',
  ) );

  $wp_customize->add_control( 'nyquist_custom_hero_headline_ctrl', array(
    'label'      => __( 'Headline Text', 'nyquist' ),
    'type'       => 'text',
    'section'    => 'nyquist_custom_hero',
    'settings'   => 'nyquist_custom_hero_headline',
  ) );

  // Lead

  $wp_customize->add_setting( 'nyquist_custom_hero_lead' , array(
    'default'   => 'Our mission is to teach you how',
    'transport' => 'refresh',
  ) );

  $wp_customize->add_control( 'nyquist_custom_hero_lead_ctrl', array(
    'label'      => __( 'Lead Text', 'nyquist' ),
    'type'       => 'textarea',
    'section'    => 'nyquist_custom_hero',
    'settings'   => 'nyquist_custom_hero_lead',
  ) );
}

add_action( 'customize_register', 'nyquist_customize_register' );
