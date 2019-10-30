<?php
	if ( class_exists( 'WooCommerce' ) ) {
		// A div.col-12 wraps the breadcrumbs via the 'woocommerce_breadcrumb_defaults' filter.
		woocommerce_breadcrumb();
	}
