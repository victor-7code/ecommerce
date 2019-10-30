<?php
	/**
	 * Generates CSS based on standard customizer settings.
	 *
	 * @return string
	 */
	function nozama_lite_get_customizer_css() {
		ob_start();

		//
		// Logo
		//
		$custom_logo_id = get_theme_mod( 'custom_logo' );
		if ( get_theme_mod( 'limit_logo_size' ) && ! empty( $custom_logo_id ) ) {
			$image_metadata = wp_get_attachment_metadata( $custom_logo_id );
			$max_width      = floor( $image_metadata['width'] / 2 );
			?>
			.header img.custom-logo {
				width: <?php echo intval( $max_width ); ?>px;
				max-width: 100%;
			}
			<?php
		}


		if ( apply_filters( 'nozama_lite_customizable_header', true ) ) {
			//
			// Header Main Menu Bar
			//
			$header_primary_menu_padding = get_theme_mod( 'header_primary_menu_padding' );

			if ( ! empty( $header_primary_menu_padding ) ) {
				?>
				.head-mast {
					padding-top: <?php echo intval( $header_primary_menu_padding ); ?>px;
					padding-bottom: <?php echo intval( $header_primary_menu_padding ); ?>px;
				}
				<?php
			}

			$header_primary_menu_text_size = get_theme_mod( 'header_primary_menu_text_size' );

			if ( ! empty( $header_primary_menu_text_size ) ) {
				?>
				.navigation-main > li > a {
					font-size: <?php echo intval( $header_primary_menu_text_size ); ?>px;
				}
				<?php
			}
		} // filter nozama_lite_customizable_header


		//
		// Global Colors
		//
		$site_secondary_accent_color = get_theme_mod( 'site_secondary_accent_color' );

		if ( ! empty( $site_secondary_accent_color ) ) {
			?>
			a,
			a:hover,
			.site-tagline,
			.section-title > a,
			.entry-author-socials .social-icon,
			.widget-newsletter-content {
				color: <?php echo sanitize_hex_color( $site_secondary_accent_color ); ?>;
			}
			<?php
		}

		$site_accent_color = get_theme_mod( 'site_accent_color' );

		if ( ! empty( $site_accent_color ) ) {
			?>
			.entry-title a:hover,
			.item-title a:hover,
			.woocommerce-pagination a:hover,
			.woocommerce-pagination .current,
			.navigation a:hover,
			.navigation .current,
			.page-links .page-number:hover,
			.category-search-results-item a,
			.text-theme,
			.sidebar .social-icon:hover,
			.entry-social-share .social-icon:hover,
			.widget-newsletter-content-wrap .fas,
			.widget-newsletter-content-wrap .far,
			.widget_meta li a:hover,
			.widget_pages li a:hover,
			.widget_categories li a:hover,
			.widget_archive li a:hover,
			.widget_nav_menu li a:hover,
			.widget_product_categories li a:hover,
			.widget_layered_nav li a:hover,
			.widget_rating_filter li a:hover,
			.widget_recent_entries a:hover,
			.widget_recent_comments a:hover,
			.widget_rss a:hover,
			.shop-actions .product-number a.product-number-active,
			.shop-filter-toggle i,
			.star-rating,
			.comment-form-rating a,
			.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a:hover,
			.product_list_widget .product-title:hover,
			.navigation-main > li.fas::before,
			.navigation-main > li.far::before,
			.navigation-main > li.fab::before,
			.navigation-main > li:hover > a,
			.navigation-main > li > a:focus,
			.navigation-main > .current-menu-item > a,
			.navigation-main > .current-menu-parent > a,
			.navigation-main > .current-menu-ancestor > a,
			.navigation-main li li:hover > a,
			.navigation-main li li > a:focus,
			.navigation-main li .current-menu-item > a,
			.navigation-main li .current-menu-parent > a,
			.navigation-main li .current-menu-ancestor > a,
			.navigation-main .menu-item-has-children > a::after,
			.navigation-main .page_item_has_children > a::after,
			.navigation-main li .current_page_ancestor > a,
			.navigation-main li .current_page_item > a,
			.navigation-main > .current_page_ancestor > a,
			.navigation-main > .current_page_item > a {
				color: <?php echo sanitize_hex_color( $site_accent_color ); ?>;
			}

			.sidebar .social-icon:hover {
				border-color: <?php echo sanitize_hex_color( $site_accent_color ); ?>;
			}

			.onsale,
			.row-slider-nav .slick-arrow:hover,
			.nozama-lite-slick-slider .slick-arrow:hover,
			.btn,
			.button,
			.comment-reply-link,
			input[type="submit"],
			input[type="reset"],
			button[type="submit"],
			.btn:hover,
			.button:hover,
			.comment-reply-link:hover,
			input[type="submit"]:hover,
			input[type="reset"]:hover,
			button[type="submit"]:hover {
				background-color: <?php echo sanitize_hex_color( $site_accent_color ); ?>;
			}
			<?php
		}

		$site_text_color = get_theme_mod( 'site_text_color' );

		if ( ! empty( $site_text_color ) ) {
			$site_text_color_light = nozama_lite_color_luminance( $site_text_color, 0.3 );
			?>
			body,
			blockquote cite,
			.instagram-pics li a,
			.category-search-select,
			.section-subtitle a,
			.entry-title a,
			.woocommerce-ordering select,
			.shop_table .product-name a,
			.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a,
			.woocommerce-MyAccount-content mark,
			.woocommerce-MyAccount-downloads .download-file a,
			.woocommerce-Address-title a,
			.sidebar .widget_layered_nav_filters a,
			.row-slider-nav .slick-arrow {
				color: <?php echo sanitize_hex_color( $site_text_color ); ?>;
			}

			.comment-metadata a,
			.entry-meta,
			.item-meta,
			.item-meta a,
			.sidebar .widget_recent_entries .post-date,
			.sidebar .tag-cloud-link,
			.breadcrumb,
			.woocommerce-breadcrumb,
			.woocommerce-product-rating .woocommerce-review-link,
			.wc-tabs a,
			.sidebar .product_list_widget .quantity,
			.woocommerce-mini-cart__total {
				color: <?php echo sanitize_hex_color( $site_text_color_light ); ?>;
			}
			<?php
		}

		$site_text_color_secondary = get_theme_mod( 'site_text_color_secondary' );

		if ( ! empty( $site_text_color_secondary ) ) {
			?>
			.entry-meta a,
			.entry-tags a,
			.item-title a,
			.woocommerce-pagination a,
			.woocommerce-pagination span,
			.navigation a,
			.navigation .page-numbers,
			.page-links .page-number,
			.page-links > .page-number,
			.sidebar .social-icon,
			.entry-social-share .social-icon,
			.sidebar-dismiss,
			.sidebar-dismiss:hover,
			.sidebar .widget_meta li a,
			.sidebar .widget_pages li a,
			.sidebar .widget_categories li a,
			.sidebar .widget_archive li a,
			.sidebar .widget_nav_menu li a,
			.sidebar .widget_product_categories li a,
			.sidebar .widget_layered_nav li a,
			.sidebar .widget_rating_filter li a,
			.sidebar .widget_recent_entries a,
			.sidebar .widget_recent_comments a,
			.sidebar .widget_rss a,
			.woocommerce-message a:not(.button),
			.woocommerce-error a:not(.button),
			.woocommerce-info a:not(.button),
			.woocommerce-noreview a:not(.button),
			.breadcrumb a,
			.woocommerce-breadcrumb a,
			.shop-actions a,
			.shop-filter-toggle,
			.entry-summary .product_title,
			.product_meta a,
			.entry-product-info .price,
			.tagged_as a,
			.woocommerce-grouped-product-list-item__label a,
			.reset_variations,
			.wc-tabs li.active a,
			.shop_table .remove,
			.shop_table .product-name a:hover,
			.shop_table .product-subtotal .woocommerce-Price-amount,
			.shipping-calculator-button,
			.sidebar .product_list_widget .product-title {
				color: <?php echo sanitize_hex_color( $site_text_color_secondary ); ?>;
			}

			.price_slider .ui-slider-handle {
				background-color: <?php echo sanitize_hex_color( $site_text_color_secondary ); ?>;
			}
			<?php
		}

		$site_border_color = get_theme_mod( 'site_border_color' );

		if ( ! empty( $site_border_color ) ) {
			$site_border_color_dark = nozama_lite_color_luminance( $site_border_color, -0.2 );
			?>
			hr,
			blockquote,
			.entry-content th,
			.entry-content td,
			textarea,
			select,
			input,
			.no-comments,
			.header-mini-cart-contents,
			.entry-thumb img,
			.item,
			.item-media .item-thumb img,
			.sidebar .social-icon,
			.entry-social-share .social-icon,
			.sidebar .ci-schedule-widget-table tr,
			.sidebar .widget_meta li a,
			.sidebar .widget_pages li a,
			.sidebar .widget_categories li a,
			.sidebar .widget_archive li a,
			.sidebar .widget_nav_menu li a,
			.sidebar .widget_product_categories li a,
			.sidebar .widget_layered_nav li a,
			.sidebar .widget_rating_filter li a,
			.sidebar .widget_recent_entries li,
			.sidebar .widget_recent_comments li,
			.sidebar .widget_rss li,
			.demo_store,
			.woocommerce-product-gallery .flex-viewport,
			.woocommerce-product-gallery .flex-contorl-thumbs li img,
			.woocommerce-product-gallery__wrapper,
			.single-product-table-wrapper,
			.wc-tabs,
			.shop_table.cart,
			.shop_table.cart th,
			.shop_table.cart td,
			.cart-collaterals .shop_table,
			.cart-collaterals .shop_table th,
			.cart-collaterals .shop_table td,
			#order_review_heading,
			.wc_payment_method,
			.payment_box,
			.woocommerce-order-received .customer_details,
			.woocommerce-thankyou-order-details,
			.wc-bacs-bank-details,
			.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a,
			.woocommerce-EditAccountForm fieldset,
			.wc-form-login,
			.sidebar .product_list_widget .product-thumb img,
			.header .widget_shopping_cart li.empty,
			.woocommerce-mini-cart__empty-message,
			.row-slider-nav .slick-arrow {
				border-color: <?php echo sanitize_hex_color( $site_border_color ); ?>;
			}

			textarea,
			select,
			input,
			.select2-container .select2-selection--single,
			.select2-container .select2-search--dropdown .select2-search__field,
			.select2-dropdown {
				border-color: <?php echo sanitize_hex_color( $site_border_color_dark ); ?>;
			}

			.price_slider
			.price_slider .ui-slider-range {
				background-color: <?php echo sanitize_hex_color( $site_border_color ); ?>;
			}
			<?php
		}

		$css = ob_get_clean();
		return apply_filters( 'nozama_lite_customizer_css', $css );
	}

	if ( ! function_exists( 'nozama_lite_get_all_customizer_css' ) ) :
		function nozama_lite_get_all_customizer_css() {
			$styles = array(
				'customizer' => nozama_lite_get_customizer_css(),
			);

			$styles = apply_filters( 'nozama_lite_all_customizer_css', $styles );

			return implode( PHP_EOL, $styles );
		}
	endif;
