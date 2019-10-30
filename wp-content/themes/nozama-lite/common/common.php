<?php
/**
 * Common theme features.
 */

/**
 * Common assets registration
 */
function nozama_lite_register_common_assets() {
	$theme = wp_get_theme();
	wp_register_style( 'nozama-lite-common', get_template_directory_uri() . '/common/css/global.css', array(), $theme->get( 'Version' ) );
}
add_action( 'init', 'nozama_lite_register_common_assets', 8 );
