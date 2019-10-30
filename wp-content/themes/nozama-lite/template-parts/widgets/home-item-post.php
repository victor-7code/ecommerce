<div class="item">
	<div class="item-thumb">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( 'nozama_lite_item' ); ?>
		</a>
	</div>

	<div class="item-content">
		<div class="item-meta">
			<?php echo get_the_date(); ?>
		</div>

		<p class="item-title">
			<a href="<?php the_permalink(); ?>">
				<?php the_title(); ?>
			</a>
		</p>

		<?php the_terms( get_the_ID(), 'category', '<p class="item-inset">', ', ', '</p>' ); ?>
	</div>
</div>
