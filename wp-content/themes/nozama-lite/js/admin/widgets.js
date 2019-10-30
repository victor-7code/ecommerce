jQuery( document ).ready( function( $ ) {
	"use strict";
	var $body = $( 'body' );


	var nozama_lite_initialize_widget = function ( widget_el ) {
		nozama_lite_repeating_sortable_init( widget_el );
		nozama_lite_colorpicker_init( widget_el );
		nozama_lite_alpha_colorpicker_init( widget_el );
		nozama_lite_collapsible_init( widget_el );
	};

	nozama_lite_initialize_widget();

	function nozama_lite_on_customizer_widget_form_update( e, widget_el ) {
		nozama_lite_initialize_widget( widget_el );
	}
	// Widget init doesn't occur for some reason, when added through the customizer. Therefore the event handler below is needed.
	// https://make.wordpress.org/core/2014/04/17/live-widget-previews-widget-management-in-the-customizer-in-wordpress-3-9/
	// 'widget-added' is complemented by 'widget-updated'. However, alpha-color-picker shows multiple alpha channel
	// pickers if called on 'widget-updated'
	// $( document ).on( 'widget-updated', nozama_lite_on_customizer_widget_form_update );
	$( document ).on( 'widget-added', nozama_lite_on_customizer_widget_form_update );


	// Widget Actions on Save
	$( document ).ajaxSuccess( function( e, xhr, options ) {
		if ( options.data.search( 'action=save-widget' ) != -1 ) {
			var widget_id;

			if ( ( widget_id = options.data.match( /widget-id=(ci-.*?-\d+)\b/ ) ) !== null ) {
				var widget = $( "input[name='widget-id'][value='" + widget_id[1] + "']" ).parent();
				nozama_lite_initialize_widget( widget );
			}
		}
	} );


	$body.on( 'click', '.ci-collapsible legend', function() {
		var arrow = $( this ).find( 'i' );
		if ( arrow.hasClass( 'dashicons-arrow-down' ) ) {
			arrow.removeClass( 'dashicons-arrow-down' ).addClass( 'dashicons-arrow-right' );
			$( this ).siblings( '.elements' ).slideUp();
		} else {
			arrow.removeClass( 'dashicons-arrow-right' ).addClass( 'dashicons-arrow-down' );
			$( this ).siblings( '.elements' ).slideDown();
		}
	} );


});

var nozama_lite_collapsible_init = function( selector ) {
	if ( selector === undefined ) {
		jQuery( '.ci-collapsible .elements' ).hide();
		jQuery( '.ci-collapsible legend i' ).removeClass( 'dashicons-arrow-down' ).addClass( 'dashicons-arrow-right' );
	} else {
		jQuery( '.ci-collapsible .elements', selector ).hide();
		jQuery( '.ci-collapsible legend i', selector ).removeClass( 'dashicons-arrow-down' ).addClass( 'dashicons-arrow-right' );
	}
};

var nozama_lite_alpha_colorpicker_init = function( selector ) {
	if ( selector === undefined ) {
		var nozama_lite_AlphaColorPicker = jQuery( '#widgets-right .nozama-lite-alpha-color-picker, #wp_inactive_widgets .nozama-lite-alpha-color-picker' ).filter( function() {
			return !jQuery( this ).parents( '.field-prototype' ).length;
		} );

		nozama_lite_AlphaColorPicker.alphaColorPicker();
	} else {
		jQuery( '.nozama-lite-alpha-color-picker', selector ).alphaColorPicker();
	}
};

var nozama_lite_colorpicker_init = function( selector ) {
	if ( selector === undefined ) {
		var nozama_lite_ColorPicker = jQuery( '#widgets-right .nozama-lite-color-picker, #wp_inactive_widgets .nozama-lite-color-picker' ).filter( function() {
			return !jQuery( this ).parents( '.field-prototype' ).length;
		} );

		// The use of throttle was taken by: https://wordpress.stackexchange.com/questions/5515/update-widget-form-after-drag-and-drop-wp-save-bug/212676#212676
		nozama_lite_ColorPicker.each( function() {
			jQuery( this ).wpColorPicker( {
				change: _.throttle( function () {
					jQuery( this ).trigger( 'change' );
				}, 1000, { leading: false } )
			} );
		} );
	} else {
		jQuery( '.nozama-lite-color-picker', selector ).each( function() {
			jQuery( this ).wpColorPicker( {
				change: _.throttle( function () {
					jQuery( this ).trigger( 'change' );
				}, 1000, { leading: false } )
			} );
		} );
	}
};
