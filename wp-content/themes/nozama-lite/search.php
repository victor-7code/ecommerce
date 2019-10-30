<?php get_header(); ?>

<main class="main">
	<div class="container">

		<div class="row">

			<?php get_template_part( 'template-parts/breadcrumbs' ); ?>

			<div class="<?php nozama_lite_the_container_classes(); ?>">

				<?php
					$the_title = get_theme_mod( 'title_search', __( 'Search results', 'nozama-lite' ) );

					global $wp_query;
					$found = intval( $wp_query->found_posts );
					/* translators: %d is the number of search results. */
					$subtitle = esc_html( sprintf( _n( '%d result found.', '%d results found.', $found, 'nozama-lite' ), $found ) );
				?>
				<article class="entry error-404 not-found">
					<header class="entry-header">
						<?php if ( $the_title ) : ?>
							<h1 class="entry-title">
								<?php echo wp_kses( $the_title, nozama_lite_get_allowed_tags() ); ?>
							</h1>
						<?php endif; ?>
					</header>

					<div class="entry-content">
						<?php if ( $subtitle ) : ?>
							<p>
								<?php echo wp_kses( $subtitle, nozama_lite_get_allowed_tags( 'guide' ) ); ?>
							</p>
						<?php endif; ?>
					</div>
				</article>

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
