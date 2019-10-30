(function($) {
	$(window).load( function() {
		$( '.nozama-lite-onboarding-notice' ).parents( '.is-dismissible' ).on( 'click', 'button', function( e ) {
			$.ajax( {
				type: 'post',
				url: ajaxurl,
				data: {
					action: 'nozama_lite_dismiss_onboarding',
					nonce: nozama_lite_Onboarding.dismiss_nonce,
					dismissed: true
				},
				dataType: 'text',
				success: function( response ) {
					// console.log( response );
				}
			} );
		});
	});
})(jQuery);
