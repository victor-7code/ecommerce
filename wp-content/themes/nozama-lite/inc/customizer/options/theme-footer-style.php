<?php
	$wp_customize->add_setting( 'footer_layout', array(
		'transport'         => 'postMessage',
		'default'           => nozama_lite_footer_layout_default(),
		'sanitize_callback' => 'nozama_lite_sanitize_footer_layout',
	) );
	$wp_customize->add_control( 'footer_layout', array(
		'type'    => 'select',
		'section' => 'theme_footer_style',
		'label'   => esc_html__( 'Layout', 'nozama-lite' ),
		'choices' => nozama_lite_footer_layout_choices(),
	) );

	$wp_customize->add_setting( 'footer_fullwidth', array(
		'transport'         => 'postMessage',
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'footer_fullwidth', array(
		'type'    => 'checkbox',
		'section' => 'theme_footer_style',
		'label'   => esc_html__( 'Full width footer', 'nozama-lite' ),
	) );

	$wp_customize->selective_refresh->add_partial( 'theme_footer_layout', array(
		'selector'            => '.footer',
		'render_callback'     => 'nozama_lite_footer',
		'settings'            => array( 'footer_layout', 'footer_fullwidth' ),
		'container_inclusive' => true,
	) );
