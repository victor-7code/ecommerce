<?php
	if ( ! class_exists( 'WooCommerce' ) ) {
		return;
	}

	global $product;
	$old_product = $product;

	$product = wc_get_product( get_the_ID() );
?>
<div class="item item-product">

	<?php woocommerce_show_product_loop_sale_flash(); ?>

	<?php woocommerce_template_loop_product_thumbnail(); ?>

	<div class="item-content">
		<?php nozama_lite_woocommerce_show_product_loop_categories(); ?>

		<?php woocommerce_template_loop_product_title(); ?>

		<?php woocommerce_template_loop_price(); ?>

		<?php woocommerce_template_loop_rating(); ?>
	</div>
</div>
<?php

$product = $old_product;
