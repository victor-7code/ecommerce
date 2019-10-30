/**
 * Base Theme Customizer enhancements for a better user experience.
 *
 * Contains handlers to make Base Theme Customizer preview reload changes asynchronously.
 *
 * https://developer.wordpress.org/themes/customize-api/tools-for-improved-user-experience/#using-postmessage-for-improved-setting-previewing
 */

(function ($) {
	function createStyleSheet(settingName, styles) {
		var $styleElement;

		style = '<style class="' + settingName + '">';
		style += styles.reduce(function (rules, style) {
			rules += style.selectors + '{' + style.property + ':' + style.value + ';} ';
			return rules;
		}, '');
		style += '</style>';

		$styleElement = $('.' + settingName);

		if ($styleElement.length) {
			$styleElement.replaceWith(style);
		} else {
			$('head').append(style);
		}
	}

	//
	// Site title and description.
	//
	wp.customize('blogname', function (value) {
		value.bind(function (to) {
			$('.site-logo a').text(to);
		});
	});

	wp.customize('blogdescription', function (value) {
		value.bind(function (to) {
			$('.site-tagline').text(to);
		});
	});


	//
	// Header Main Menu Bar
	//
	wp.customize('header_primary_menu_padding', function (value) {
		value.bind(function (to) {
			$('.head-mast').css({
				paddingTop: to + 'px',
				paddingBottom: to + 'px'
			});
		});
	});

	wp.customize('header_primary_menu_text_size', function (value) {
		value.bind(function (to) {
			$('.navigation-main > li > a').css('.navigation-main > li > a', to + 'px');
		});
	});

	//
	// Footer Colors
	//
	wp.customize('footer_bg_color', function (value) {
		value.bind(function (to) {
			$('.footer-widgets').css('background-color', to);
		});
	});

	wp.customize('footer_text_color', function (value) {
		value.bind(function (to) {
			$('.footer-widgets,' +
				'.footer-widgets .widget,' +
				'.footer-widgets .widget-title,' +
				'.footer h1,.footer h2,.footer h3,' +
				'.footer h4,.footer h5,.footer h6,' +
				'.footer-widgets .ci-contact-widget-item i').css('color', to);
		});
	});

	wp.customize('footer_link_color', function (value) {
		value.bind(function (to) {
			$('.footer-widgets a,' +
				'.footer-widgets .widget a').css('color', to);
		});
	});

	wp.customize('footer_border_color', function (value) {
		value.bind(function (to) {
			$('.footer-widgets .social-icon,' +
				'.footer-widgets .ci-schedule-widget-table tr,' +
				'.footer-widgets .widget_meta li a,' +
				'.footer-widgets .widget_pages li a,' +
				'.footer-widgets .widget_categories li a,' +
				'.footer-widgets .widget_archive li a,' +
				'.footer-widgets .widget_nav_menu li a,' +
				'.footer-widgets .widget_product_categories li a,' +
				'.footer-widgets .widget_layered_nav li a,' +
				'.footer-widgets .widget_rating_filter li a,' +
				'.footer-widgets .widget_recent_entries li,' +
				'.footer-widgets .widget_recent_comments li,' +
				'.footer-widgets .widget_rss li,' +
				'.footer-widgets .tag-cloud-link').css('border-color', to);
		});
	});

	wp.customize('footer_bottom_bg_color', function (value) {
		value.bind(function (to) {
			$('.footer-info').css('background-color', to);
		});
	});

	wp.customize('footer_bottom_text_color', function (value) {
		value.bind(function (to) {
			$('.footer-info, .footer-info-addons .social-icon').css('color', to);
		});
	});

	wp.customize('footer_bottom_link_color', function (value) {
		value.bind(function (to) {
			$('.footer-info a').css('color', to);
		});
	});

	wp.customize('footer_titles_color', function (value) {
		value.bind(function (to) {
			$('.footer .widget-title, .footer h1,.footer h2, ' +
				'.footer h3, .footer h4, .footer h5, .footer h6').css('color', to);
		});
	});

	wp.customize('theme_lightbox', function (value) {
		value.bind(function (to) {
			if (to) {
				$(".nozama-lite-lightbox, a[data-lightbox^='gal']").magnificPopup({
					type: 'image',
					mainClass: 'mfp-with-zoom',
					gallery: {
						enabled: true
					},
					zoom: {
						enabled: true
					}
				});
			} else {
				$(".nozama-lite-lightbox, a[data-lightbox^='gal']").off('click');
			}
		});
	});


	//
	// Theme global colors
	//
	wp.customize('site_secondary_accent_color', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_secondary_accent_color', [
				{
					property: 'color',
					value: to,
					selectors: 'a,' +
						'a:hover,' +
						'.site-tagline,' +
						'.section-title > a,' +
						'.entry-author-socials .social-icon,' +
						'.widget-newsletter-content'
				}
			]);
		});
	});

	wp.customize('site_accent_color', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_accent_color', [
				{
					property: 'color',
					value: to,
					selectors: '.entry-title a:hover,' +
						'.item-title a:hover,' +
						'.woocommerce-pagination a:hover,' +
						'.woocommerce-pagination .current,' +
						'.navigation a:hover,' +
						'.navigation .current,' +
						'.page-links .page-number:hover,' +
						'.text-theme,' +
						'.sidebar .social-icon:hover,' +
						'.widget-newsletter-content-wrap .fas,' +
						'.widget-newsletter-content-wrap .far,' +
						'.widget_meta li a:hover,' +
						'.widget_pages li a:hover,' +
						'.widget_categories li a:hover,' +
						'.widget_archive li a:hover,' +
						'.widget_nav_menu li a:hover,' +
						'.widget_product_categories li a:hover,' +
						'.widget_layered_nav li a:hover,' +
						'.widget_rating_filter li a:hover,' +
						'.widget_recent_entries a:hover,' +
						'.widget_recent_comments a:hover,' +
						'.widget_rss a:hover,' +
						'.shop-actions .product-number a.product-number-active,' +
						'.shop-filter-toggle i,' +
						'.star-rating,' +
						'.comment-form-rating a,' +
						'.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a:hover,' +
						'.product_list_widget .product-title:hover,' +
						'.product_list_widget .product-title:hover,' +
						'.navigation-main > li.fas::before,' +
						'.navigation-main > li.far::before,' +
						'.navigation-main > li.fab::before,' +
						'.navigation-main > li:hover > a,' +
						'.navigation-main > li > a:focus,' +
						'.navigation-main > .current-menu-item > a,' +
						'.navigation-main > .current-menu-parent > a,' +
						'.navigation-main > .current-menu-ancestor > a,' +
						'.navigation-main li li:hover > a,' +
						'.navigation-main li li > a:focus,' +
						'.navigation-main li .current-menu-item > a,' +
						'.navigation-main li .current-menu-parent > a,' +
						'.navigation-main li .current-menu-ancestor > a,' +
						'.navigation-main .menu-item-has-children > a::after,' +
						'.navigation-main .page_item_has_children > a::after,' +
						'.navigation-main li .current_page_ancestor > a,' +
						'.navigation-main li .current_page_item > a,' +
						'.navigation-main > .current_page_ancestor > a,' +
						'.navigation-main > .current_page_item > a'
				},
				{
					property: 'border-color',
					value: to,
					selectors: '.sidebar .social-icon:hover',
				},
				{
					property: 'background-color',
					value: to,
					selectors: '.onsale',
				},
			]);
		});
	});

	wp.customize('site_text_color', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_text_color', [
				{
					property: 'color',
					value: to,
					selectors: 'body,' +
						'blockquote cite,' +
						'.instagram-pics li a,' +
						'.category-search-select,' +
						'.section-subtitle a,' +
						'.entry-title a,' +
						'.woocommerce-ordering select,' +
						'.shop_table .product-name a,' +
						'.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a,' +
						'.woocommerce-MyAccount-content mark,' +
						'.woocommerce-MyAccount-downloads .download-file a,' +
						'.woocommerce-Address-title a,' +
						'.sidebar .widget_layered_nav_filters a,' +
						'.row-slider-nav .slick-arrow,.comment-metadata a,' +
						'.entry-meta,' +
						'.item-meta,' +
						'.item-meta a,' +
						'.sidebar .widget_recent_entries .post-date,' +
						'.sidebar .tag-cloud-link,' +
						'.breadcrumb,' +
						'.woocommerce-breadcrumb,' +
						'.woocommerce-product-rating .woocommerce-review-link,' +
						'.wc-tabs a,' +
						'.sidebar .product_list_widget .quantity,' +
						'.woocommerce-mini-cart__total'
				},
			]);
		});
	});

	wp.customize('site_text_color_secondary', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_text_color_secondary', [
				{
					property: 'color',
					value: to,
					selectors: '.entry-meta a,' +
						'.entry-tags a,' +
						'.item-title a,' +
						'.woocommerce-pagination a,' +
						'.woocommerce-pagination span,' +
						'.navigation a,' +
						'.navigation .page-numbers,' +
						'.page-links .page-number,' +
						'.page-links > .page-number,' +
						'.sidebar .social-icon,' +
						'.sidebar-dismiss,' +
						'.sidebar-dismiss:hover,' +
						'.sidebar .widget_meta li a,' +
						'.sidebar .widget_pages li a,' +
						'.sidebar .widget_categories li a,' +
						'.sidebar .widget_archive li a,' +
						'.sidebar .widget_nav_menu li a,' +
						'.sidebar .widget_product_categories li a,' +
						'.sidebar .widget_layered_nav li a,' +
						'.sidebar .widget_rating_filter li a,' +
						'.sidebar .widget_recent_entries a,' +
						'.sidebar .widget_recent_comments a,' +
						'.sidebar .widget_rss a,' +
						'.woocommerce-message a:not(.button),' +
						'.woocommerce-error a:not(.button),' +
						'.woocommerce-info a:not(.button),' +
						'.woocommerce-noreview a:not(.button),' +
						'.breadcrumb a,' +
						'.woocommerce-breadcrumb a,' +
						'.shop-actions a,' +
						'.shop-filter-toggle,' +
						'.entry-summary .product_title,' +
						'.product_meta a,' +
						'.entry-product-info .price,' +
						'.tagged_as a,' +
						'.woocommerce-grouped-product-list-item__label a,' +
						'.reset_variations,' +
						'.wc-tabs li.active a,' +
						'.shop_table .remove,' +
						'.shop_table .product-name a:hover,' +
						'.shop_table .product-subtotal .woocommerce-Price-amount,' +
						'.shipping-calculator-button,' +
						'.sidebar .product_list_widget .product-title'
				},
				{
					property: 'background-color',
					value: to,
					selectors: '.price_slider .ui-slider-handle',
				},
			]);
		});
	});

	wp.customize('site_border_color', function (value) {
		value.bind(function (to) {
			createStyleSheet('site_border_color', [
				{
					property: 'border-color',
					value: to,
					selectors: 'hr,' +
						'blockquote,' +
						'.entry-content th,' +
						'.entry-content td,' +
						'textarea,' +
						'select,' +
						'input,' +
						'.no-comments,' +
						'.header-mini-cart-contents,' +
						'.entry-thumb img,' +
						'.item,' +
						'.item-media .item-thumb img,' +
						'.sidebar .social-icon,' +
						'.sidebar .ci-schedule-widget-table tr,' +
						'.sidebar .widget_meta li a,' +
						'.sidebar .widget_pages li a,' +
						'.sidebar .widget_categories li a,' +
						'.sidebar .widget_archive li a,' +
						'.sidebar .widget_nav_menu li a,' +
						'.sidebar .widget_product_categories li a,' +
						'.sidebar .widget_layered_nav li a,' +
						'.sidebar .widget_rating_filter li a,' +
						'.sidebar .widget_recent_entries li,' +
						'.sidebar .widget_recent_comments li,' +
						'.sidebar .widget_rss li,' +
						'.demo_store,' +
						'.woocommerce-product-gallery .flex-viewport,' +
						'.woocommerce-product-gallery .flex-contorl-thumbs li img,' +
						'.woocommerce-product-gallery__wrapper,' +
						'.single-product-table-wrapper,' +
						'.wc-tabs,' +
						'.shop_table.cart,' +
						'.shop_table.cart th,' +
						'.shop_table.cart td,' +
						'.cart-collaterals .shop_table,' +
						'.cart-collaterals .shop_table th,' +
						'.cart-collaterals .shop_table td,' +
						'#order_review_heading,' +
						'.wc_payment_method,' +
						'.payment_box,' +
						'.woocommerce-order-received .customer_details,' +
						'.woocommerce-thankyou-order-details,' +
						'.wc-bacs-bank-details,' +
						'.woocommerce-MyAccount-navigation .woocommerce-MyAccount-navigation-link a,' +
						'.woocommerce-EditAccountForm fieldset,' +
						'.wc-form-login,' +
						'.sidebar .product_list_widget .product-thumb img,' +
						'.header .widget_shopping_cart li.empty,' +
						'.woocommerce-mini-cart__empty-message,' +
						'.row-slider-nav .slick-arrow,' +
						'textarea,' +
						'select,' +
						'input,' +
						'.select2-container .select2-selection--single,' +
						'.select2-container .select2-search--dropdown .select2-search__field,' +
						'.select2-dropdown'
				},
				{
					property: 'background-color',
					value: to,
					selectors: '.price_slider' +
						'.price_slider .ui-slider-range'
				},
			]);
		});
	});
})(jQuery);
