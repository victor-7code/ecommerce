<?php
/**
 * Template Name: Front page
 */
get_header(); ?>

<?php
	$slider_id = get_post_meta( get_queried_object_id(), 'nozama_lite_front_slider_id', true );
	if ( $slider_id && function_exists( 'MaxSlider' ) ) {
		echo apply_filters( 'nozama_lite_front_page_maxslider_html', do_shortcode( sprintf( '[maxslider id="%s" template="home"]', intval( $slider_id ) ) ) ); // WPCS: XSS ok.
	}
?>

<main class="main widget-sections">

	<?php dynamic_sidebar( 'frontpage' ); ?>

</main>

<?php get_footer();
