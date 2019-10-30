<?php
	$wp_customize->add_setting( 'title_blog', array(
		'default'           => esc_html__( 'From the blog', 'nozama-lite' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'title_blog', array(
		'type'    => 'text',
		'section' => 'theme_titles_general',
		'label'   => esc_html__( 'Blog title', 'nozama-lite' ),
	) );

	$wp_customize->add_setting( 'title_search', array(
		'default'           => esc_html__( 'Search results', 'nozama-lite' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'title_search', array(
		'type'    => 'text',
		'section' => 'theme_titles_general',
		'label'   => esc_html__( 'Search title', 'nozama-lite' ),
	) );

	$wp_customize->add_setting( 'title_404', array(
		'default'           => esc_html__( 'Page not found', 'nozama-lite' ),
		'sanitize_callback' => 'sanitize_text_field',
	) );
	$wp_customize->add_control( 'title_404', array(
		'type'    => 'text',
		'section' => 'theme_titles_general',
		'label'   => esc_html__( '404 (not found) title', 'nozama-lite' ),
	) );
