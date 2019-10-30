<?php get_header(); ?>

<main class="main">
	<div class="container">
		<div class="row <?php nozama_lite_the_row_classes(); ?>">

			<?php get_template_part( 'template-parts/breadcrumbs' ); ?>

			<?php $the_title = get_theme_mod( 'title_blog', __( 'From the blog', 'nozama-lite' ) ); ?>
			<?php if ( ! empty( $the_title ) && is_home() ) : ?>
				<div class="col-12">
					<div class="page-title">
						<?php echo esc_html( $the_title ); ?>
					</div>
				</div>
			<?php endif; ?>

			<div class="<?php nozama_lite_the_container_classes(); ?>">
				<?php
					if ( have_posts() ) :

						while ( have_posts() ) : the_post();

							get_template_part( 'template-parts/item-media', get_post_type() );

						endwhile;

						nozama_lite_posts_pagination();

					else :

						get_template_part( 'template-parts/article', 'none' );

					endif;
				?>
			</div>

			<?php get_sidebar(); ?>
		</div>
	</div>
</main>

<?php get_footer();
