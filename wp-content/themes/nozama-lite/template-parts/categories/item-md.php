<?php
/** @var $term WP_Term */
// Assume we were passed a term object.
if ( empty( $term ) || ! is_object( $term ) ) {
	return;
}

$title     = $term->name;
$subtitle  = get_term_meta( $term->term_id, 'subtitle', true );
$image_id  = get_term_meta( $term->term_id, 'thumbnail_id', true );
$image_url = wp_get_attachment_image_url( $image_id, 'nozama_lite_block_item_md' );
$link_url  = get_term_link( $term->term_id );
?>
<div
	class="block-item block-item-md"
	style="background-image: url(<?php echo esc_url( $image_url ); ?>)"
>
	<div class="block-item-content-wrap">
		<div class="block-item-content">
			<p class="block-item-title"><?php echo esc_html( $title ); ?></p>
			<?php if ( $subtitle ) : ?>
				<p class="block-item-subtitle"><?php echo wp_kses( $subtitle, nozama_lite_get_allowed_tags( 'guide' ) ); ?></p>
			<?php endif; ?>

			<a href="<?php echo esc_url( $link_url ); ?>" class="btn btn-sm btn-block-item">
				<?php esc_html_e( 'Shop now', 'nozama-lite' ); ?>
			</a>
		</div>
	</div>
</div>
