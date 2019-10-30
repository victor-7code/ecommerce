<div class="item item-media item-media-sm">
	<?php if ( has_post_thumbnail() ) : ?>
		<div class="item-thumb">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( 'nozama_lite_item_media_sm' ); ?>
			</a>
		</div>
	<?php endif; ?>

	<div class="item-content">
		<?php if ( 'post' === get_post_type() ) : ?>
			<div class="item-meta">
				<?php echo get_the_date(); ?>
			</div>
		<?php endif; ?>

		<p class="item-title">
			<a href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</p>
	</div>
</div>
