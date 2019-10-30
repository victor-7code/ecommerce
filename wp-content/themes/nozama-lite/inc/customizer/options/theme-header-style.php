<?php
	$wp_customize->add_setting( 'header_fullwidth', array(
		'transport'         => 'postMessage',
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'header_fullwidth', array(
		'type'    => 'checkbox',
		'section' => 'theme_header_style',
		'label'   => esc_html__( 'Full width header', 'nozama-lite' ),
	) );

	$wp_customize->selective_refresh->add_partial( 'theme_header_layout', array(
		'selector'            => '.header',
		'render_callback'     => 'nozama_lite_header',
		'settings'            => array( 'header_fullwidth' ),
		'container_inclusive' => true,
	) );
