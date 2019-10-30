<?php
	$wp_customize->get_setting( 'blogname' )->transport = 'postMessage';
	$wp_customize->selective_refresh->add_partial( 'theme_blogname', array(
		'selector'        => '.site-logo a',
		'render_callback' => 'nozama_lite_customize_preview_blogname',
	) );

	$wp_customize->get_setting( 'blogdescription' )->transport = 'postMessage';
	$wp_customize->selective_refresh->add_partial( 'theme_blogdescription', array(
		'selector'        => '.site-tagline',
		'render_callback' => 'nozama_lite_customize_preview_blogdescription',
	) );

	$wp_customize->add_setting( 'limit_logo_size', array(
		'transport'         => 'postMessage',
		'default'           => 0,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'limit_logo_size', array(
		'type'        => 'checkbox',
		'section'     => 'title_tagline',
		'priority'    => 8,
		'label'       => esc_html__( 'Limit logo size (for Retina display)', 'nozama-lite' ),
		'description' => esc_html__( 'This option will limit the image size to half its width. You will need to upload your image in 2x the dimension you want to display it in.', 'nozama-lite' ),
	) );

	$wp_customize->selective_refresh->get_partial( 'theme_style' )->settings[] = 'limit_logo_size';


	$wp_customize->add_setting( 'show_site_title', array(
		'transport'         => 'postMessage',
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'show_site_title', array(
		'type'    => 'checkbox',
		'section' => 'title_tagline',
		'label'   => esc_html__( 'Show site title', 'nozama-lite' ),
	) );

	$wp_customize->add_setting( 'show_site_description', array(
		'transport'         => 'postMessage',
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'show_site_description', array(
		'type'    => 'checkbox',
		'section' => 'title_tagline',
		'label'   => esc_html__( 'Show site tagline', 'nozama-lite' ),
	) );

	$wp_customize->selective_refresh->add_partial( 'theme_site_branding', array(
		'selector'            => '.site-branding',
		'render_callback'     => 'nozama_lite_the_site_identity',
		'settings'            => array( 'custom_logo', 'show_site_title', 'show_site_description' ),
		'container_inclusive' => true,
	) );
