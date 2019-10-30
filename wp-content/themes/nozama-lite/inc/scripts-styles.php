<?php
/**
 * Nozama_Lite scripts and styles related functions.
 */

/**
 * Register Google Fonts
 */
function nozama_lite_fonts_url() {
	$fonts_url = '';
	$fonts     = array();
	$subsets   = 'cyrillic,cyrillic-ext,greek,greek-ext,latin-ext,vietnamese';

	/* translators: If there are characters in your language that are not supported by Source Sans Pro, translate this to 'off'. Do not translate into your own language. */
	if ( 'off' !== _x( 'on', 'Source Sans Pro font: on or off', 'nozama-lite' ) ) {
		$fonts[] = 'Source Sans Pro:400,400i,600,700';
	}

	if ( $fonts ) {
		$fonts_url = add_query_arg( array(
			'family' => urlencode( implode( '|', $fonts ) ),
			'subset' => urlencode( $subsets ),
		), 'https://fonts.googleapis.com/css' );
	}

	if ( get_theme_mod( 'theme_local_google_fonts' ) ) {
		$fonts_url = get_template_directory_uri() . '/css/google-fonts.css';
	}

	return $fonts_url;
}

/**
 * Register scripts and styles unconditionally.
 */
function nozama_lite_register_scripts() {
	$theme = wp_get_theme();

	if ( ! wp_script_is( 'alpha-color-picker', 'enqueued' ) && ! wp_script_is( 'alpha-color-picker', 'registered' ) ) {
		wp_register_style( 'alpha-color-picker', get_template_directory_uri() . '/assets/vendor/alpha-color-picker/alpha-color-picker.css', array(
			'wp-color-picker',
		), '1.0.0' );
		wp_register_script( 'alpha-color-picker', get_template_directory_uri() . '/assets/vendor/alpha-color-picker/alpha-color-picker.js', array(
			'jquery',
			'wp-color-picker',
		), '1.0.0', true );
	}

	if ( ! wp_script_is( 'slick', 'enqueued' ) && ! wp_script_is( 'slick', 'registered' ) ) {
		wp_register_style( 'slick', get_template_directory_uri() . '/assets/vendor/slick/slick.css', array(), '1.6.0' );
		wp_register_script( 'slick', get_template_directory_uri() . '/assets/vendor/slick/slick.js', array(
			'jquery',
		), '1.6.0', true );
	}

	if ( ! wp_script_is( 'nozama-lite-plugin-post-meta', 'enqueued' ) && ! wp_script_is( 'nozama-lite-plugin-post-meta', 'registered' ) ) {
		wp_register_style( 'nozama-lite-plugin-post-meta', get_template_directory_uri() . '/css/admin/post-meta.css', array(
			'alpha-color-picker',
		), $theme->get( 'Version' ) );
		wp_register_script( 'nozama-lite-plugin-post-meta', get_template_directory_uri() . '/js/admin/post-meta.js', array(
			'media-editor',
			'jquery',
			'jquery-ui-sortable',
			'alpha-color-picker',
		), $theme->get( 'Version' ), true );

		$settings = array(
			'ajaxurl'             => admin_url( 'admin-ajax.php' ),
			'tSelectFile'         => esc_html__( 'Select file', 'nozama-lite' ),
			'tSelectFiles'        => esc_html__( 'Select files', 'nozama-lite' ),
			'tUseThisFile'        => esc_html__( 'Use this file', 'nozama-lite' ),
			'tUseTheseFiles'      => esc_html__( 'Use these files', 'nozama-lite' ),
			'tUpdateGallery'      => esc_html__( 'Update gallery', 'nozama-lite' ),
			'tLoading'            => esc_html__( 'Loading...', 'nozama-lite' ),
			'tPreviewUnavailable' => esc_html__( 'Gallery preview not available.', 'nozama-lite' ),
			'tRemoveImage'        => esc_html__( 'Remove image', 'nozama-lite' ),
			'tRemoveFromGallery'  => esc_html__( 'Remove from gallery', 'nozama-lite' ),
		);
		wp_localize_script( 'nozama-lite-plugin-post-meta', 'nozama_lite_plugin_PostMeta', $settings );
	}

	wp_register_style( 'nozama-lite-repeating-fields', get_template_directory_uri() . '/css/admin/repeating-fields.css', array(), $theme->get( 'Version' ) );
	wp_register_script( 'nozama-lite-repeating-fields', get_template_directory_uri() . '/js/admin/repeating-fields.js', array(
		'jquery',
		'jquery-ui-sortable',
	), $theme->get( 'Version' ), true );

	wp_register_style( 'font-awesome-5', get_template_directory_uri() . '/assets/vendor/fontawesome/css/font-awesome.css', array(), '5.1.0' );

	wp_register_style( 'jquery-magnific-popup', get_template_directory_uri() . '/assets/vendor/magnific-popup/magnific.css', array(), '1.0.0' );
	wp_register_script( 'jquery-magnific-popup', get_template_directory_uri() . '/assets/vendor/magnific-popup/jquery.magnific-popup.js', array( 'jquery' ), '1.0.0', true );
	wp_register_script( 'nozama-lite-magnific-init', get_template_directory_uri() . '/js/magnific-init.js', array( 'jquery' ), $theme->get( 'Version' ), true );



	wp_register_style( 'nozama-lite-google-font', nozama_lite_fonts_url(), array(), null );
	wp_register_style( 'nozama-lite-base', get_template_directory_uri() . '/css/base.css', array(), $theme->get( 'Version' ) );
	wp_register_style( 'mmenu', get_template_directory_uri() . '/css/mmenu.css', array(), '5.5.3' );

	wp_register_style( 'nozama-lite-dependencies', false, array(
		'nozama-lite-google-font',
		'nozama-lite-base',
		'nozama-lite-common',
		'mmenu',
		'slick',
		'font-awesome-5',
	), $theme->get( 'Version' ) );

	if ( is_child_theme() ) {
		wp_register_style( 'nozama-lite-style-parent', get_template_directory_uri() . '/style.css', array(
			'nozama-lite-dependencies',
		), $theme->get( 'Version' ) );
	}

	wp_register_style( 'nozama-lite-style', get_stylesheet_uri(), array(
		'nozama-lite-dependencies',
	), $theme->get( 'Version' ) );


	wp_register_script( 'mmenu-oncanvas', get_template_directory_uri() . '/js/jquery.mmenu.oncanvas.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu-offcanvas', get_template_directory_uri() . '/js/jquery.mmenu.offcanvas.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu-autoheight', get_template_directory_uri() . '/js/jquery.mmenu.autoheight.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu-backbutton', get_template_directory_uri() . '/js/jquery.mmenu.backbutton.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu-navbars', get_template_directory_uri() . '/js/jquery.mmenu.navbars.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu-navbar-close', get_template_directory_uri() . '/js/jquery.mmenu.navbar.close.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu-navbar-next', get_template_directory_uri() . '/js/jquery.mmenu.navbar.next.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu-navbar-prev', get_template_directory_uri() . '/js/jquery.mmenu.navbar.prev.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu-navbar-title', get_template_directory_uri() . '/js/jquery.mmenu.navbar.title.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu-toggles', get_template_directory_uri() . '/js/jquery.mmenu.toggles.js', array( 'jquery' ), '5.5.3', true );
	wp_register_script( 'mmenu', false, array(
		'jquery',
		'mmenu-oncanvas',
		'mmenu-offcanvas',
		'mmenu-autoheight',
	), '5.5.3', true );
	wp_register_script( 'fitVids', get_template_directory_uri() . '/js/jquery.fitvids.js', array( 'jquery' ), '1.1', true );

	wp_register_script( 'nozama-lite-dependencies', false, array(
		'jquery',
		'mmenu',
		'slick',
		'fitVids',
	), $theme->get( 'Version' ), true );

	wp_register_script( 'nozama-lite-front-scripts', get_template_directory_uri() . '/js/scripts.js', array(
		'nozama-lite-dependencies',
	), $theme->get( 'Version' ), true );

	$vars = array(
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
	);
	wp_localize_script( 'nozama-lite-front-scripts', 'nozama_lite_vars', $vars );

}
add_action( 'init', 'nozama_lite_register_scripts' );

/**
 * Enqueue scripts and styles.
 */
function nozama_lite_enqueue_scripts() {

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	if ( get_theme_mod( 'theme_lightbox', 1 ) ) {
		wp_enqueue_style( 'jquery-magnific-popup' );
		wp_enqueue_script( 'jquery-magnific-popup' );
		wp_enqueue_script( 'nozama-lite-magnific-init' );
	}

	if ( is_child_theme() ) {
		wp_enqueue_style( 'nozama-lite-style-parent' );
	}

	wp_enqueue_style( 'nozama-lite-style' );
	wp_add_inline_style( 'nozama-lite-style', nozama_lite_get_all_customizer_css() );

	wp_enqueue_script( 'nozama-lite-front-scripts' );

}
add_action( 'wp_enqueue_scripts', 'nozama_lite_enqueue_scripts' );


/**
 * Enqueue admin scripts and styles.
 */
function nozama_lite_admin_scripts( $hook ) {
	$theme = wp_get_theme();

	wp_register_style( 'nozama-lite-widgets', get_template_directory_uri() . '/css/admin/widgets.css', array(
		'nozama-lite-repeating-fields',
		'nozama-lite-plugin-post-meta',
		'alpha-color-picker',
	), $theme->get( 'Version' ) );

	wp_register_script( 'nozama-lite-widgets', get_template_directory_uri() . '/js/admin/widgets.js', array(
		'jquery',
		'nozama-lite-repeating-fields',
		'nozama-lite-plugin-post-meta',
		'alpha-color-picker',
	), $theme->get( 'Version' ), true );
	$params = array(
		'ajaxurl'                      => admin_url( 'admin-ajax.php' ),
		'widget_post_type_items_nonce' => wp_create_nonce( 'nozama-lite-post-type-items' ),
	);
	wp_localize_script( 'nozama-lite-widgets', 'ThemeWidget', $params );

	//
	// Enqueue
	//
	if ( in_array( $hook, array( 'widgets.php', 'customize.php' ), true ) ) {
		wp_enqueue_style( 'nozama-lite-repeating-fields' );
		wp_enqueue_script( 'nozama-lite-repeating-fields' );

		wp_enqueue_media();
		wp_enqueue_style( 'nozama-lite-widgets' );
		wp_enqueue_script( 'nozama-lite-widgets' );
	}

	if ( in_array( $hook, array( 'post.php', 'post-new.php' ), true ) ) {
		wp_enqueue_media();
		wp_enqueue_style( 'nozama-lite-plugin-post-meta' );
		wp_enqueue_script( 'nozama-lite-plugin-post-meta' );
	}
}
add_action( 'admin_enqueue_scripts', 'nozama_lite_admin_scripts' );
