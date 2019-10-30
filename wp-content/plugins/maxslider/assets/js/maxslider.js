jQuery(function ($) {
	var $maxSlider = $('.maxslider');

	$maxSlider.each(function () {
		var $this = $(this);
		var navigation = $this.data('navigation');

		$this.slick({
			dots: navigation === 'dots',
			arrows: navigation === 'arrows',
			autoplay: $this.data('autoslide'),
			autoplaySpeed: $this.data('slide-speed'),
			fade: $this.data('effect') === 'fade',
			speed: 600,
			prevArrow: '<span class="slick-arrow-prev"><span class="dashicons dashicons-arrow-left-alt2"></span></span>',
			nextArrow: '<span class="slick-arrow-next"><span class="dashicons dashicons-arrow-right-alt2"></span></span>',
			responsive: [
				{
					breakpoint: 767,
					settings: {
						arrows: false,
						dots: !!navigation
					}
				}
			]
		});
	})
});