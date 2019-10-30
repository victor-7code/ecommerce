<?php
add_filter( 'maxslider_enqueue_slider_css', 'nozama_lite_front_page_replace_maxslider_enqueue_slider_css', 10, 2 );
function nozama_lite_front_page_replace_maxslider_enqueue_slider_css( $css, $slider ) {
	if ( 'home' !== $slider['template'] ) {
		return $css;
	}

	$css = str_replace( '.maxslider-btn', '.btn', $css );
	$css = str_replace( '.maxslider-slide-', '.page-hero-', $css );

	return $css;
}

add_filter( 'maxslider_slider_classes', 'nozama_lite_front_page_replace_maxslider_slider_classes', 10, 2 );
function nozama_lite_front_page_replace_maxslider_slider_classes( $classes, $slider ) {
	if ( 'home' !== $slider['template'] ) {
		return $classes;
	}

	$maxslider = array_search( 'maxslider', $classes, true );
	if ( false !== $maxslider ) {
		unset( $classes[ $maxslider ] );
		$classes[] = 'page-hero-slideshow';
		$classes[] = 'nozama-lite-slick-slider';
	}

	$new_classes = array();
	foreach ( $classes as $class ) {
		$new_classes[] = str_replace( 'maxslider-', 'page-hero-', $class );
	}

	return $new_classes;
}


add_filter( 'maxslider_default_slide_values', 'nozama_lite_change_maxslider_default_slide_values' );
function nozama_lite_change_maxslider_default_slide_values( $defaults ) {
	$defaults['content_align']  = 'maxslider-align-left';
	$defaults['content_valign'] = 'maxslider-align-bottom';

	return $defaults;
}
