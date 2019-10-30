<?php
//
// WooCommerce integration
//

// This needs to be here. Don't remove. Don't wrap this file's include/require in a check like this.
if ( ! class_exists( 'WooCommerce' ) ) {
	return;
}

add_action( 'after_setup_theme', 'nozama_lite_woocommerce_activation' );
function nozama_lite_woocommerce_activation() {

	add_filter( 'woocommerce_enqueue_styles', '__return_empty_array' );

	add_theme_support( 'woocommerce', array(
		'thumbnail_image_width'         => 630,
		'single_image_width'            => 690,
		'gallery_thumbnail_image_width' => 160,
		'product_grid'                  => array(
			'default_columns' => 3,
			'min_columns'     => 2,
			'max_columns'     => 4,
		),
	) );
	add_theme_support( 'wc-product-gallery-zoom' );
	add_theme_support( 'wc-product-gallery-slider' );
	add_theme_support( 'wc-product-gallery-lightbox' );

}

add_action( 'init', 'nozama_lite_woocommerce_integration' );
function nozama_lite_woocommerce_integration() {

	add_filter( 'woocommerce_upsells_total', 'nozama_lite_woocommerce_upsells_total' );
	add_filter( 'woocommerce_cross_sells_total', 'nozama_lite_woocommerce_cross_sells_total' );

	// Only add filter when the query param is set, otherwise the 'woocommerce_catalog_rows' customizer option doesn't appear.
	if ( isset( $_GET['view'] ) ) {
		add_filter( 'loop_shop_per_page', 'nozama_lite_woocommerce_loop_shop_per_page_view' );
	}

	// Shop page
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
	remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30 );
	add_action( 'woocommerce_before_shop_loop', 'nozama_lite_woocommerce_shop_actions', 30 );

	// Shop item
	remove_action( 'woocommerce_before_shop_loop_item', 'woocommerce_template_loop_product_link_open', 10 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_product_link_close', 5 );
	add_action( 'woocommerce_shop_loop_item_title', 'nozama_lite_woocommerce_show_product_loop_categories', 5 );
	remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
	add_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 15 );
	remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );

	// Category
	remove_action( 'woocommerce_before_subcategory', 'woocommerce_template_loop_category_link_open', 10 );
	remove_action( 'woocommerce_after_subcategory', 'woocommerce_template_loop_category_link_close', 10 );
	remove_action( 'woocommerce_before_subcategory_title', 'woocommerce_subcategory_thumbnail', 10 );
	add_action( 'woocommerce_before_subcategory', 'nozama_lite_woocommerce_subcategory_thumbnail', 10 );

	// Single product
	remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
	add_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 3 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
	remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
	add_action( 'woocommerce_single_product_summary', 'nozama_lite_woocommerce_single_product_tags', 30 );
}

add_action( 'wp', 'nozama_lite_woocommerce_integration_late' );
function nozama_lite_woocommerce_integration_late() {
	// These require features that are not yet ready on 'init'.
	// For example, is_product() (which is also implicitly called from nozama_lite_has_sidebar()) doesn't work properly on init.

	// Single product
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_upsell_display', 15 );
	remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );

	// Only add these in single products, as the 'woocommerce_after_main_content' hook exists in other templates too.
	if ( is_product() ) {
		if ( nozama_lite_has_sidebar() ) {
			add_action( 'woocommerce_after_single_product', 'woocommerce_output_product_data_tabs', 10 );
		} else {
			add_action( 'woocommerce_after_main_content', 'woocommerce_output_product_data_tabs', 4 );
		}

		add_action( 'woocommerce_after_main_content', 'woocommerce_upsell_display', 6 );
		add_action( 'woocommerce_after_main_content', 'woocommerce_output_related_products', 8 );
	}
}

add_filter( 'woocommerce_breadcrumb_defaults', 'nozama_lite_woocommerce_breadcrumb_defaults' );
function nozama_lite_woocommerce_breadcrumb_defaults( $args ) {
	$args['wrap_before'] = '<div class="col-12">' . $args['wrap_before'];
	$args['wrap_after']  = $args['wrap_after'] . '</div>';
	$args['delimiter']   = '<span>&sol;</span>';

	return $args;
}

function woocommerce_template_loop_product_title() {
	?>
	<p class="item-title">
		<a href="<?php the_permalink(); ?>">
			<?php the_title(); ?>
		</a>
	</p>
	<?php
}

function nozama_lite_woocommerce_show_product_loop_categories() {
	/** @var $product WC_Product */
	global $product;
	echo wp_kses( wc_get_product_category_list( $product->get_id(), ', ', '<div class="item-meta"><span class="item-categories">', '</span></div>' ), nozama_lite_get_allowed_tags( 'woocommerce_terms' ) );
}

if ( ! function_exists( 'nozama_lite_woocommerce_shop_actions' ) ) :
	function nozama_lite_woocommerce_shop_actions() {
		$actions_class = 'shop-actions-no-filter';

		?>
		<div class="shop-actions <?php echo esc_attr( $actions_class ); ?>">

			<div class="shop-action-results">
				<?php woocommerce_result_count(); ?>

				<?php
					$first  = absint( apply_filters( 'nozama_lite_products_view_first', 25 ) );
					$second = absint( apply_filters( 'nozama_lite_products_view_second', 50 ) );

					$active_class = 'product-number-active';

					$classes = array(
						'first'  => '',
						'second' => '',
						'all'    => '',
					);

					if ( ! empty( $_GET['view'] ) ) {
						if ( 'all' === $_GET['view'] ) {
							$classes['all'] = $active_class;
						} else {
							$view = absint( $_GET['view'] );
							if ( $view === $first ) {
								$classes['first'] = $active_class;
							} elseif ( $view === $second ) {
								$classes['second'] = $active_class;
							}
						}
					}
				?>
				<div class="product-number">
					<span><?php esc_html_e( 'View:', 'nozama-lite' ); ?></span>
					<a href="<?php echo esc_url( add_query_arg( 'view', $first, get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>" class="<?php echo esc_attr( $classes['first'] ); ?>"><?php echo esc_html( $first ); ?></a>
					<a href="<?php echo esc_url( add_query_arg( 'view', $second, get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>" class="<?php echo esc_attr( $classes['second'] ); ?>"><?php echo esc_html( $second ); ?></a>
					<?php if ( apply_filters( 'nozama_lite_products_view_all', true ) ) : ?>
						<a href="<?php echo esc_url( add_query_arg( 'view', 'all', get_permalink( wc_get_page_id( 'shop' ) ) ) ); ?>" class="<?php echo esc_attr( $classes['all'] ); ?>"><?php esc_html_e( 'All', 'nozama-lite' ); ?></a>
					<?php endif; ?>
				</div>
			</div>

			<?php woocommerce_catalog_ordering(); ?>
		</div>
		<?php
	}
endif;

function woocommerce_template_loop_category_title( $category ) {
	?>
	<p class="item-title">
		<a href="<?php echo esc_url( get_term_link( $category, 'product_cat' ) ); ?>">
			<?php
			echo esc_html( $category->name );

			if ( $category->count > 0 ) {
				echo apply_filters( 'woocommerce_subcategory_count_html', ' <mark class="count">(' . esc_html( $category->count ) . ')</mark>', $category ); // WPCS: XSS ok.
			}
			?>
		</a>
	</p>
	<?php
}

function nozama_lite_woocommerce_upsells_total() {
	return 4;
}

function nozama_lite_woocommerce_cross_sells_total() {
	return 2;
}

function nozama_lite_woocommerce_loop_shop_per_page_view( $posts_per_page ) {

	if ( empty( $_GET['view'] ) ) {
		return $posts_per_page;
	}

	if ( 'all' === $_GET['view'] ) {
		$view = - 1;
	} else {
		$view = absint( $_GET['view'] );
	}

	$first  = absint( apply_filters( 'nozama_lite_products_view_first', 25 ) );
	$second = absint( apply_filters( 'nozama_lite_products_view_second', 50 ) );

	$valid_values = array( $posts_per_page );

	if ( $first ) {
		$valid_values[] = $first;
	}

	if ( $second ) {
		$valid_values[] = $second;
	}

	if ( apply_filters( 'nozama_lite_products_view_all', true ) ) {
		$valid_values[] = -1;
	}

	if ( in_array( $view, $valid_values, true ) ) {
		return $view;
	}

	return $posts_per_page;
}

function woocommerce_template_loop_product_thumbnail() {
	/** @var $product WC_Product */
	global $product;
	?>
	<div class="item-thumb">
		<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
			<?php echo woocommerce_get_product_thumbnail(); // WPCS: XSS ok. ?>
		</a>
	</div>
	<?php
}

function nozama_lite_woocommerce_subcategory_thumbnail( $category ) {
	?>
	<div class="item-thumb">
		<a href="<?php echo esc_url( get_term_link( $category, 'product_cat' ) ); ?>">
			<?php woocommerce_subcategory_thumbnail( $category ); ?>
		</a>
	</div>
	<?php
}


// Make some WooCommerce pages get the fullwidth template
add_filter( 'template_include', 'nozama_lite_woocommerce_fullwidth_pages' );
if ( ! function_exists( 'nozama_lite_woocommerce_fullwidth_pages' ) ) :
	function nozama_lite_woocommerce_fullwidth_pages( $template ) {
		$filename = 'templates/full-width-page.php';
		$located  = '';
		if ( file_exists( get_stylesheet_directory() . '/' . $filename ) ) {
			$located = get_stylesheet_directory() . '/' . $filename;
		} elseif ( file_exists( get_template_directory() . '/' . $filename ) ) {
			$located = get_template_directory() . '/' . $filename;
		} else {
			$located = '';
		}

		if ( ! empty( $located ) && ( is_cart() || is_checkout() || is_account_page() ) ) {
			return $located;
		}

		return $template;
	}
endif;

function nozama_lite_woocommerce_get_wrap_login_templates() {
	return array(
		'myaccount/form-login.php',
		'myaccount/form-lost-password.php',
		'myaccount/lost-password-confirmation.php',
		'myaccount/form-reset-password.php',
	);
}

add_action( 'woocommerce_before_template_part', 'nozama_lite_woocommerce_wrap_login_forms_open', 10, 4 );
add_action( 'woocommerce_after_template_part', 'nozama_lite_woocommerce_wrap_login_forms_close', 10, 4 );
function nozama_lite_woocommerce_wrap_login_forms_open( $template_name, $template_path, $located, $args ) {
	if ( ! in_array( $template_name, nozama_lite_woocommerce_get_wrap_login_templates(), true ) ) {
		return;
	}
	$registration_class = '';
	if ( 'myaccount/form-login.php' === $template_name && 'yes' === get_option( 'woocommerce_enable_myaccount_registration' ) ) {
		$registration_class = 'with-register';
	}
	?>
	<div class="wc-form-login <?php echo esc_attr( $registration_class ); ?>">
		<style>.entry-title { display: none; }</style>
	<?php

}
function nozama_lite_woocommerce_wrap_login_forms_close( $template_name, $template_path, $located, $args ) {
	if ( ! in_array( $template_name, nozama_lite_woocommerce_get_wrap_login_templates(), true ) ) {
		return;
	}

	?></div><?php
}

function nozama_lite_woocommerce_single_product_tags() {
	global $product;
	echo wp_kses( wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'nozama-lite' ) . ' ', '</span>' ), nozama_lite_get_allowed_tags( 'woocommerce_terms' ) );
}

add_filter( 'nozama_lite_get_allowed_tags', 'nozama_lite_get_allowed_tags_woocommerce_terms', 10, 2 );
function nozama_lite_get_allowed_tags_woocommerce_terms( $tags, $context ) {
	if ( 'woocommerce_terms' !== $context ) {
		return $tags;
	}

	$tags['a']['rel'] = true;

	$tags['div'] = array( 'class' => true );

	return $tags;
}
