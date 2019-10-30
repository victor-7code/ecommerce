<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * MaxSlider Visual Composer Playlist
 * @since 1.0.0
 */
class MaxSlider_Visual_Composer_Slider implements Vc_Vendor_Interface {

	/**
	 * Implement interface, map MaxSlider shortcode
	 * @since 1.0.0
	 */
	public function load() {
		vc_map( $this->addShortcodeSettings() );
	}

	/**
	 * Mapping settings.
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function addShortcodeSettings() {
		$sliders = MaxSlider()->get_all_sliders();

		$values = array();
		foreach ( $sliders as $slider ) {
			$values[ $slider->post_title ] = $slider->ID;
		}

		return array(
			'base'        => 'maxslider',
			'name'        => esc_html__( 'MaxSlider', 'maxslider' ),
			'icon'        => MaxSlider()->plugin_url() . 'assets/images/vc_icon.png',
			/* translators: This translation should match the Visual Composer element category's translation, in order for the element to get grouped under the same category. */
			'category'    => esc_html_x( 'Content', 'Visual Composer element category', 'maxslider' ),
			'description' => esc_html__( 'Displays a single slider', 'maxslider' ),
			'params'      => array(
				array(
					'type'        => 'dropdown',
					'heading'     => esc_html__( 'Slider', 'maxslider' ),
					'param_name'  => 'id',
					'value'       => $values,
					'save_always' => true,
					'admin_label' => true,
					'description' => esc_html__( 'Choose a previously created slider from the drop down list.', 'maxslider' ),
				),
			),
		);
	}
}
