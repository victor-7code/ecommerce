<?php
/**
 * The template for displaying product content within loops
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/content-product.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @package WooCommerce/Templates
 * @version 3.6.0
 */

defined( 'ABSPATH' ) || exit;

global $product;

// Ensure visibility.
if ( empty( $product ) || ! $product->is_visible() ) {
	return;
}

$classes = array( 'item', 'item-product' );

global $woocommerce_loop;

$col_class = nozama_lite_get_columns_classes( ! empty( $woocommerce_loop['columns'] ) ? $woocommerce_loop['columns'] : apply_filters( 'loop_shop_columns', 4 ) );
?>
<div class="<?php echo esc_attr( $col_class ); ?>">
	<div <?php wc_product_class( $classes, $product ); ?>>
		<?php
		/**
		 * Hook: woocommerce_before_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_open - 10 // Removed by the theme.
		 */
		do_action( 'woocommerce_before_shop_loop_item' );

		/**
		 * Hook: woocommerce_before_shop_loop_item_title.
		 *
		 * @hooked woocommerce_show_product_loop_sale_flash - 10
		 * @hooked woocommerce_template_loop_product_thumbnail - 10
		 */
		do_action( 'woocommerce_before_shop_loop_item_title' );
		?>

		<div class="item-content">

			<?php
			/**
			 * Hook: woocommerce_shop_loop_item_title.
			 *
			 * @hooked nozama_lite_woocommerce_show_product_loop_categories - 5 // Added by the theme.
			 * @hooked woocommerce_template_loop_product_title - 10
			 */
			do_action( 'woocommerce_shop_loop_item_title' );

			/**
			 * Hook: woocommerce_after_shop_loop_item_title.
			 *
			 * @hooked woocommerce_template_loop_rating - 5 // Removed by the theme.
			 * @hooked woocommerce_template_loop_price - 10
			 * @hooked woocommerce_template_loop_rating - 15 // Added by the theme.
			 */
			do_action( 'woocommerce_after_shop_loop_item_title' );
			?>

		</div>

		<?php
		/**
		 * Hook: woocommerce_after_shop_loop_item.
		 *
		 * @hooked woocommerce_template_loop_product_link_close - 5 // Removed by the theme.
		 * @hooked woocommerce_template_loop_add_to_cart - 10 // Removed by the theme.
		 */
		do_action( 'woocommerce_after_shop_loop_item' );
		?>
	</div>
</div>
