<?php
	$related   = nozama_lite_get_related_posts( get_the_ID(), apply_filters( 'nozama_lite_related_count', 3, get_post_type() ) );
	$columns   = apply_filters( 'nozama_lite_related_columns', 3, get_post_type() );
	$title     = get_theme_mod( 'title_post_related_title', __( 'Related articles', 'nozama-lite' ) );
	$post_type = get_post_type();

	do_action( "nozama_lite_before_related_{$post_type}", $related, $post_type, $title );

	if ( $related->have_posts() ) : ?>
		<div class="section-related">
			<?php if ( $title ) : ?>
				<div class="section-heading">
					<h3 class="section-title"><?php echo esc_html( $title ); ?></h3>
				</div>
			<?php endif; ?>

			<div class="row row-items">
				<?php while ( $related->have_posts() ) : $related->the_post(); ?>
					<div class="<?php echo esc_attr( nozama_lite_get_columns_classes( $columns ) ); ?>">
						<?php get_template_part( 'template-parts/item', get_post_type() ); ?>
					</div>
				<?php endwhile; ?>
				<?php wp_reset_postdata(); ?>
			</div>
		</div>
	<?php endif;

	do_action( "nozama_lite_after_related_{$post_type}", $related, $post_type, $title );

