<?php
/*
 * This file contains parts of code taken from the "WP Instagram Widget" WordPress plugin, Copyright 2013 Scott Evans,
 * licensed under the GPLv2 or later. https://github.com/scottsweb/wp-instagram-widget
 */
if ( class_exists( 'null_instagram_widget' ) && ! class_exists( 'CI_Widget_Home_Instagram' ) ) :

	class CI_Widget_Home_Instagram extends null_instagram_widget {
		protected $defaults = array(
			'title'             => '',
			'username'          => '',
			'number'            => 9,
			'target'            => '_self',
			'link'              => 'Follow me!',
			'overlay_color'     => '',
			'background_color'  => '',
			'background_image'  => '',
			'background_repeat' => 'repeat',
			'background_size'   => 1,
			'parallax'          => '',
		);

		public function __construct() {
			$widget_ops  = array( 'description' => esc_html__( 'Homepage widget. Displays your latest Instagram photos. Requires the plugin "WP Instagram Widget" to be active.', 'nozama-lite' ) );
			$control_ops = array();
			WP_Widget::__construct( 'ci-home-instagram', esc_html__( 'Theme (home) - Instagram', 'nozama-lite' ), $widget_ops, $control_ops );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_css' ) );
		}

		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$id            = isset( $args['id'] ) ? $args['id'] : '';
			$before_widget = $args['before_widget'];
			$after_widget  = $args['after_widget'];

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );

			$username = $instance['username'];
			$number   = $instance['number'];
			$target   = $instance['target'];
			$link     = $instance['link'];

			$background_color = $instance['background_color'];
			$background_image = $instance['background_image'];
			$parallax         = $instance['parallax'];

			if ( ! empty( $background_color ) || ! empty( $background_image ) || ! empty( $overlay_color ) ) {
				preg_match( '/class=(["\']).*?widget-section.*?\1/', $before_widget, $match );
				if ( ! empty( $match ) ) {
					$classes = array( 'widget-section-padded' );
					if ( $parallax ) {
						$classes[] = 'widget-section-parallax';
					}

					$attr_class    = preg_replace( '/\bwidget-section\b/', 'widget-section ' . implode( ' ', $classes ), $match[0], 1 );
					$before_widget = str_replace( $match[0], $attr_class, $before_widget );
				}
			}

			echo wp_kses( $before_widget, nozama_lite_get_allowed_sidebar_wrappers() );

			if ( in_array( $id, nozama_lite_get_fullwidth_sidebars(), true ) ) {
				?>
				<div class="container">
					<div class="row">
						<div class="col-12">
				<?php
			}

			if ( $title || $link ) {
				?><div class="section-heading">
					<div class="section-heading-content">
				<?php
					if ( $title ) {
						echo wp_kses( $args['before_title'] . $title . $args['after_title'], nozama_lite_get_allowed_sidebar_wrappers() );
					}

					if ( $link ) {
						?><p class="section-subtitle"><?php

						echo sprintf( '<a href="%s" rel="me" target="%s">%s</a>',
							esc_url( sprintf( 'https://instagram.com/%s', $username ) ),
							esc_attr( $target ),
							esc_html( $link )
						);

						?></p>

						</div>
						<?php
					}
				?></div><?php
			}

			if ( ! empty( $username ) ) {
				$media_array = $this->scrape_instagram( $username );

				if ( is_wp_error( $media_array ) ) {
					echo wp_kses_post( $media_array->get_error_message() );
				} else {

					// filter for images only?
					if ( $images_only = apply_filters( 'wpiw_images_only', false ) ) {
						$media_array = array_filter( $media_array, array( $this, 'images_only' ) );
					}

					// slice list down to required limit
					$media_array = array_slice( $media_array, 0, $number );

					?>
					<ul class="instagram-pics instagram-size-large"><?php
					foreach ( $media_array as $item ) {
						echo sprintf( '<li><a href="%s" target="%s"><img src="%s" alt="%s"></a></li>',
							esc_url( $item['link'] ),
							esc_attr( $target ),
							esc_url( $item['large'] ),
							esc_attr( $item['description'] )
						);
					}
					?></ul><?php
				}
			}

			if ( in_array( $id, nozama_lite_get_fullwidth_sidebars(), true ) ) {
				?>
						</div>
					</div>
				</div>
				<?php
			}

			echo wp_kses( $after_widget, nozama_lite_get_allowed_sidebar_wrappers() );
		}

		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title']    = sanitize_text_field( $new_instance['title'] );
			$instance['username'] = trim( sanitize_text_field( $new_instance['username'] ) );
			$instance['number']   = absint( $new_instance['number'] );
			$instance['target']   = in_array( $new_instance['target'], array( '_self', '_blank' ), true ) ? $new_instance['target'] : $this->defaults['target'];
			$instance['link']     = sanitize_text_field( $new_instance['link'] );

			$instance['overlay_color']     = nozama_lite_sanitize_rgba_color( $new_instance['overlay_color'] );
			$instance['background_color']  = sanitize_hex_color( $new_instance['background_color'] );
			$instance['background_image']  = esc_url_raw( $new_instance['background_image'] );
			$instance['background_repeat'] = nozama_lite_sanitize_image_repeat( $new_instance['background_repeat'] );
			$instance['background_size']   = isset( $new_instance['background_size'] );
			$instance['parallax']          = isset( $new_instance['parallax'] );

			return $instance;
		}

		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$title    = $instance['title'];
			$username = $instance['username'];
			$number   = $instance['number'];
			$target   = $instance['target'];
			$link     = $instance['link'];

			$overlay_color     = $instance['overlay_color'];
			$background_color  = $instance['background_color'];
			$background_image  = $instance['background_image'];
			$background_repeat = $instance['background_repeat'];
			$background_size   = $instance['background_size'];
			$parallax          = $instance['parallax'];

			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php esc_html_e( 'Username:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $username ); ?>" class="widefat" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of photos:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $number ); ?>" class="widefat" /></p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"><?php esc_html_e( 'Open links in:', 'nozama-lite' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>">
					<?php $choices = array(
						'_self'  => esc_html__( 'Current window', 'nozama-lite' ),
						'_blank' => esc_html__( 'New window', 'nozama-lite' ),
					); ?>
					<?php foreach ( $choices as $value => $description ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $target ); ?>><?php echo wp_kses( $description, array() ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>"><?php esc_html_e( 'Link text:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'link' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'link' ) ); ?>" type="text" value="<?php echo esc_attr( $link ); ?>" class="widefat" /></p>

			<fieldset class="ci-collapsible">
				<legend><?php esc_html_e( 'Customize', 'nozama-lite' ); ?> <i class="dashicons dashicons-arrow-down"></i></legend>
				<div class="elements">
					<p><label for="<?php echo esc_attr( $this->get_field_id( 'overlay_color' ) ); ?>"><?php esc_html_e( 'Overlay Color:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'overlay_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'overlay_color' ) ); ?>" type="text" value="<?php echo esc_attr( $overlay_color ); ?>" class="widefat nozama-lite-alpha-color-picker" /></p>
					<p><label for="<?php echo esc_attr( $this->get_field_id( 'background_color' ) ); ?>"><?php esc_html_e( 'Background Color:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'background_color' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'background_color' ) ); ?>" type="text" value="<?php echo esc_attr( $background_color ); ?>" class="nozama-lite-color-picker widefat"/></p>

					<p class="ci-collapsible-media"><label for="<?php echo esc_attr( $this->get_field_id( 'background_image' ) ); ?>"><?php esc_html_e( 'Background Image:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'background_image' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'background_image' ) ); ?>" type="text" value="<?php echo esc_attr( $background_image ); ?>" class="ci-uploaded-url widefat"/><a href="#" class="button ci-media-button"><?php esc_html_e( 'Select', 'nozama-lite' ); ?></a></p>
					<p>
						<label for="<?php echo esc_attr( $this->get_field_id( 'background_repeat' ) ); ?>"><?php esc_html_e( 'Background Repeat:', 'nozama-lite' ); ?></label>
						<select id="<?php echo esc_attr( $this->get_field_id( 'background_repeat' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'background_repeat' ) ); ?>">
							<option value="repeat" <?php selected( 'repeat', $background_repeat ); ?>><?php esc_html_e( 'Repeat', 'nozama-lite' ); ?></option>
							<option value="repeat-x" <?php selected( 'repeat-x', $background_repeat ); ?>><?php esc_html_e( 'Repeat Horizontally', 'nozama-lite' ); ?></option>
							<option value="repeat-y" <?php selected( 'repeat-y', $background_repeat ); ?>><?php esc_html_e( 'Repeat Vertically', 'nozama-lite' ); ?></option>
							<option value="no-repeat" <?php selected( 'no-repeat', $background_repeat ); ?>><?php esc_html_e( 'No Repeat', 'nozama-lite' ); ?></option>
						</select>
					</p>
					<p><label for="<?php echo esc_attr( $this->get_field_id( 'background_size' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'background_size' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'background_size' ) ); ?>" value="1" <?php checked( $background_size, 1 ); ?> /><?php esc_html_e( 'Stretch background image to cover the entire width (requires a background image).', 'nozama-lite' ); ?></label></p>

					<p><label for="<?php echo esc_attr( $this->get_field_id( 'parallax' ) ); ?>"><input type="checkbox" name="<?php echo esc_attr( $this->get_field_name( 'parallax' ) ); ?>" id="<?php echo esc_attr( $this->get_field_id( 'parallax' ) ); ?>" value="1" <?php checked( $parallax, 1 ); ?> /><?php esc_html_e( 'Parallax effect (requires a background image).', 'nozama-lite' ); ?></label></p>
				</div>
			</fieldset>
			<?php
		}

		public function enqueue_custom_css() {
			$settings = $this->get_settings();

			if ( empty( $settings ) ) {
				return;
			}

			foreach ( $settings as $instance_id => $instance ) {
				$id = $this->id_base . '-' . $instance_id;

				if ( ! is_active_widget( false, $id, $this->id_base ) ) {
					continue;
				}

				$instance = wp_parse_args( (array) $instance, $this->defaults );

				$sidebar_id      = false; // Holds the sidebar id that the widget is assigned to.
				$sidebar_widgets = wp_get_sidebars_widgets();
				if ( ! empty( $sidebar_widgets ) ) {
					foreach ( $sidebar_widgets as $sidebar => $widgets ) {
						// We need to check $widgets for emptiness due to https://core.trac.wordpress.org/ticket/14876
						if ( ! empty( $widgets ) && array_search( $id, $widgets, true ) !== false ) {
							$sidebar_id = $sidebar;
						}
					}
				}

				$background_color  = $instance['background_color'];
				$background_image  = $instance['background_image'];
				$background_repeat = $instance['background_repeat'];
				$background_size   = $instance['background_size'] ? '' : 'auto'; // Assumes that background-size: cover; is applied by default.

				$css = '';

				if ( ! empty( $background_color ) ) {
					$css .= 'background-color: ' . $background_color . '; ';
				}
				if ( ! empty( $background_image ) ) {
					$css .= 'background-image: url(' . esc_url( $background_image ) . '); ';
					$css .= 'background-repeat: ' . $background_repeat . '; ';
				}

				if ( ! empty( $background_size ) ) {
					$css .= 'background-size: ' . $background_size . '; ';
				}

				if ( ! empty( $css ) ) {
					$css = '#' . $id . ' { ' . $css . ' } ' . PHP_EOL;
					wp_add_inline_style( 'nozama-lite-style', $css );
				}

				$overlay_color = $instance['overlay_color'];

				$css = '';

				if ( ! empty( $overlay_color ) ) {
					$css .= 'background-color: ' . $overlay_color . '; ';
				}

				if ( ! empty( $css ) ) {
					$css = '#' . $id . '::before { ' . $css . ' } ' . PHP_EOL;
					wp_add_inline_style( 'nozama-lite-style', $css );
				}

			}

		}

	}

endif;
