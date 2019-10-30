<?php
add_action( 'admin_init', 'nozama_lite_cpt_page_add_metaboxes' );
add_action( 'save_post', 'nozama_lite_cpt_page_update_meta' );

if ( ! function_exists( 'nozama_lite_cpt_page_add_metaboxes' ) ) :
	function nozama_lite_cpt_page_add_metaboxes() {
		add_meta_box( 'nozama-lite-tpl-front-page', esc_html__( 'Front Page Options', 'nozama-lite' ), 'nozama_lite_add_page_front_page_meta_box', 'page', 'normal', 'high' );
	}
endif;

if ( ! function_exists( 'nozama_lite_cpt_page_update_meta' ) ) :
	function nozama_lite_cpt_page_update_meta( $post_id ) {

		if ( ! nozama_lite_can_save_meta( 'page' ) ) {
			return;
		}

		// nonce verification is being done inside nozama_lite_can_save_meta()
		if ( isset( $_POST['nozama_lite_front_slider_id'] ) ) {
			update_post_meta( $post_id, 'nozama_lite_front_slider_id', nozama_lite_sanitize_intval_or_empty( wp_unslash( $_POST['nozama_lite_front_slider_id'] ) ) ); // WPCS: CSRF ok.
		}
	}
endif;

if ( ! function_exists( 'nozama_lite_add_page_front_page_meta_box' ) ) :
	function nozama_lite_add_page_front_page_meta_box( $object, $box ) {
		nozama_lite_prepare_metabox( 'page' );

		?>
		<div class="ci-cf-wrap">
			<div class="ci-cf-section">

				<h3 class="ci-cf-title"><?php esc_html_e( 'Slider', 'nozama-lite' ); ?></h3>

				<div class="ci-cf-inside">
					<p class="ci-cf-guide"><?php esc_html_e( 'You can select a MaxSlider slideshow to display on your front page. If you choose a slideshow, it will be displayed instead of the image that you have set on "Hero section".', 'nozama-lite' ); ?></p>

					<p class="ci-field-group ci-field-dropdown">
						<label for="background_slider_id"><?php esc_html_e( 'MaxSlider Slideshow:', 'nozama-lite' ); ?></label>
						<?php
							$post_type = 'maxslider_slide';
							if ( function_exists( 'MaxSlider' ) ) {
								$post_type = MaxSlider()->post_type;
							}
							nozama_lite_dropdown_posts( array(
								'post_type'            => $post_type,
								'selected'             => get_post_meta( $object->ID, 'nozama_lite_front_slider_id', true ),
								'class'                => 'posts_dropdown',
								'show_option_none'     => esc_html__( 'Disable Slideshow', 'nozama-lite' ),
								'select_even_if_empty' => true,
							), 'nozama_lite_front_slider_id' );
						?>
					</p>
				</div>
			</div>
		</div>
		<?php

		nozama_lite_bind_metabox_to_page_template( 'nozama-lite-tpl-front-page', 'templates/front-page.php', 'nozama_lite_front_page_metabox_tpl' );
	}
endif;

function nozama_lite_prepare_metabox( $post_type ) {
	wp_nonce_field( basename( __FILE__ ), $post_type . '_nonce' );
}

function nozama_lite_can_save_meta( $post_type ) {
	global $post;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return false;
	}

	if ( ! isset( $_POST[ $post_type . '_nonce' ] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST[ $post_type . '_nonce' ] ) ), basename( __FILE__ ) ) ) {
		return false;
	}

	if ( isset( $_POST['post_view'] ) && 'list' === $_POST['post_view'] ) {
		return false;
	}

	if ( ! isset( $_POST['post_type'] ) || $post_type !== $_POST['post_type'] ) {
		return false;
	}

	$post_type_obj = get_post_type_object( $post->post_type );
	if ( ! current_user_can( $post_type_obj->cap->edit_post, $post->ID ) ) {
		return false;
	}

	return true;
}

function nozama_lite_bind_metabox_to_page_template( $metabox_id, $template_file, $js_var ) {
	if ( is_string( $template_file ) && ( '' === $template_file || 'default' === $template_file ) ) {
		$template_file = array( '', 'default' );
	} elseif ( is_array( $template_file ) && ( in_array( '', $template_file, true ) || in_array( 'default', $template_file, true ) ) ) {
		$template_file = array_unique( array_merge( $template_file, array( '', 'default' ) ) );
	}

	if ( is_array( $template_file ) ) {
		$template_file = implode( "', '", $template_file );
	}

	$css = sprintf( '<style type="text/css">%s { display: none; }</style>', '#' . $metabox_id );

	$js = <<<ENDJS
    (function($) {
		$('head').append('{$css}');

	    $(window).load( function() {
			var template_box = $( '#page_template, .editor-page-attributes__template select' );
			var {$js_var} = $( '#{$metabox_id}' );
			if ( template_box.length > 0 ) {
				var {$js_var}_template = [ '{$template_file}' ];
		
				if ( $.inArray( template_box.val(), {$js_var}_template ) > -1 ) {
					{$js_var}.show();
				}
		
				template_box.change( function() {
					if ( $.inArray( template_box.val(), {$js_var}_template ) > -1 ) {
						{$js_var}.show();
						if ( typeof google === 'object' && typeof google.maps === 'object' ) {
							if ( {$js_var}.find( '.gllpLatlonPicker' ).length > 0 ) {
								google.maps.event.trigger( window, 'resize', {} );
							}
						}
					} else {
						{$js_var}.hide();
					}
				} );
			} else {
				{$js_var}.hide();
			}
	    } );
    })(jQuery);
ENDJS;

	wp_add_inline_script( 'nozama-lite-plugin-post-meta', $js );
}
