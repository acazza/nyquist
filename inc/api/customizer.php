<?php
/**
 * Soundlush Wordpress Customizer
 * All our sections, settings, and controls will be added here
 * @link https://codex.wordpress.org/Theme_Customization_API
 *
 * @package com.soundlush.theme.v1
 */

function soundlush_customize_register( $wp_customize )
{

  /**
   * Header Section
   */

   $wp_customize->add_section( 'soundlush_custom_hero' , array(
    'title'      => __( 'Hero', 'soundlush' ),
    'priority'   => 30,
  ) );

  // Headline

  $wp_customize->add_setting( 'soundlush_custom_hero_headline' , array(
    'default'   => 'Create professional sounding music',
    'transport' => 'refresh',
  ) );

  $wp_customize->add_control( 'soundlush_custom_hero_headline_ctrl', array(
    'label'      => __( 'Headline Text', 'soundlush' ),
    'type'       => 'text',
    'section'    => 'soundlush_custom_hero',
    'settings'   => 'soundlush_custom_hero_headline',
  ) );

  // Lead

  $wp_customize->add_setting( 'soundlush_custom_hero_lead' , array(
    'default'   => 'Our mission is to teach you how',
    'transport' => 'refresh',
  ) );

  $wp_customize->add_control( 'soundlush_custom_hero_lead_ctrl', array(
    'label'      => __( 'Lead Text', 'soundlush' ),
    'type'       => 'textarea',
    'section'    => 'soundlush_custom_hero',
    'settings'   => 'soundlush_custom_hero_lead',
  ) );
}

add_action( 'customize_register', 'soundlush_customize_register' );
