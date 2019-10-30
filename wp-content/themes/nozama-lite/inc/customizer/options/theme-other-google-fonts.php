<?php
	$wp_customize->add_setting( 'theme_local_google_fonts', array(
		'default'           => '',
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'theme_local_google_fonts', array(
		'type'        => 'checkbox',
		'section'     => 'theme_other_google_fonts',
		'label'       => esc_html__( 'Serve Google Fonts locally.', 'nozama-lite' ),
		'description' => esc_html__( "When enabled, Google fonts will be served by your server instead from Google's CDN. You may need to enable this option for GDPR compliance.", 'nozama-lite' ),
	) );
