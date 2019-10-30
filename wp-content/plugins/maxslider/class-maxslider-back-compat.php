<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MaxSlider_Back_Compat
 *
 * @version 1.1.0
 * @since 1.1.0
 */
class MaxSlider_Back_Compat {
	public function __construct() {
		// Remove support when 1.3.0 is released.
		add_filter( 'maxslider_get_slider_parameters_array', array( $this, 'arrows_to_navigation' ), 10, 2 );
	}

	public function arrows_to_navigation( $params, $id ) {
		$params['arrows']          = 'arrows' === $params['navigation'] ? 1 : '';
		$params['arrows_fg_color'] = $params['navigation_fg_color'];
		$params['arrows_bg_color'] = $params['navigation_bg_color'];

		return $params;
	}
}
