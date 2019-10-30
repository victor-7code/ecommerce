<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MaxSlider_Template_Hooks {
	public function __construct() {
		add_filter( 'maxslider_slider_classes', 'MaxSlider_Template_Hooks::slider_classes', 10, 2 );
	}

	public static function slider_classes( $classes, $slider ) {
		if ( ! empty( $slider['params']['navigation_position'] ) ) {
			$classes[] = $slider['params']['navigation_position'];
		}

		return $classes;
	}

}
