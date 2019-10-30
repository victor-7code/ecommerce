<?php
	$wp_customize->add_setting( 'header_primary_menu_padding', array(
		'transport'         => 'postMessage',
		'default'           => '',
		'sanitize_callback' => 'nozama_lite_sanitize_intval_or_empty',
	) );
	$wp_customize->add_control( 'header_primary_menu_padding', array(
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 0,
			'step' => 1,
		),
		'section'     => 'theme_header_primary_menu',
		'label'       => esc_html__( 'Vertical padding (in pixels)', 'nozama-lite' ),
	) );

	$wp_customize->add_setting( 'header_primary_menu_text_size', array(
		'transport'         => 'postMessage',
		'default'           => '',
		'sanitize_callback' => 'nozama_lite_sanitize_intval_or_empty',
	) );
	$wp_customize->add_control( 'header_primary_menu_text_size', array(
		'type'        => 'number',
		'input_attrs' => array(
			'min'  => 0,
			'step' => 1,
		),
		'section'     => 'theme_header_primary_menu',
		'label'       => esc_html__( 'Menu text size (in pixels)', 'nozama-lite' ),
	) );

	$partial = $wp_customize->selective_refresh->get_partial( 'theme_style' );
	$partial->settings = array_merge( $partial->settings, array(
		'header_primary_menu_padding',
		'header_primary_menu_text_size',
	) );
