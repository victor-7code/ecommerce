<?php $subtitle = get_post_meta( get_the_ID(), 'subtitle', true ); ?>
<div class="item">
	<div class="item-thumb">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'nozama_lite_item' ); ?>
		</a>
	</div>

	<div class="item-content">
		<p class="item-title">
			<a href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</p>

		<?php if ( $subtitle ) : ?>
			<p class="item-inset"><?php echo wp_kses( $subtitle, nozama_lite_get_allowed_tags( 'guide' ) ); ?></p>
		<?php endif; ?>
	</div>	
</div>
