<div class="entry-author-box">
	<figure class="entry-author-thumbnail">
		<?php $avatar_size = apply_filters( 'nozama_lite_the_post_author_box_avatar_size', 200 ); ?>
		<?php echo get_avatar( get_the_author_meta( 'ID' ), $avatar_size, 'avatar_default', esc_attr( get_the_author_meta( 'display_name' ) ), array( 'extra_attr' => 'itemprop="image"' ) ); ?>
	</figure>
	<div class="entry-author-desc">
		<h4 class="entry-author-title"><?php echo esc_html( get_the_author_meta( 'display_name' ) ); ?></h4>

		<?php if ( get_the_author_meta( 'user_subtitle' ) ) : ?>
			<p class="entry-author-subtitle"><?php echo esc_html( get_the_author_meta( 'user_subtitle' ) ); ?></p>
		<?php endif; ?>

		<?php if ( get_the_author_meta( 'description' ) ) {
			echo wp_kses( get_the_author_meta( 'description' ), nozama_lite_get_allowed_tags() );
		} ?>

		<div class="entry-author-socials">
			<?php nozama_lite_the_social_icons( 'user' ); ?>
		</div>
	</div>
</div>
