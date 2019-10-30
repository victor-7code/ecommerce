<?php
if ( ! class_exists( 'CI_Widget_Newsletter' ) ) :

	class CI_Widget_Newsletter extends WP_Widget {
		protected $defaults = array(
			'title'             => '',
			'subtitle'          => '',
			'form_email'        => '',
			'form_action'       => '',
			'background_color'  => '',
			'background_image'  => '',
			'background_repeat' => 'repeat',
			'background_size'   => 1,
		);

		public function __construct() {
			$widget_ops  = array( 'description' => esc_html__( 'Displays a newsletter subscription form.', 'nozama-lite' ) );
			$control_ops = array();
			WP_Widget::__construct( 'ci-home-newsletter', esc_html__( 'Theme - Newsletter', 'nozama-lite' ), $widget_ops, $control_ops );

			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_custom_css' ) );
		}

		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$id            = isset( $args['id'] ) ? $args['id'] : '';
			$before_widget = $args['before_widget'];
			$after_widget  = $args['after_widget'];

			$title    = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$subtitle = $instance['subtitle'];

			$form_email  = $instance['form_email'];
			$form_action = $instance['form_action'];


			if ( ! $form_action ) {
				return;
			}

			$background_color = $instance['background_color'];
			$background_image = $instance['background_image'];

			if ( ! empty( $background_color ) || ! empty( $background_image ) ) {
				preg_match( '/class=(["\']).*?widget-section.*?\1/', $before_widget, $match );
				if ( ! empty( $match ) ) {
					$classes = array( 'widget-section-padded' );

					$attr_class    = preg_replace( '/\bwidget-section\b/', 'widget-section ' . implode( ' ', $classes ), $match[0], 1 );
					$before_widget = str_replace( $match[0], $attr_class, $before_widget );
				}
			}

			echo wp_kses( $before_widget, nozama_lite_get_allowed_sidebar_wrappers() );
			?>
			<div class="widget-newsletter-wrap">
				<div class="container">
					<div class="row align-items-lg-center">
						<div class="col-lg-6 col-12">
							<div class="widget-newsletter-content-wrap">
								<i class="far fa-envelope-open"></i>
								<?php	if ( $title || $subtitle ) { ?>
									<div class="widget-newsletter-content">
										<?php
											if ( $title ) {
												echo wp_kses( $args['before_title'] . esc_html( $title ) . $args['after_title'], nozama_lite_get_allowed_sidebar_wrappers() );
											}

											if ( $subtitle ) {
												?><p><?php echo esc_html( $subtitle ); ?></p><?php
											}
										?>
									</div>
								<?php } ?>
							</div>
						</div>

						<div class="col-lg-6 col-12">
							<form method="post" action="<?php echo esc_url( $form_action ); ?>" class="widget-newsletter-form">
								<label for="widget-newsletter-email" class="sr-only"><?php esc_html_e( 'Your email', 'nozama-lite' ); ?></label>
								<input name="<?php echo esc_attr( $form_email ); ?>" id="widget-newsletter-email" type="email" required placeholder="<?php esc_attr_e( 'Your email address', 'nozama-lite' ); ?>">
								<button type="submit"><?php esc_html_e( 'Sign Up Today!', 'nozama-lite' ); ?></button>
							</form>
						</div>
					</div>
				</div>
			</div>
			<?php

			echo wp_kses( $after_widget, nozama_lite_get_allowed_sidebar_wrappers() );
		}

		public function update( $new_instance, $old_instance ) {
			$instance = $old_instance;

			$instance['title']       = sanitize_text_field( $new_instance['title'] );
			$instance['subtitle']    = sanitize_text_field( $new_instance['subtitle'] );
			$instance['form_email']  = sanitize_text_field( $new_instance['form_email'] );
			$instance['form_action'] = esc_url_raw( $new_instance['form_action'] );

			$instance['background_color']  = sanitize_hex_color( $new_instance['background_color'] );
			$instance['background_image']  = esc_url_raw( $new_instance['background_image'] );
			$instance['background_repeat'] = nozama_lite_sanitize_image_repeat( $new_instance['background_repeat'] );
			$instance['background_size']   = isset( $new_instance['background_size'] );
			return $instance;
		}

		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$title       = $instance['title'];
			$subtitle    = $instance['subtitle'];
			$form_email  = $instance['form_email'];
			$form_action = $instance['form_action'];

			$background_color  = $instance['background_color'];
			$background_image  = $instance['background_image'];
			$background_repeat = $instance['background_repeat'];
			$background_size   = $instance['background_size'];

			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>"><?php esc_html_e( 'Subtitle:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>" class="widefat" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'form_email' ) ); ?>"><?php esc_html_e( 'Form email field name attribute:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'form_email' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'form_email' ) ); ?>" type="text" value="<?php echo esc_attr( $form_email ); ?>" class="widefat" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'form_action' ) ); ?>"><?php esc_html_e( 'Form Action URL:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'form_action' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'form_action' ) ); ?>" type="text" value="<?php echo esc_url( $form_action ); ?>" class="widefat" /></p>

			<fieldset class="ci-collapsible">
				<legend><?php esc_html_e( 'Customize', 'nozama-lite' ); ?> <i class="dashicons dashicons-arrow-down"></i></legend>
				<div class="elements">
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

				$css = '';

				if ( ! empty( $css ) ) {
					$css = '#' . $id . '::before { ' . $css . ' } ' . PHP_EOL;
					wp_add_inline_style( 'nozama-lite-style', $css );
				}

			}

		}

	}

endif;
