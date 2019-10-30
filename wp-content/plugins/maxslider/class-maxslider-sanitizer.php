<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class MaxSlider_Sanitizer {
	/**
	 * Sanitizes a slider (repeatable slides).
	 *
	 * @since 1.0.0
	 *
	 * @uses MaxSlider_Sanitizer::slider_slide()
	 *
	 * @param array $POST_slides Input values to sanitize, as passed by the slides metabox.
	 * @param int|null $post_id Optional. Post ID where the slide belongs to.
	 *
	 * @return array
	 */
	public static function metabox_slider( $POST_slides, $post_id = null ) {
		if ( empty( $POST_slides ) || ! is_array( $POST_slides ) ) {
			return array();
		}

		$slides = array();

		foreach ( $POST_slides as $uid => $slide_data ) {
			$slide = self::slider_slide( $slide_data, $post_id, $uid );
			if ( false !== $slide ) {
				$slides[] = $slide;
			}
		}

		return apply_filters( 'maxslider_sanitize_slider', $slides, $POST_slides, $post_id );
	}

	/**
	 * Sanitizes a single slide.
	 *
	 * @since 1.0.0
	 *
	 * @uses MaxSlider::get_default_slide_values()
	 *
	 * @param array $slide Input values to sanitize.
	 * @param int|null $post_id Optional. Post ID where the slide belongs to.
	 * @param string $slide_uid Optional. UID that identifies the slide in the metabox list.
	 *
	 * @return array|false Array if at least one field is completed, false otherwise.
	 */
	public static function slider_slide( $slide, $post_id = null, $slide_uid = '' ) {
		$slide = wp_parse_args( $slide, MaxSlider::get_default_slide_values() );

		if ( isset( $slide['is_template'] ) && intval( $slide['is_template'] ) ) {
			return false;
		}

		$sanitized_slide = array();

		$sanitized_slide['image_id'] = intval( $slide['image_id'] );

		$sanitized_slide['title']       = wp_kses( $slide['title'], self::allowed_tags( 'title' ) );
		$sanitized_slide['title_size']  = self::intval_or_empty( $slide['title_size'] );
		$sanitized_slide['title_color'] = self::hex_color( $slide['title_color'] );

		$sanitized_slide['subtitle']       = wp_kses( $slide['subtitle'], self::allowed_tags( 'subtitle' ) );
		$sanitized_slide['subtitle_size']  = self::intval_or_empty( $slide['subtitle_size'] );
		$sanitized_slide['subtitle_color'] = self::hex_color( $slide['subtitle_color'] );

		$sanitized_slide['button']          = wp_kses( $slide['button'], self::allowed_tags( 'button' ) );
		$sanitized_slide['button_url']      = esc_url_raw( $slide['button_url'] );
		$sanitized_slide['button_fg_color'] = self::hex_color( $slide['button_fg_color'] );
		$sanitized_slide['button_bg_color'] = self::hex_color( $slide['button_bg_color'] );
		$sanitized_slide['button_size']     = self::slide_button_size( $slide['button_size'] );

		$sanitized_slide['content_align']    = self::slide_content_align( $slide['content_align'] );
		$sanitized_slide['content_valign']   = self::slide_content_valign( $slide['content_valign'] );
		$sanitized_slide['content_bg_color'] = self::rgba_color( $slide['content_bg_color'] );
		$sanitized_slide['overlay_color']    = self::rgba_color( $slide['overlay_color'] );


		$sanitized_slide = array_map( 'trim', $sanitized_slide );

		$tmp = array_filter( $sanitized_slide );
		if ( empty( $tmp ) ) {
			$sanitized_slide = false;
		}

		return apply_filters( 'maxslider_sanitize_playlist_track', $sanitized_slide, $slide, $post_id, $slide_uid );
	}

	/**
	 * Return a list of allowed tags and attributes for a given context.
	 *
	 * @param string $context The context for which to retrieve tags.
	 *                        Currently available contexts: guide
	 * @return array List of allowed tags and their allowed attributes.
	 */
	public static function allowed_tags( $context = '' ) {
		$allowed = array(
			'a'       => array(
				'href'   => true,
				'title'  => true,
				'class'  => true,
				'target' => true,
			),
			'abbr'    => array( 'title' => true ),
			'acronym' => array( 'title' => true ),
			'b'       => array( 'class' => true ),
			'br'      => array(),
			'code'    => array( 'class' => true ),
			'em'      => array( 'class' => true ),
			'i'       => array( 'class' => true ),
			'img'     => array(
				'alt'    => true,
				'class'  => true,
				'src'    => true,
				'width'  => true,
				'height' => true,
			),
			'li'      => array( 'class' => true ),
			'ol'      => array( 'class' => true ),
			'pre'     => array( 'class' => true ),
			'span'    => array( 'class' => true ),
			'strong'  => array( 'class' => true ),
			'ul'      => array( 'class' => true ),
		);

		switch ( $context ) {
			case 'button':
				unset( $allowed['a'] );
				break;
			default:
				break;
		}

		return apply_filters( 'maxslider_sanitize_allowed_tags', $allowed, $context );
	}

	public static function slide_button_size( $value ) {
		$choices = MaxSlider::get_slide_button_sizes();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		$default = MaxSlider::get_default_slide_values();
		$default = $default['button_size'];
		return $default;
	}

	/**
	 * Sanitizes slider navigation type.
	 *
	 * @version 1.1.0
	 * @since 1.1.0
	 *
	 * @uses MaxSlider::get_slider_navigation_options()
	 *
	 * @param string $value Input value to sanitize.
	 *
	 * @return string
	 */
	public static function slider_navigation( $value ) {
		$choices = MaxSlider::get_slider_navigation_options();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		$default = MaxSlider::get_default_slide_values();
		$default = $default['navigation'];
		return $default;
	}

	/**
	 * Sanitizes slider navigation position.
	 *
	 * @version 1.1.0
	 * @since 1.1.0
	 *
	 * @uses MaxSlider::get_slider_navigation_position_options()
	 *
	 * @param string $value Input value to sanitize.
	 *
	 * @return string
	 */
	public static function slider_navigation_position( $value ) {
		$choices = MaxSlider::get_slider_navigation_position_options();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		$default = MaxSlider::get_default_slide_values();
		$default = $default['navigation_position'];
		return $default;
	}

	public static function slide_content_align( $value ) {
		$choices = MaxSlider::get_slide_content_align_options();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		$default = MaxSlider::get_default_slide_values();
		$default = $default['content_align'];
		return $default;
	}

	public static function slide_content_valign( $value ) {
		$choices = MaxSlider::get_slide_content_valign_options();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		$default = MaxSlider::get_default_slide_values();
		$default = $default['content_valign'];
		return $default;
	}

	public static function slide_effect( $value ) {
		$choices = MaxSlider::get_slide_effects();
		if ( array_key_exists( $value, $choices ) ) {
			return $value;
		}

		return 'fade';
	}

	/**
	 * Sanitizes a checkbox value.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|bool $input Input value to sanitize.
	 *
	 * @return int|string Returns 1 if $input evaluates to 1, an empty string otherwise.
	 */
	public static function checkbox( $input ) {
		if ( 1 == $input ) {
			return 1;
		}

		return '';
	}

	/**
	 * Sanitizes a checkbox value. Value is passed by reference.
	 *
	 * Useful when sanitizing form checkboxes. Since browsers don't send any data when a checkbox
	 * is not checked, checkbox() throws an error.
	 * checkbox_ref() however evaluates &$input as null so no errors are thrown.
	 *
	 * @since 1.0.0
	 *
	 * @param int|string|bool &$input Input value to sanitize.
	 *
	 * @return int|string Returns 1 if $input evaluates to 1, an empty string otherwise.
	 */
	public static function checkbox_ref( &$input ) {
		if ( 1 == $input ) {
			return 1;
		}

		return '';
	}


	/**
	 * Sanitizes integer input while differentiating zero from empty string.
	 *
	 * @since 1.0.0
	 *
	 * @param mixed $input Input value to sanitize.
	 *
	 * @return int|string Integer value (including zero), or an empty string otherwise.
	 */
	public static function intval_or_empty( $input ) {
		if ( is_null( $input ) || false === $input || '' === $input ) {
			return '';
		}

		if ( 0 == $input ) {
			return 0;
		}

		return intval( $input );
	}


	/**
	 * Returns a sanitized hex color code.
	 *
	 * @since 1.0.0
	 *
	 * @param string $str The color string to be sanitized.
	 * @param bool $return_hash Whether to return the color code prepended by a hash.
	 * @param string $return_fail The value to return on failure.
	 *
	 * @return string A valid hex color code on success, an empty string on failure.
	 */
	public static function hex_color( $str, $return_hash = true, $return_fail = '' ) {
		if ( false === $str || empty( $str ) || 'false' === $str ) {
			return $return_fail;
		}

		// Allow keywords and predefined colors
		if ( in_array( $str, array( 'transparent', 'initial', 'inherit', 'black', 'silver', 'gray', 'grey', 'white', 'maroon', 'red', 'purple', 'fuchsia', 'green', 'lime', 'olive', 'yellow', 'navy', 'blue', 'teal', 'aqua', 'orange', 'aliceblue', 'antiquewhite', 'aquamarine', 'azure', 'beige', 'bisque', 'blanchedalmond', 'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse', 'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray', 'darkgrey', 'darkgreen', 'darkkhaki', 'darkmagenta', 'darkolivegreen', 'darkorange', 'darkorchid', 'darkred', 'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray', 'darkslategrey', 'darkturquoise', 'darkviolet', 'deeppink', 'deepskyblue', 'dimgray', 'dimgrey', 'dodgerblue', 'firebrick', 'floralwhite', 'forestgreen', 'gainsboro', 'ghostwhite', 'gold', 'goldenrod', 'greenyellow', 'grey', 'honeydew', 'hotpink', 'indianred', 'indigo', 'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen', 'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan', 'lightgoldenrodyellow', 'lightgray', 'lightgreen', 'lightgrey', 'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue', 'lightslategray', 'lightslategrey', 'lightsteelblue', 'lightyellow', 'limegreen', 'linen', 'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple', 'mediumseagreen', 'mediumslateblue', 'mediumspringgreen', 'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream', 'mistyrose', 'moccasin', 'navajowhite', 'oldlace', 'olivedrab', 'orangered', 'orchid', 'palegoldenrod', 'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip', 'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown', 'seagreen', 'seashell', 'sienna', 'skyblue', 'slateblue', 'slategray', 'slategrey', 'snow', 'springgreen', 'steelblue', 'tan', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'whitesmoke', 'yellowgreen', 'rebeccapurple' ), true ) ) {
			return $str;
		}

		// Include the hash if not there.
		// The regex below depends on in.
		if ( substr( $str, 0, 1 ) !== '#' ) {
			$str = '#' . $str;
		}

		preg_match( '/(#)([0-9a-fA-F]{6})/', $str, $matches );

		if ( count( $matches ) === 3 ) {
			if ( $return_hash ) {
				return $matches[1] . $matches[2];
			} else {
				return $matches[2];
			}
		}

		return $return_fail;
	}


	public static function rgba_color( $str, $return_hash = true, $return_fail = '' ) {
		if ( false === $str || empty( $str ) || 'false' === $str ) {
			return $return_fail;
		}

		// Allow keywords and predefined colors
		if ( in_array( $str, array( 'transparent', 'initial', 'inherit', 'black', 'silver', 'gray', 'grey', 'white', 'maroon', 'red', 'purple', 'fuchsia', 'green', 'lime', 'olive', 'yellow', 'navy', 'blue', 'teal', 'aqua', 'orange', 'aliceblue', 'antiquewhite', 'aquamarine', 'azure', 'beige', 'bisque', 'blanchedalmond', 'blueviolet', 'brown', 'burlywood', 'cadetblue', 'chartreuse', 'chocolate', 'coral', 'cornflowerblue', 'cornsilk', 'crimson', 'darkblue', 'darkcyan', 'darkgoldenrod', 'darkgray', 'darkgrey', 'darkgreen', 'darkkhaki', 'darkmagenta', 'darkolivegreen', 'darkorange', 'darkorchid', 'darkred', 'darksalmon', 'darkseagreen', 'darkslateblue', 'darkslategray', 'darkslategrey', 'darkturquoise', 'darkviolet', 'deeppink', 'deepskyblue', 'dimgray', 'dimgrey', 'dodgerblue', 'firebrick', 'floralwhite', 'forestgreen', 'gainsboro', 'ghostwhite', 'gold', 'goldenrod', 'greenyellow', 'grey', 'honeydew', 'hotpink', 'indianred', 'indigo', 'ivory', 'khaki', 'lavender', 'lavenderblush', 'lawngreen', 'lemonchiffon', 'lightblue', 'lightcoral', 'lightcyan', 'lightgoldenrodyellow', 'lightgray', 'lightgreen', 'lightgrey', 'lightpink', 'lightsalmon', 'lightseagreen', 'lightskyblue', 'lightslategray', 'lightslategrey', 'lightsteelblue', 'lightyellow', 'limegreen', 'linen', 'mediumaquamarine', 'mediumblue', 'mediumorchid', 'mediumpurple', 'mediumseagreen', 'mediumslateblue', 'mediumspringgreen', 'mediumturquoise', 'mediumvioletred', 'midnightblue', 'mintcream', 'mistyrose', 'moccasin', 'navajowhite', 'oldlace', 'olivedrab', 'orangered', 'orchid', 'palegoldenrod', 'palegreen', 'paleturquoise', 'palevioletred', 'papayawhip', 'peachpuff', 'peru', 'pink', 'plum', 'powderblue', 'rosybrown', 'royalblue', 'saddlebrown', 'salmon', 'sandybrown', 'seagreen', 'seashell', 'sienna', 'skyblue', 'slateblue', 'slategray', 'slategrey', 'snow', 'springgreen', 'steelblue', 'tan', 'thistle', 'tomato', 'turquoise', 'violet', 'wheat', 'whitesmoke', 'yellowgreen', 'rebeccapurple' ), true ) ) {
			return $str;
		}

		preg_match( '/rgba\(\s*(\d{1,3}\.?\d*\%?)\s*,\s*(\d{1,3}\.?\d*\%?)\s*,\s*(\d{1,3}\.?\d*\%?)\s*,\s*(\d{1}\.?\d*\%?)\s*\)/', $str, $rgba_matches );
		if ( ! empty( $rgba_matches ) && count( $rgba_matches ) === 5 ) {
			for ( $i = 1; $i < 4; $i++ ) {
				if ( strpos( $rgba_matches[ $i ], '%' ) !== false ) {
					$rgba_matches[ $i ] = self::percent_0_100( $rgba_matches[ $i ] );
				} else {
					$rgba_matches[ $i ] = self::int_0_255( $rgba_matches[ $i ] );
				}
			}
			$rgba_matches[4] = self::opacity_0_1( $rgba_matches[ $i ] );
			return sprintf( 'rgba(%s, %s, %s, %s)', $rgba_matches[1], $rgba_matches[2], $rgba_matches[3], $rgba_matches[4] );
		}

		preg_match( '/rgb\(\s*(\d{1,3}\.?\d*\%?)\s*,\s*(\d{1,3}\.?\d*\%?)\s*,\s*(\d{1,3}\.?\d*\%?)\s*\)/', $str, $rgba_matches );
		if ( ! empty( $rgba_matches ) && count( $rgba_matches ) === 4 ) {
			for ( $i = 1; $i < 3; $i++ ) {
				if ( strpos( $rgba_matches[ $i ], '%' ) !== false ) {
					$rgba_matches[ $i ] = self::percent_0_100( $rgba_matches[ $i ] );
				} else {
					$rgba_matches[ $i ] = self::int_0_255( $rgba_matches[ $i ] );
				}
			}
			return sprintf( 'rgb(%s, %s, %s)', $rgba_matches[1], $rgba_matches[2], $rgba_matches[3] );
		}

		// Not a color function either. Let's see if it's a hex color.

		// Include the hash if not there.
		// The regex below depends on in.
		if ( substr( $str, 0, 1 ) !== '#' ) {
			$str = '#' . $str;
		}

		preg_match( '/(#)([0-9a-fA-F]{6}|[0-9a-fA-F]{3})/', $str, $matches );

		if ( count( $matches ) === 3 ) {
			if ( $return_hash ) {
				return $matches[1] . $matches[2];
			} else {
				return $matches[2];
			}
		}

		return $return_fail;
	}

	public static function percent_0_100( $val ) {
		$val = str_replace( '%', '', $val );
		if ( floatval( $val ) > 100 ) {
			$val = 100;
		} elseif ( floatval( $val ) < 0 ) {
			$val = 0;
		}

		return floatval( $val ) . '%';
	}

	public static function int_0_255( $val ) {
		if ( intval( $val ) > 255 ) {
			$val = 255;
		} elseif ( intval( $val ) < 0 ) {
			$val = 0;
		}

		return intval( $val );
	}

	public static function opacity_0_1( $val ) {
		if ( floatval( $val ) > 1 ) {
			$val = 1;
		} elseif ( floatval( $val ) < 0 ) {
			$val = 0;
		}

		return floatval( $val );
	}


	/**
	 * Removes elements whose keys are not valid data-attribute names.
	 *
	 * @since 1.0.0
	 *
	 * @param array $array Input array to sanitize.
	 *
	 * @return array
	 */
	public static function html_data_attributes_array( $array ) {
		$keys       = array_keys( $array );
		$key_prefix = 'data-';

		// Remove keys that are not data attributes.
		foreach ( $keys as $key ) {
			if ( substr( $key, 0, strlen( $key_prefix ) ) !== $key_prefix ) {
				unset( $array[ $key ] );
			}
		}

		return $array;
	}

	/**
	 * Sanitizes an image size name, according to the available image sizes during runtime.
	 *
	 * @since 1.0.0
	 *
	 * @param string $name Image size name to sanitize.
	 * @param string $default Optional. Default 'maxslider_slide'
	 *
	 * @return string
	 */
	public static function usable_image_size( $name, $default = 'maxslider_slide' ) {
		$sizes = MaxSlider()->usable_image_sizes();

		if ( array_key_exists( $name, $sizes ) ) {
			return $name;
		}

		return $default;
	}


}
