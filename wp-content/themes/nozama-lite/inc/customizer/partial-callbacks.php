<?php
if ( ! function_exists( 'nozama_lite_customize_preview_blogname' ) ) {
	function nozama_lite_customize_preview_blogname() {
		bloginfo( 'name' );
	}
}

if ( ! function_exists( 'nozama_lite_customize_preview_blogdescription' ) ) {
	function nozama_lite_customize_preview_blogdescription() {
		bloginfo( 'description' );
	}
}

/**
 * Renders pagination preview for archive pages.
 *
 * Its results may not be accurate as the actual call may include arguments,
 * however it should be good enough for preview purposes.
 * nozama_lite_posts_pagination() cannot be used directly as the render callback passes $this and $container_context
 * as the first two arguments.
 */
if ( ! function_exists( 'nozama_lite_customize_preview_pagination' ) ) {
	function nozama_lite_customize_preview_pagination( $_this, $container_context ) {
		nozama_lite_posts_pagination();
	}
}
