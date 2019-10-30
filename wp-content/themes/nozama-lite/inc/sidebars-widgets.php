<?php
/**
 * Nozama_Lite sidebars and widgets related functions.
 */

/**
 * Register widget areas.
 */
function nozama_lite_widgets_init() {
	register_sidebar( array(
		'name'          => esc_html__( 'Blog', 'nozama-lite' ),
		'id'            => 'sidebar-1',
		'description'   => esc_html__( 'Widgets added here will appear on the blog section.', 'nozama-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Page', 'nozama-lite' ),
		'id'            => 'sidebar-2',
		'description'   => esc_html__( 'Widgets added here will appear on the static pages.', 'nozama-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Front Page', 'nozama-lite' ),
		'id'            => 'frontpage',
		'description'   => esc_html__( 'These widgets appear on pages that have the "Front page" template assigned.', 'nozama-lite' ),
		'before_widget' => '<section id="%1$s" class="widget-section %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="section-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Shop', 'nozama-lite' ),
		'id'            => 'shop',
		'description'   => esc_html__( 'These widgets appear on shop-related pages.', 'nozama-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Pre-footer', 'nozama-lite' ),
		'id'            => 'prefooter',
		'description'   => esc_html__( 'Full width widget area, built to house the Theme - Newsletter widget.', 'nozama-lite' ),
		'before_widget' => '<section id="%1$s" class="widget-section %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h2 class="section-title">',
		'after_title'   => '</h2>',
	) );

	register_sidebar( array(
		'name'          => esc_html__( 'Footer - 1st column', 'nozama-lite' ),
		'id'            => 'footer-1',
		'description'   => esc_html__( 'Widgets added here will appear on the first footer column.', 'nozama-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer - 2nd column', 'nozama-lite' ),
		'id'            => 'footer-2',
		'description'   => esc_html__( 'Widgets added here will appear on the second footer column.', 'nozama-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer - 3rd column', 'nozama-lite' ),
		'id'            => 'footer-3',
		'description'   => esc_html__( 'Widgets added here will appear on the third footer column.', 'nozama-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
	register_sidebar( array(
		'name'          => esc_html__( 'Footer - 4th column', 'nozama-lite' ),
		'id'            => 'footer-4',
		'description'   => esc_html__( 'Widgets added here will appear on the fourth footer column.', 'nozama-lite' ),
		'before_widget' => '<aside id="%1$s" class="widget %2$s">',
		'after_widget'  => '</aside>',
		'before_title'  => '<h3 class="widget-title">',
		'after_title'   => '</h3>',
	) );
}
add_action( 'widgets_init', 'nozama_lite_widgets_init' );


function nozama_lite_load_widgets() {
	require get_template_directory() . '/inc/widgets/socials.php';
	require get_template_directory() . '/inc/widgets/latest-posts.php';
	require get_template_directory() . '/inc/widgets/newsletter.php';

	require get_template_directory() . '/inc/widgets/home-latest-products.php';
	require get_template_directory() . '/inc/widgets/home-woocommerce-categories.php';
	require get_template_directory() . '/inc/widgets/home-instagram.php';

	register_widget( 'CI_Widget_Socials' );
	register_widget( 'CI_Widget_Latest_Posts' );
	register_widget( 'CI_Widget_Newsletter' );

	register_widget( 'CI_Widget_WooCommerce_Categories' );
	register_widget( 'CI_Widget_Home_Latest_Products' );

	if ( class_exists( 'CI_Widget_Home_Instagram' ) ) {
		register_widget( 'CI_Widget_Home_Instagram' );
	}
}
add_action( 'widgets_init', 'nozama_lite_load_widgets' );


function nozama_lite_get_fullwidth_sidebars() {
	return apply_filters( 'nozama_lite_fullwidth_sidebars', array(
		'frontpage',
		'prefooter',
	) );
}


function nozama_lite_get_fullwidth_widgets() {
	return apply_filters( 'nozama_lite_fullwidth_widgets', array(
		'ci-home-instagram',
		'ci-home-latest-products',
		'ci-home-woocommerce-categories',
		'ci-home-newsletter',
	) );
}


function nozama_lite_wrap_non_fullwidth_widgets( $params ) {
	$sidebar = $params[0]['id'];
	if ( is_admin() || ! in_array( $sidebar, nozama_lite_get_fullwidth_sidebars(), true ) ) {
		return $params;
	}

	$fullwidth_widgets = nozama_lite_get_fullwidth_widgets();

	$pattern = '/\-' . $params[1]['number'] . '$/';
	$widget  = $params[0]['widget_id'];
	$widget  = preg_replace( $pattern, '', $widget, 1 );

	$wrap_widget = ! in_array( $widget, $fullwidth_widgets, true );
	$wrap_widget = apply_filters( 'nozama_lite_wrap_non_fullwidth_widget', $wrap_widget, $widget, $sidebar, $params );

	if ( $wrap_widget ) {
		$params[0]['before_widget'] = $params[0]['before_widget'] . '<div class="container"><div class="row"><div class="col-12">';
		$params[0]['after_widget']  = '</div></div></div>' . $params[0]['after_widget'];
		$params[0]['before_title']  = '<div class="section-heading">' . $params[0]['before_title'];
		$params[0]['after_title']   = $params[0]['after_title'] . '</div>';
	}

	return $params;
}
add_filter( 'dynamic_sidebar_params', 'nozama_lite_wrap_non_fullwidth_widgets' );

function nozama_lite_footer_widget_area_classes( $layout ) {
	switch ( $layout ) {
		case '3-col':
			$classes = array(
				'footer-1' => array(
					'active' => true,
					'class'  => 'col-lg-4 col-12',
				),
				'footer-2' => array(
					'active' => true,
					'class'  => 'col-lg-4 col-12',
				),
				'footer-3' => array(
					'active' => true,
					'class'  => 'col-lg-4 col-12',
				),
				'footer-4' => array(
					'active' => false,
					'class'  => '',
				),
			);
			break;
		case '2-col':
			$classes = array(
				'footer-1' => array(
					'active' => true,
					'class'  => 'col-md-6 col-12',
				),
				'footer-2' => array(
					'active' => true,
					'class'  => 'col-md-6 col-12',
				),
				'footer-3' => array(
					'active' => false,
					'class'  => '',
				),
				'footer-4' => array(
					'active' => false,
					'class'  => '',
				),
			);
			break;
		case '1-col':
			$classes = array(
				'footer-1' => array(
					'active' => true,
					'class'  => 'col-12',
				),
				'footer-2' => array(
					'active' => false,
					'class'  => '',
				),
				'footer-3' => array(
					'active' => false,
					'class'  => '',
				),
				'footer-4' => array(
					'active' => false,
					'class'  => '',
				),
			);
			break;
		case '1-3':
			$classes = array(
				'footer-1' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
				'footer-2' => array(
					'active' => true,
					'class'  => 'col-lg-9 col-md-6 col-12',
				),
				'footer-3' => array(
					'active' => false,
					'class'  => '',
				),
				'footer-4' => array(
					'active' => false,
					'class'  => '',
				),
			);
			break;
		case '3-1':
			$classes = array(
				'footer-1' => array(
					'active' => true,
					'class'  => 'col-lg-9 col-md-6 col-12',
				),
				'footer-2' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
				'footer-3' => array(
					'active' => false,
					'class'  => '',
				),
				'footer-4' => array(
					'active' => false,
					'class'  => '',
				),
			);
			break;
		case '1-1-2':
			$classes = array(
				'footer-1' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
				'footer-2' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
				'footer-3' => array(
					'active' => true,
					'class'  => 'col-lg-6 col-12',
				),
				'footer-4' => array(
					'active' => false,
					'class'  => '',
				),
			);
			break;
		case '2-1-1':
			$classes = array(
				'footer-1' => array(
					'active' => true,
					'class'  => 'col-lg-6 col-12',
				),
				'footer-2' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
				'footer-3' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
				'footer-4' => array(
					'active' => false,
					'class'  => '',
				),
			);
			break;
		case '4-col':
		default:
			$classes = array(
				'footer-1' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
				'footer-2' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
				'footer-3' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
				'footer-4' => array(
					'active' => true,
					'class'  => 'col-lg-3 col-md-6 col-12',
				),
			);
	}

	return apply_filters( 'nozama_lite_footer_widget_area_classes', $classes, $layout );
}

function nozama_lite_get_allowed_sidebar_wrappers() {
	$attributes = array(
		'id'    => true,
		'class' => true,
	);

	$allowed = array(
		'a'       => array(
			'id'     => true,
			'class'  => true,
			'href'   => true,
			'title'  => true,
			'target' => true,
		),
		'div'     => $attributes,
		'span'    => $attributes,
		'strong'  => $attributes,
		'i'       => $attributes,
		'section' => $attributes,
		'aside'   => $attributes,
		'h1'      => $attributes,
		'h2'      => $attributes,
		'h3'      => $attributes,
		'h4'      => $attributes,
		'h5'      => $attributes,
		'h6'      => $attributes,
	);

	return apply_filters( 'nozama_lite_get_allowed_sidebar_wrappers', $allowed );
}
