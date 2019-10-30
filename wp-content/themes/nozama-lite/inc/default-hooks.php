<?php
/**
 * Add a pingback url auto-discovery header for singularly identifiable articles.
 */
function nozama_lite_pingback_header() {
	if ( is_singular() && pings_open() ) {
		echo '<link rel="pingback" href="', esc_url( get_bloginfo( 'pingback_url' ) ), '">';
	}
}
add_action( 'wp_head', 'nozama_lite_pingback_header' );


add_filter( 'excerpt_length', 'nozama_lite_excerpt_length' );
function nozama_lite_excerpt_length( $length ) {
	return get_theme_mod( 'excerpt_length', 55 );
}

add_filter( 'the_content', 'nozama_lite_lightbox_rel', 12 );
add_filter( 'get_comment_text', 'nozama_lite_lightbox_rel' );
if ( ! function_exists( 'nozama_lite_lightbox_rel' ) ) :
	function nozama_lite_lightbox_rel( $content ) {
		if ( get_theme_mod( 'theme_lightbox', 1 ) ) {
			global $post;
			$pattern     = "/<a(.*?)href=('|\")([^>]*).(bmp|gif|jpeg|jpg|png)('|\")(.*?)>(.*?)<\/a>/i";
			$replacement = '<a$1href=$2$3.$4$5 data-lightbox="gal[' . $post->ID . ']"$6>$7</a>';
			$content     = preg_replace( $pattern, $replacement, $content );
		}

		return $content;
	}
endif;

add_filter( 'wp_get_attachment_link', 'nozama_lite_wp_get_attachment_link_lightbox_caption', 10, 6 );
function nozama_lite_wp_get_attachment_link_lightbox_caption( $html, $id, $size, $permalink, $icon, $text ) {
	if ( get_theme_mod( 'theme_lightbox', 1 ) && false === $permalink ) {
		$found = preg_match( '#(<a.*?>)<img.*?></a>#', $html, $matches );
		if ( $found ) {
			$found_title = preg_match( '#title=([\'"])(.*?)\1#', $matches[1], $title_matches );

			// Only continue if title attribute doesn't exist.
			if ( 0 === $found_title ) {
				$caption = nozama_lite_get_image_lightbox_caption( $id );

				if ( $caption ) {
					$new_a = $matches[1];
					$new_a = rtrim( $new_a, '>' );
					$new_a = $new_a . ' title="' . $caption . '">';

					$html = str_replace( $matches[1], $new_a, $html );
				}
			}
		}
	}

	return $html;
}

add_filter( 'the_title', 'nozama_lite_replace_the_title', 10, 2 );
if ( ! function_exists( 'nozama_lite_replace_the_title' ) ) :
	function nozama_lite_replace_the_title( $title, $id ) {
		if ( is_admin() ) {
			return $title;
		}

		$alt_title = get_post_meta( $id, 'title', true );

		if ( $alt_title ) {
			$title = $alt_title;
		}

		return $title;
	}
endif;

// Wraps the core's archive widget's post counts in span.ci-count
add_filter( 'get_archives_link', 'nozama_lite_wrap_archive_widget_post_counts_in_span', 10, 2 );
if ( ! function_exists( 'nozama_lite_wrap_archive_widget_post_counts_in_span' ) ) :
	function nozama_lite_wrap_archive_widget_post_counts_in_span( $output ) {
		$output = preg_replace_callback( '#(<li>.*?<a.*?>.*?</a>.*?)&nbsp;(\(.*?\))(.*?</li>)#', 'nozama_lite_replace_archive_widget_post_counts_in_span', $output );

		return $output;
	}
endif;

if ( ! function_exists( 'nozama_lite_replace_archive_widget_post_counts_in_span' ) ) :
	function nozama_lite_replace_archive_widget_post_counts_in_span( $matches ) {
		return sprintf( '%s <span class="ci-count">%s</span>%s',
			$matches[1],
			$matches[2],
			$matches[3]
		);
	}
endif;

// Wraps the core's category widget's post counts in span.ci-count
add_filter( 'wp_list_categories', 'nozama_lite_wrap_category_widget_post_counts_in_span', 10, 2 );
if ( ! function_exists( 'nozama_lite_wrap_category_widget_post_counts_in_span' ) ) :
	function nozama_lite_wrap_category_widget_post_counts_in_span( $output, $args ) {
		if ( ! isset( $args['show_count'] ) || $args['show_count'] == 0 ) {
			return $output;
		}
		$output = preg_replace_callback( '#(<a.*?>)\s*?(\(.*?\))#', 'nozama_lite_replace_category_widget_post_counts_in_span', $output );

		return $output;
	}
endif;

if ( ! function_exists( 'nozama_lite_replace_category_widget_post_counts_in_span' ) ) :
	function nozama_lite_replace_category_widget_post_counts_in_span( $matches ) {
		return sprintf( '%s <span class="ci-count">%s</span>',
			$matches[1],
			$matches[2]
		);
	}
endif;
