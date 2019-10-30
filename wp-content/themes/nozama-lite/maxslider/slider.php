<?php
/**
 * Slider container template
 *
 * This template can be overridden by copying it to yourtheme/maxslider/ directory.
 *
 * @author  The CSSIgniter Team
 * @package MaxSlider/Templates
 * @version 1.1.0
 * @since 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}


$slider   = $template_vars['slider'];
$template = $template_vars['template'];

MaxSlider()->enqueue_slider_css( $slider );

$slider_classes = implode( ' ', apply_filters( 'maxslider_slider_classes', array(
	'maxslider',
), $slider ) );

?>
<div
	id="maxslider-<?php echo esc_attr( $slider['id'] ); ?>"
	class="<?php echo esc_attr( $slider_classes ); ?>"
	<?php echo $slider['data_string']; ?>
>
	<?php
		foreach ( $slider['slides'] as $slide ) {
			MaxSlider()->get_template_part( 'slide', $template, array(
				'slider'   => $slider,
				'slide'    => $slide,
				'template' => $template,
			) );
		}
	?>
	<div class="page-hero-slideshow-nav-wrap">
		<div class="container">
			<div class="row">
				<div class="col-12">
					<div class="page-hero-slideshow-nav"></div>
				</div>
			</div>
		</div>
	</div>
</div>
