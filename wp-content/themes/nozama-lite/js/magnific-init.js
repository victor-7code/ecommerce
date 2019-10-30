jQuery(function( $ ) {
	'use strict';

	/* -----------------------------------------
	Image Lightbox
	----------------------------------------- */
	$( ".nozama-lite-lightbox, a[data-lightbox^='gal']" ).magnificPopup({
		type: 'image',
		mainClass: 'mfp-with-zoom',
		gallery: {
			enabled: true
		},
		zoom: {
			enabled: true
		},
		image: {
			titleSrc: function (item) {
				var $item = item.el;
				var $parentCaption = $item.parents('.wp-caption').first();

				if ($item.attr('title')) {
					return $item.attr('title');
				}

				if ($parentCaption) {
					return $parentCaption.find('.wp-caption-text').text();
				}
			},
		},
	} );

});
