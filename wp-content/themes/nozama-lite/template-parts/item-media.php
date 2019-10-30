<div id="item-<?php the_ID(); ?>" <?php post_class( 'item item-media' ); ?>>
	<?php nozama_lite_the_item_thumbnail( 'nozama_lite_item_media' ); ?>

	<div class="item-content">
		<p class="item-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</p>

		<div class="item-excerpt">
			<?php the_excerpt(); ?>
		</div>

		<a href="<?php the_permalink(); ?>" class="btn item-read-more"><?php esc_html_e( 'Read More', 'nozama-lite' ); ?></a>
	</div>
</div>
