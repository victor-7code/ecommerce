<?php
/**
 * Custom template tags for this theme
 */

add_action( 'nozama_lite_head_mast', 'nozama_lite_header_branding', 10 );
add_action( 'nozama_lite_head_mast', 'nozama_lite_header_search', 20 );

add_action( 'nozama_lite_the_post_header', 'nozama_lite_the_post_entry_date', 10 );
add_action( 'nozama_lite_the_post_header', 'nozama_lite_the_post_entry_title', 20 );
add_action( 'nozama_lite_the_post_header', 'nozama_lite_the_post_entry_meta', 30 );

add_action( 'nozama_lite_the_post_entry_meta', 'nozama_lite_the_post_entry_sticky_label', 10 );
add_action( 'nozama_lite_the_post_entry_meta', 'nozama_lite_the_post_entry_categories', 20 );
add_action( 'nozama_lite_the_post_entry_meta', 'nozama_lite_the_post_entry_author', 30 );
add_action( 'nozama_lite_the_post_entry_meta', 'nozama_lite_the_post_entry_comments_link', 40 );

function nozama_lite_header() {
	do_action( 'nozama_lite_before_header' );

	?>
	<header class="<?php nozama_lite_the_header_classes(); ?>">

		<?php do_action( 'nozama_lite_before_head_mast' ); ?>

		<div class="head-mast">
			<div class="head-mast-container">
				<div class="head-mast-row">
					<?php
						/**
						 * nozama_lite_head_mast hook.
						 *
						 * @hooked nozama_lite_header_branding - 10
						 * @hooked nozama_lite_header_search - 20
						 */
						do_action( 'nozama_lite_head_mast' );
					?>
				</div>
			</div>
		</div>

		<?php do_action( 'nozama_lite_after_head_mast' ); ?>


		<?php do_action( 'nozama_lite_before_head_nav' ); ?>

		<div class="head-nav">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-12">
						<nav class="nav">
							<?php
								wp_nav_menu( array(
									'theme_location' => 'menu-1',
									'container'      => '',
									'menu_id'        => 'header-menu-1',
									'menu_class'     => 'navigation-main',
								) );

								wp_nav_menu( array(
									'theme_location' => 'menu-2',
									'container'      => '',
									'menu_id'        => 'header-menu-2',
									'menu_class'     => 'navigation-main navigation-secondary',
									'fallback_cb'    => '',
								) );
							?>
						</nav>
					</div>
				</div>
			</div>
		</div>

		<?php do_action( 'nozama_lite_after_head_nav' ); ?>

	</header>
	<?php

	do_action( 'nozama_lite_after_header' );
}

function nozama_lite_footer() {
	$sidebars           = array( 'footer-1', 'footer-2', 'footer-3', 'footer-4' );
	$classes            = nozama_lite_footer_widget_area_classes( get_theme_mod( 'footer_layout', nozama_lite_footer_layout_default() ) );
	$has_active_sidebar = false;
	foreach ( $sidebars as $sidebar ) {
		if ( is_active_sidebar( $sidebar ) && $classes[ $sidebar ]['active'] ) {
			$has_active_sidebar = true;
			break;
		}
	}

	do_action( 'nozama_lite_before_footer' );

	?>
	<footer class="<?php nozama_lite_the_footer_classes(); ?>">
		<?php if ( $has_active_sidebar ) : ?>
			<div class="footer-widgets">
				<div class="container">
					<div class="row">
						<?php foreach ( $sidebars as $sidebar ) : ?>
							<?php if ( $classes[ $sidebar ]['active'] ) : ?>
								<div class="<?php echo esc_attr( $classes[ $sidebar ]['class'] ); ?>">
									<?php dynamic_sidebar( $sidebar ); ?>
								</div>
							<?php endif; ?>
						<?php endforeach; ?>
					</div>
				</div>
			</div>
		<?php endif; ?>

		<?php nozama_lite_footer_bottom_bar(); ?>
	</footer>
	<?php

	do_action( 'nozama_lite_after_footer' );
}

function nozama_lite_footer_bottom_bar() {
	if ( ! get_theme_mod( 'footer_show_bottom_bar', 1 ) ) {
		return;
	}

	do_action( 'nozama_lite_before_footer_info' );

	?>
	<div class="footer-info">
		<div class="container">
			<div class="row align-items-center">
				<div class="col-lg-6 col-12">
					<?php $credits = get_theme_mod( 'footer_text', nozama_lite_get_default_footer_text() ); ?>
					<?php if ( $credits || is_customize_preview() ) : ?>
						<p class="footer-copy text-lg-left text-center"><?php echo nozama_lite_sanitize_footer_text( $credits ); ?></p>
					<?php endif; ?>
				</div>

				<div class="col-lg-6 col-12">
					<div class="footer-info-addons text-lg-right text-center">
						<?php $icons = nozama_lite_get_card_icons_array( get_theme_mod( 'footer_card_icons', nozama_lite_get_default_footer_card_icons() ) ); ?>
						<?php if ( ! empty( $icons ) ) : ?>
							<ul class="list-card-icons">
								<?php foreach ( $icons as $icon ) : ?>
									<li><span class="social-icon"><i class="fab <?php echo esc_attr( $icon ); ?>"></i></span></li>
								<?php endforeach; ?>
							</ul>
						<?php endif; ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php

	do_action( 'nozama_lite_after_footer_info' );

}

function nozama_lite_get_default_footer_text( $position = 'left' ) {
	if ( 'right' === $position && get_theme_support( 'nozama-lite-footer-text-right' ) ) {
		/* translators: %s is a URL. */
		$text = sprintf( __( 'Powered by <a href="%s">WordPress</a>', 'nozama-lite' ),
			esc_url( 'https://wordpress.org/' )
		);
	} else {
		if ( ! defined( 'NOZAMA_LITE_WHITELABEL' ) || ! NOZAMA_LITE_WHITELABEL ) {
		/* translators: %s is a URL. */
			$text = sprintf( __( 'A theme by <a href="%s">CSSIgniter</a>', 'nozama-lite' ),
				esc_url( 'https://www.cssigniter.com/' )
			);
		} else {
		/* translators: %1$s is a URL. %2$s is the current site's name/title. */
			$text = sprintf( __( '<a href="%1$s">%2$s</a>', 'nozama-lite' ),
				esc_url( home_url( '/' ) ),
				get_bloginfo( 'name' )
			);
		}
	}

	return apply_filters( 'nozama_lite_default_footer_text', $text, $position );
}

function nozama_lite_sanitize_footer_text( $text ) {
	return wp_kses( $text, nozama_lite_get_allowed_tags( 'guide' ) );
}

function nozama_lite_get_default_footer_card_icons() {
	return apply_filters( 'nozama_lite_default_footer_card_icons', nozama_lite_sanitize_footer_card_icons( 'fa-cc-visa, fa-cc-mastercard, fa-cc-amex, fa-cc-discover, fa-cc-diners-club, fa-cc-paypal, fa-cc-apple-pay, fa-cc-amazon-pay' ) );
}

function nozama_lite_get_card_icons_array( $text ) {
	$icons = explode( ',', $text );
	$icons = array_map( 'trim', $icons );
	$icons = array_map( 'sanitize_key', $icons );
	$icons = array_filter( $icons );

	return $icons;
}

function nozama_lite_sanitize_footer_card_icons( $text ) {
	$icons = nozama_lite_get_card_icons_array( $text );
	$text  = implode( ',' . PHP_EOL, $icons );

	return $text;
}


function nozama_lite_header_branding() {
	?>
	<div class="header-branding-wrap">
		<?php
			ob_start();
			wp_nav_menu( array(
				'theme_location' => 'menu-1',
				'container'      => '',
				'menu_id'        => 'header-menu-1',
				'menu_class'     => 'navigation-main navigation-main-right',
			) );
			$menu = trim( ob_get_clean() );
		?>
		<?php if ( ! empty( $menu ) ) : ?>
			<a href="#mobilemenu" class="mobile-nav-trigger"><i class="fas fa-bars"></i> <span class="sr-only"><?php esc_html_e( 'Menu', 'nozama-lite' ); ?></span></a>
		<?php endif; ?>

		<?php nozama_lite_the_site_identity(); ?>
	</div>
	<?php
}

function nozama_lite_header_search() {
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	?>
	<div class="head-search-form-wrap">
		<form class="category-search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>" method="get">
			<label for="category-search-name" class="sr-only" >
				<?php esc_html_e( 'Category name', 'nozama-lite' ); ?>
			</label>

			<?php wp_dropdown_categories( array(
				'taxonomy'          => 'product_cat',
				'show_option_none'  => esc_html__( 'Search all categories', 'nozama-lite' ),
				'option_none_value' => '',
				'value_field'       => 'slug',
				'hide_empty'        => 1,
				'echo'              => 1,
				'hierarchical'      => 1,
				'name'              => 'product_cat',
				'id'                => 'category-search-name',
				'class'             => 'category-search-select',
			) ); ?>

			<div class="category-search-input-wrap">
				<label for="category-search-input" class="sr-only">
					<?php esc_html_e( 'Search text', 'nozama-lite' ); ?>
				</label>
				<input
					type="text"
					class="category-search-input"
					id="category-search-input"
					placeholder="<?php esc_attr_e( 'What are you looking for?', 'nozama-lite' ); ?>"
					name="s"
					autocomplete="on"
				/>

				<ul class="category-search-results">
					<li class="category-search-results-item">
						<a href="">
							<span class="category-search-results-item-title"></span>
						</a>
					</li>
				</ul>
				<span class="category-search-spinner"></span>
				<input type="hidden" name="post_type" value="product" />
			</div>

			<button type="submit" class="category-search-btn">
				<i class="fas fa-search"></i><span class="sr-only"><?php echo esc_html_x( 'Search', 'submit button', 'nozama-lite' ); ?></span>
			</button>
		</form>
	</div>
	<?php
}


/**
 * Echoes the logo / site title / description, depending on customizer options.
 */
function nozama_lite_the_site_identity() {
	do_action( 'nozama_lite_before_site_identity' );

	?><div class="site-branding"><?php

	if ( has_custom_logo() && get_theme_mod( 'show_site_title', 1 ) ) {
		the_custom_logo();

		?><h1 class="site-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1><?php
	} elseif ( has_custom_logo() ) {
		?><h1 class="site-logo"><?php the_custom_logo(); ?></h1><?php
	} elseif ( get_theme_mod( 'show_site_title', 1 ) ) {
		?><h1 class="site-logo"><a href="<?php echo esc_url( home_url( '/' ) ); ?>" rel="home"><?php bloginfo( 'name' ); ?></a></h1><?php
	}

	if ( get_theme_mod( 'show_site_description', 1 ) ) {
		$description = get_bloginfo( 'description', 'display' );
		if ( $description || is_customize_preview() ) {
			?><p class="site-tagline"><?php echo $description; /* WPCS: xss ok. */ ?></p><?php
		}
	}

	?></div><?php

	do_action( 'nozama_lite_after_site_identity' );
}

/**
 * Echoes header classes based on customizer options
 */
function nozama_lite_the_header_classes() {
	$classes = apply_filters( 'nozama_lite_header_classes', array(
		'header',
		get_theme_mod( 'header_fullwidth' ) ? 'header-fullwidth' : '',
	) );

	$classes = array_filter( $classes );

	echo esc_attr( implode( ' ', $classes ) );
}

/**
 * Echoes header classes based on customizer options
 */
function nozama_lite_the_footer_classes() {
	$classes = apply_filters( 'nozama_lite_footer_classes', array(
		'footer',
		get_theme_mod( 'footer_fullwidth' ) ? 'footer-fullwidth' : '',
	) );

	$classes = array_filter( $classes );

	echo esc_attr( implode( ' ', $classes ) );
}

function nozama_lite_the_post_thumbnail( $size = false ) {
	if ( ! $size ) {
		$size = 'post-thumbnail';
	}

	if ( ! has_post_thumbnail() || ! get_theme_mod( 'post_show_featured', 1 ) ) {
		return;
	}

	do_action( 'nozama_lite_before_the_post_thumbnail' );

	if ( is_singular() && get_the_ID() === get_queried_object_id() ) {
		$caption = nozama_lite_get_image_lightbox_caption( get_post_thumbnail_id() );
		?>
		<figure class="entry-thumb">
			<a class="nozama-lite-lightbox" href="<?php echo esc_url( get_the_post_thumbnail_url( get_the_ID(), 'large' ) ); ?>" title="<?php echo esc_attr( $caption ); ?>">
				<?php the_post_thumbnail( $size ); ?>
			</a>
		</figure>
		<?php
	} else {
		?>
		<figure class="entry-thumb">
			<a href="<?php the_permalink(); ?>">
				<?php the_post_thumbnail( $size ); ?>
			</a>
		</figure>
		<?php
	}

	do_action( 'nozama_lite_after_the_post_thumbnail' );
}

function nozama_lite_the_post_header() {
	ob_start();

	/**
	 * nozama_lite_the_post_header hook.
	 *
	 * @hooked nozama_lite_the_post_entry_date - 10
	 * @hooked nozama_lite_the_post_entry_title - 20
	 * @hooked nozama_lite_the_post_entry_meta - 30
	 */
	do_action( 'nozama_lite_the_post_header' );

	$html = ob_get_clean();

	if ( trim( $html ) ) {
		$html = sprintf( '<header class="entry-header">%s</header>', $html );
	}

	do_action( 'nozama_lite_before_the_post_header', $html );

	echo $html; // WPCS: XSS ok.

	do_action( 'nozama_lite_after_the_post_header', $html );
}

function nozama_lite_the_post_entry_title() {
	if ( is_singular() && get_the_ID() === get_queried_object_id() ) {
		?>
		<h1 class="entry-title">
			<?php the_title(); ?>
		</h1>
		<?php
	} else {
		?>
		<h1 class="entry-title">
			<a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
		</h1>
		<?php
	}
}

function nozama_lite_the_post_entry_meta() {
	ob_start();

	/**
	 * nozama_lite_the_post_entry_meta hook.
	 *
	 * @hooked nozama_lite_the_post_entry_sticky_label - 10
	 * @hooked nozama_lite_the_post_entry_categories - 20
	 * @hooked nozama_lite_the_post_entry_author - 30
	 * @hooked nozama_lite_the_post_entry_comments_link - 40
	 */
	do_action( 'nozama_lite_the_post_entry_meta' );

	$html = ob_get_clean();

	if ( trim( $html ) ) {
		$html = sprintf( '<div class="entry-meta">%s</div>', $html );
	}

	do_action( 'nozama_lite_before_the_post_entry_meta', $html );

	echo $html; // WPCS: XSS ok.

	do_action( 'nozama_lite_after_the_post_entry_meta', $html );
}

function nozama_lite_the_post_entry_sticky_label() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( ! is_singular() && is_sticky() ) {
		?>
		<span class="entry-meta-item entry-sticky">
			<?php esc_html_e( 'Featured', 'nozama-lite' ); ?>
		</span>
		<?php
	}
}

function nozama_lite_the_post_entry_date() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_date', 1 ) ) {
		?>
		<div class="entry-meta">
			<span class="entry-meta-item">
				<time datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo get_the_date(); ?></time>
			</span>
		</div>
		<?php
	}
}

function nozama_lite_the_post_entry_categories() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_categories', 1 ) ) {
		?>
		<span class="entry-meta-item entry-categories">
			<?php the_category( ', ' ); ?>
		</span>
		<?php
	}
}

function nozama_lite_the_post_entry_author() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_author', 1 ) ) {
		?>
		<span class="entry-meta-item entry-author">
			<?php
				printf(
					/* translators: %s is the author's name. */
					esc_html_x( 'by %s', 'post author', 'nozama-lite' ),
					'<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
				);
			?>
		</span>
		<?php
	}
}

function nozama_lite_the_post_entry_comments_link() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_comments', 1 ) ) {
		if ( ! is_single() && ! post_password_required() && ( comments_open() || get_comments_number() ) ) {
			?>
			<span class="entry-meta-item entry-comments-link">
				<?php
					/* translators: %s: post title */
					comments_popup_link( sprintf( wp_kses( __( 'Leave a Comment<span class="screen-reader-text"> on %s</span>', 'nozama-lite' ), array(
						'span' => array(
							'class' => array(),
						),
					) ), get_the_title() ) );
				?>
			</span>
			<?php
		}
	}
}

function nozama_lite_the_post_author_box() {

	do_action( 'nozama_lite_before_the_post_author_box' );

	get_template_part( 'template-parts/authorbox' );

	do_action( 'nozama_lite_after_the_post_author_box' );
}

/**
 * @param string $$context May be 'global' or 'user'. If false, it will try to decide by itself.
 */
function nozama_lite_the_social_icons( $context = false ) {
	$networks    = nozama_lite_get_social_networks();

	$global_urls = array();
	$user_urls   = array();
	$used_urls   = array();

	$global_rss  = get_theme_mod( 'theme_rss_feed', get_bloginfo( 'rss2_url' ) );
	$user_rss    = get_author_feed_link( get_the_author_meta( 'ID' ) );
	$used_rss    = '';

	foreach ( $networks as $network ) {
		if ( get_theme_mod( 'theme_social_' . $network['name'] ) ) {
			$global_urls[ $network['name'] ] = get_theme_mod( 'theme_social_' . $network['name'] );
		}
	}

	foreach ( $networks as $network ) {
		if ( get_the_author_meta( 'user_' . $network['name'] ) ) {
			$user_urls[ $network['name'] ] = get_the_author_meta( 'user_' . $network['name'] );
		}
	}

	if ( 'user' === $context ) {
		$used_urls = $user_urls;
		$used_rss  = $user_rss;
	} elseif ( 'global' === $context ) {
		$used_urls = $global_urls;
		$used_rss  = $global_rss;
	} else {
		$used_urls = $global_urls;
		$used_rss  = $global_rss;

		if ( in_the_loop() ) {
			$used_urls = $user_urls;
			$used_rss  = $user_rss;
		}
	}

	$used_urls = apply_filters( 'nozama_lite_social_icons_used_urls', $used_urls, $context, $global_urls, $user_urls );
	$used_rss  = apply_filters( 'nozama_lite_social_icons_used_rss', $used_rss, $context, $global_rss, $user_rss );

	$has_rss = $used_rss ? true : false;

	// Set the target attribute for social icons.
	$add_target = false;
	if ( get_theme_mod( 'theme_social_target', 1 ) ) {
		$add_target = true;
	}

	if ( count( $used_urls ) > 0 || $has_rss ) {
		do_action( 'nozama_lite_before_the_social_icons' );
		?>
		<ul class="list-social-icons">
			<?php
				$template = '<li><a href="%1$s" class="social-icon"><i class="%2$s"></i></a></li>';

				foreach ( $networks as $network ) {
					if ( ! empty( $used_urls[ $network['name'] ] ) ) {
						$html = sprintf( $template,
							esc_url( $used_urls[ $network['name'] ] ),
							esc_attr( $network['icon'] )
						);

						if ( $add_target ) {
							$html = links_add_target( $html );
						}

						echo wp_kses( $html, nozama_lite_get_allowed_tags() );
					}
				}

				if ( $has_rss ) {
					$html = sprintf( $template,
						$used_rss,
						esc_attr( 'fas fa-rss' )
					);

					if ( $add_target ) {
						$html = links_add_target( $html );
					}

					echo wp_kses( $html, nozama_lite_get_allowed_tags() );
				}
			?>
		</ul>
		<?php
		do_action( 'nozama_lite_after_the_social_icons' );
	}
}


/**
 * Echoes pagination links if applicable. Output depends on pagination method selected from the customizer.
 *
 * @uses the_post_pagination()
 * @uses previous_posts_link()
 * @uses next_posts_link()
 *
 * @param array $args An array of arguments to change default behavior.
 * @param WP_Query|null $query A WP_Query object to paginate. Defaults to null and uses the global $wp_query
 *
 * @return void
 */
function nozama_lite_posts_pagination( $args = array() ) {
	$args = wp_parse_args( $args, apply_filters( 'nozama_lite_posts_pagination_default_args', array(
		'mid_size'           => 1,
		'prev_text'          => _x( 'Previous', 'previous post', 'nozama-lite' ),
		'next_text'          => _x( 'Next', 'next post', 'nozama-lite' ),
		'screen_reader_text' => __( 'Posts navigation', 'nozama-lite' ),
		'container_id'       => '',
		'container_class'    => '',
	) ) );

	global $wp_query;

	$output = '';
	$method = get_theme_mod( 'pagination_method', 'numbers' );

	switch ( $method ) {
		case 'text':
			$output = get_the_posts_navigation( $args );
			break;
		case 'numbers':
		default:
			$output = get_the_posts_pagination( $args );
			break;
	}

	if ( ! empty( $output ) && ! empty( $args['container_id'] ) || ! empty( $args['container_class'] ) ) {
		$output = sprintf( '<div id="%2$s" class="%3$s">%1$s</div>', $output, esc_attr( $args['container_id'] ), esc_attr( $args['container_class'] ) );
	}

	// All markup is from native WordPress functions. The wrapping div is properly escaped above.
	$output_safe = $output;

	echo $output_safe; // WPCS: XSS ok.
}

/**
 * Echoes row classes based on whether the current template has a visible sidebar or not,
 * and depending on sidebar visibility option on single post/pages/etc.
 */
function nozama_lite_the_row_classes() {
	$info = nozama_lite_get_layout_info();
	echo esc_attr( $info['row_classes'] );
}

/**
 * Echoes container classes based on whether
 * the current template has a visible sidebar or not
 */
function nozama_lite_the_container_classes() {
	$info = nozama_lite_get_layout_info();
	echo esc_attr( $info['container_classes'] );
}

/**
 * Echoes container classes based on whether
 * the current template has a visible sidebar or not
 */
function nozama_lite_the_sidebar_classes() {
	$info = nozama_lite_get_layout_info();
	echo esc_attr( $info['sidebar_classes'] );
}


function nozama_lite_the_item_thumbnail( $size = false ) {
	if ( ! $size ) {
		$size = 'post-thumbnail';
	}

	if ( ! has_post_thumbnail() || ! get_theme_mod( 'post_show_featured', 1 ) ) {
		return;
	}

	do_action( 'nozama_lite_before_the_item_thumbnail' );

	?>
	<div class="item-thumb">
		<a href="<?php the_permalink(); ?>">
			<?php the_post_thumbnail( $size ); ?>
		</a>
	</div>
	<?php

	do_action( 'nozama_lite_after_the_item_thumbnail' );
}

function nozama_lite_the_post_item_date() {
	if ( 'post' !== get_post_type() ) {
		return;
	}

	if ( get_theme_mod( 'post_show_date', 1 ) ) {
		?>
		<div class="item-meta">
			<time class="item-date" datetime="<?php echo esc_attr( get_the_date( 'c' ) ); ?>"><?php echo get_the_date(); ?></time>
		</div>
		<?php
	}
}
