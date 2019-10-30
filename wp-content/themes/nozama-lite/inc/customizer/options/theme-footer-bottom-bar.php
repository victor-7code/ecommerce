<?php
	$wp_customize->add_setting( 'footer_show_bottom_bar', array(
		'transport'         => 'postMessage',
		'default'           => 1,
		'sanitize_callback' => 'absint',
	) );
	$wp_customize->add_control( 'footer_show_bottom_bar', array(
		'type'    => 'checkbox',
		'section' => 'theme_footer_bottom_bar',
		'label'   => esc_html__( 'Show bottom bar', 'nozama-lite' ),
	) );

	$wp_customize->selective_refresh->get_partial( 'theme_footer_layout' )->settings[] = 'footer_show_bottom_bar';

	$wp_customize->add_setting( 'footer_text', array(
		'transport'         => 'postMessage',
		'default'           => nozama_lite_get_default_footer_text(),
		'sanitize_callback' => 'nozama_lite_sanitize_footer_text',
	) );
	$wp_customize->add_control( 'footer_text', array(
		'type'    => 'textarea',
		'section' => 'theme_footer_bottom_bar',
		'label'   => esc_html__( 'Credits text', 'nozama-lite' ),
	) );

	if ( get_theme_support( 'nozama-lite-footer-text-right' ) ) {
		$wp_customize->add_setting( 'footer_text_right', array(
			'transport'         => 'postMessage',
			'default'           => nozama_lite_get_default_footer_text( 'right' ),
			'sanitize_callback' => 'nozama_lite_sanitize_footer_text',
		) );
		$wp_customize->add_control( 'footer_text_right', array(
			'type'    => 'textarea',
			'section' => 'theme_footer_bottom_bar',
			'label'   => esc_html__( 'Credits text (right)', 'nozama-lite' ),
		) );
	}

	$wp_customize->add_setting( 'footer_card_icons', array(
		'transport'         => 'postMessage',
		'default'           => nozama_lite_get_default_footer_card_icons(),
		'sanitize_callback' => 'nozama_lite_sanitize_footer_card_icons',
	) );
	$wp_customize->add_control( 'footer_card_icons', array(
		'type'        => 'textarea',
		'section'     => 'theme_footer_bottom_bar',
		'label'       => esc_html__( 'Payment card / method Icons', 'nozama-lite' ),
		/* translators: %s is a URL */
		'description' => wp_kses( sprintf( __( 'Enter a comma-separated list of icon codes that you want displayed, e.g. <code>fa-cc-visa, fa-cc-mastercard</code>. Only icons from the "Brands" set can be displayed. A list of related icons <a href="%s" target="_blank">can be found here.', 'nozama-lite' ), 'https://fontawesome.com/icons?d=gallery&s=brands&c=payments-shopping' ), nozama_lite_get_allowed_tags( 'guide' ) ),
	) );


	$wp_customize->selective_refresh->add_partial( 'footer_bottom_bar', array(
		'selector'            => '.footer-info',
		'render_callback'     => 'nozama_lite_footer_bottom_bar',
		'settings'            => array( 'footer_text', 'footer_card_icons' ),
		'container_inclusive' => true,
		'fallback_refresh'    => true,
	) );

	if ( get_theme_support( 'nozama-lite-footer-text-right' ) ) {
		$wp_customize->selective_refresh->get_partial( 'footer_bottom_bar' )->settings[] = 'footer_text_right';
	}
