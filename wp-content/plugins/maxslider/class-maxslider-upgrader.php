<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Class MaxSlider_Upgrader
 *
 * @version 1.1.0
 * @since 1.1.0
 */
class MaxSlider_Upgrader {
	public static $version = '1.1.0'; // Current file version. Only updated when something in db changes.
	public $db_version; // Version of options in db.

	public function __construct() {
		$this->db_version = get_option( 'maxslider_db_version' );
	}

	public function do_upgrade() {
		if ( ! empty( $this->db_version ) && version_compare( $this->db_version, self::$version, '>=' ) ) {
			return;
		}

		if ( empty( $this->db_version ) ) {
			$this->do_1_1_0();
		}

		update_option( 'maxslider_db_version', $this->db_version );
	}

	public function do_1_1_0() {
		$q = new WP_Query( array(
			'post_type'      => MaxSlider()->post_type,
			'posts_per_page' => -1,
		) );

		if ( $q->have_posts() ) {
			while ( $q->have_posts() ) {
				$q->the_post();

				// Handle show/update of old 'show_arrows' option to new 'navigation' option.
				$arrows = MaxSlider()->get_post_meta( get_the_ID(), '_maxslider_show_arrows', false );
				if ( empty( $arrows ) ) {
					update_post_meta( get_the_ID(), '_maxslider_navigation', '' );
				} else {
					update_post_meta( get_the_ID(), '_maxslider_navigation', 'arrows' );
				}
				delete_post_meta( get_the_ID(), '_maxslider_show_arrows' );

				// Rename the old arrow colors
				$arrows_fg = MaxSlider()->get_post_meta( get_the_ID(), '_maxslider_arrows_fg_color' );
				$arrows_bg = MaxSlider()->get_post_meta( get_the_ID(), '_maxslider_arrows_bg_color' );

				update_post_meta( get_the_ID(), '_maxslider_navigation_fg_color', $arrows_fg );
				update_post_meta( get_the_ID(), '_maxslider_navigation_bg_color', $arrows_bg );

				delete_post_meta( get_the_ID(), '_maxslider_arrows_fg_color' );
				delete_post_meta( get_the_ID(), '_maxslider_arrows_bg_color' );
			}
			wp_reset_postdata();
		}

		$this->db_version = '1.1.0';
	}

}
