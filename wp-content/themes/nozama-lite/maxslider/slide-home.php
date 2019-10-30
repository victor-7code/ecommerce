<?php
/**
 * Individual slide template
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

$slide         = $template_vars['slide'];
$slider        = $template_vars['slider'];
$template      = $template_vars['template'];
$align_classes = implode( ' ', apply_filters( 'maxslider_slide_align_classes', array(
	str_replace( 'maxslider-align-', 'page-hero-align-', $slide['content_align'] ),
	str_replace( 'maxslider-align-', 'page-hero-align-', $slide['content_valign'] ),
), $slider, $slide ) );

$slide_class = "maxslider-{$slider['id']}-slide-{$slide['id']}";

?>
<?php if ( ! empty( $slide['image_id'] ) ) : ?>
	<div class="page-hero page-hero-lg <?php echo esc_attr( $slide_class ); ?> <?php echo esc_attr( $align_classes ); ?>">

		<div class="container">
			<div class="row">
				<div class="col-12">

					<div class="page-hero-content">
						<?php if ( ! empty( $slide['title'] ) ) : ?>
							<h3 class="page-hero-title">
								<?php echo wp_kses( $slide['title'], MaxSlider()->sanitizer->allowed_tags( 'title' ) ); ?>
							</h3>
						<?php endif; ?>

						<?php if ( ! empty( $slide['subtitle'] ) ) : ?>
							<p class="page-hero-subtitle">
								<?php echo wp_kses( $slide['subtitle'], MaxSlider()->sanitizer->allowed_tags( 'subtitle' ) ); ?>
							</p>
						<?php endif; ?>

						<?php if ( ! empty( $slide['button'] ) && ! empty( $slide['button_url'] ) ) : ?>
							<a href="<?php echo esc_url( $slide['button_url'] ); ?>" class="btn <?php echo esc_attr( str_replace( 'maxslider-', '', $slide['button_size'] ) ); ?>">
								<?php echo wp_kses( $slide['button'], MaxSlider()->sanitizer->allowed_tags( 'button' ) ); ?>
							</a>
						<?php endif; ?>
					</div>

				</div>
			</div>
		</div>

	</div>
<?php endif;
