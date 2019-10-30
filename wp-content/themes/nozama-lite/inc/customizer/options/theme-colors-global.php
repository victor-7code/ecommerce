<?php
	$scss = new Nozama_Lite_SCSS_Colors( get_theme_file_path( '/css/inc/_variables.scss' ) );

	// Rename & Reposition the header image section.
	$wp_customize->get_control( 'background_color' )->section      = 'theme_colors_global';
	$wp_customize->get_control( 'background_image' )->section      = 'theme_colors_global';
	$wp_customize->get_control( 'background_preset' )->section     = 'theme_colors_global';
	$wp_customize->get_control( 'background_position' )->section   = 'theme_colors_global';
	$wp_customize->get_control( 'background_size' )->section       = 'theme_colors_global';
	$wp_customize->get_control( 'background_repeat' )->section     = 'theme_colors_global';
	$wp_customize->get_control( 'background_attachment' )->section = 'theme_colors_global';

	$wp_customize->add_setting( 'site_secondary_accent_color', array(
		'transport'         => 'postMessage',
		'default'           => $scss->get( 'secondary-text-color' ),
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_secondary_accent_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Link & Secondary Text Color', 'nozama-lite' ),
	) ) );

	$wp_customize->add_setting( 'site_accent_color', array(
		'transport'         => 'postMessage',
		'default'           => $scss->get( 'accent-color' ),
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_accent_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Accent color', 'nozama-lite' ),
	) ) );

	$wp_customize->add_setting( 'site_text_color', array(
		'transport'         => 'postMessage',
		'default'           => $scss->get( 'text-color' ),
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_text_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Text color', 'nozama-lite' ),
	) ) );

	$wp_customize->add_setting( 'site_text_color_secondary', array(
		'transport'         => 'postMessage',
		'default'           => $scss->get( 'text-color-dark' ),
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_text_color_secondary', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Text color secondary (darker)', 'nozama-lite' ),
	) ) );

	$wp_customize->add_setting( 'site_border_color', array(
		'transport'         => 'postMessage',
		'default'           => $scss->get( 'border-color' ),
		'sanitize_callback' => 'sanitize_hex_color',
	) );
	$wp_customize->add_control( new WP_Customize_Color_Control( $wp_customize, 'site_border_color', array(
		'section' => 'theme_colors_global',
		'label'   => esc_html__( 'Border color', 'nozama-lite' ),
	) ) );

	$partial = $wp_customize->selective_refresh->get_partial( 'theme_style' );
	$partial->settings = array_merge( $partial->settings, array(
		'site_secondary_accent_color',
		'site_accent_color',
		'site_text_color',
		'site_text_color_secondary',
		'site_border_color',
	) );
