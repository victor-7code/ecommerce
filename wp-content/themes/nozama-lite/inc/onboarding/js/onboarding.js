jQuery( document ).ready( function ( $ ) {
	$( 'body' ).on( 'click', '.nozama-lite-onboarding-wrap .install-now ', function () {
		var slug = $( this ).attr( 'data-slug' );

		wp.updates.installPlugin(
			{
				slug: slug
			}
		);

		return false;
	} );

	$( document ).on( 'DOMNodeInserted','.activate-now', function () {
		var activateButton = $( this );
		if (activateButton.length) {
			var url = $( activateButton ).attr( 'href' );
			if (typeof url !== 'undefined') {
				// Request plugin activation.
				$.ajax(
					{
						beforeSend: function () {
							$( activateButton ).replaceWith( '<a class="button updating-message">' + nozama_lite_onboarding.activating_text + '</a>' );
						},
						async: true,
						type: 'GET',
						url: url,
						success: function () {
							// Reload the page.
							location.reload();
						}
					}
				);
			}
		}
	} );

	$( document ).on( 'click','.activate-now', function () {
		var activateButton = $( this );
		if (activateButton.length) {
			var url = $( activateButton ).attr( 'href' );
			if (typeof url !== 'undefined') {
				// Request plugin activation.
				$.ajax(
					{
						beforeSend: function () {
							$( activateButton ).replaceWith( '<a class="button updating-message">' + nozama_lite_onboarding.activating_text + '</a>' );
						},
						async: true,
						type: 'GET',
						url: url,
						success: function () {
							// Reload the page.
							location.reload();
						}
					}
				);
			}
		}

		return false;
	} );

	$( '.ajax-install-plugin' ).on( 'click', function( e ) {
		var button = $(this);
		var plugin_slug = button.data('plugin-slug');
		$.ajax( {
			type: 'post',
			url: ajaxurl,
			data: {
				action: 'install_nozama_lite_plugin',
				onboarding_nonce: nozama_lite_onboarding.onboarding_nonce,
				plugin_slug: plugin_slug,
			},
			dataType: 'text',
			beforeSend: function() {
				button.addClass('updating-message');
				button.text(nozama_lite_onboarding.installing_text);
			},
			success: function( response ) {
				button.removeClass('updating-message');
				button.addClass('activate-now button-primary');
				button.text(nozama_lite_onboarding.activate_text);
			}
		} );
	} );

} );
