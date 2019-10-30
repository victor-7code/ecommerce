<?php get_header(); ?>

<main class="main">

	<div class="container">

		<div class="row <?php nozama_lite_the_row_classes(); ?>">

			<?php get_template_part( 'template-parts/breadcrumbs' ); ?>

			<div class="<?php nozama_lite_the_container_classes(); ?>">

				<?php while ( have_posts() ) : the_post(); ?>

					<article id="entry-<?php the_ID(); ?>" <?php post_class( 'entry' ); ?>>

						<?php nozama_lite_the_post_thumbnail(); ?>

						<div class="article-content-container">
							<div class="row">
								<div class="col-12">
									<?php nozama_lite_the_post_header(); ?>

									<div class="entry-content">
										<?php the_content(); ?>

										<?php wp_link_pages( nozama_lite_wp_link_pages_default_args() ); ?>
									</div>

									<?php if ( has_tag() && get_theme_mod( 'post_show_tags', 1 ) ) : ?>
										<div class="entry-tags">
											<?php
												/* translators: There is a space at the end. */
												the_tags( esc_html__( 'Tags: ', 'nozama-lite' ), ', ' );
											?>
										</div>
									<?php endif; ?>

									<?php if ( get_theme_mod( 'post_show_authorbox', 1 ) ) {
										nozama_lite_the_post_author_box();
									} ?>
								</div>
							</div>
						</div>

					</article>

					<?php if ( get_theme_mod( 'post_show_related', 1 ) ) {
						get_template_part( 'template-parts/related', get_post_type() );
					} ?>

					<div class="row">
						<div class="col-lg-10 col-12 ml-lg-auto">
						<?php if ( get_theme_mod( 'post_show_comments', 1 ) ) {
							comments_template();
						} ?>
						</div>
					</div>

				<?php endwhile; ?>

			</div>

			<?php get_sidebar(); ?>

		</div>

	</div>

</main>

<?php get_footer();
