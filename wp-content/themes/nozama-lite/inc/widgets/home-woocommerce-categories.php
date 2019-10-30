<?php
if ( ! class_exists( 'CI_Widget_WooCommerce_Categories' ) ) :

	class CI_Widget_WooCommerce_Categories extends WP_Widget {

		protected $defaults = array(
			'title'    => '',
			'subtitle' => '',
			'layout'   => 1,
			'rows'     => array(),
		);

		public function __construct() {
			$widget_ops  = array( 'description' => esc_html__( 'Homepage widget. Displays a hand-picked selection of WooCommerce categories.', 'nozama-lite' ) );
			$control_ops = array();
			parent::__construct( 'ci-home-woocommerce-categories', esc_html__( 'Theme (home) - WooCommerce Categories', 'nozama-lite' ), $widget_ops, $control_ops );
		}

		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			$id            = isset( $args['id'] ) ? $args['id'] : '';
			$before_widget = $args['before_widget'];
			$after_widget  = $args['after_widget'];

			$title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );


			$subtitle = $instance['subtitle'];
			$rows     = $instance['rows'];
			$layout   = $instance['layout'];

			if ( empty( $rows ) ) {
				return;
			}

			$cat_ids = wp_list_pluck( $rows, 'cat_id' );

			echo wp_kses( $before_widget, nozama_lite_get_allowed_sidebar_wrappers() );

			if ( in_array( $id, nozama_lite_get_fullwidth_sidebars(), true ) ) {
				?>
				<div class="container">
					<div class="row">
						<div class="col-12">
				<?php
			}

			if ( $title || $subtitle ) {
				?>
				<div class="section-heading">
					<div class="section-heading-content">
				<?php

				if ( $title ) {
					echo wp_kses( $args['before_title'] . $title . $args['after_title'], nozama_lite_get_allowed_sidebar_wrappers() );
				}

				if ( $subtitle ) {
					?><p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p><?php
				}

				?>
					</div>
				</div>
				<?php
			}

			nozama_lite_get_template_part( 'template-parts/categories/layout', $layout, array(
				'term_ids' => $cat_ids,
			) );

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
			$instance['subtitle'] = sanitize_text_field( $new_instance['subtitle'] );
			$instance['layout']   = $this->sanitize_layout( $new_instance['layout'] );
			$instance['rows']     = $this->sanitize_instance_rows( $new_instance );

			return $instance;
		}

		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$title    = $instance['title'];
			$subtitle = $instance['subtitle'];
			$layout   = $instance['layout'];
			$rows     = $instance['rows'];

			$row_cat_id_name = $this->get_field_name( 'row_cat_id' ) . '[]';

			if ( ! class_exists( 'WooCommerce' ) ) {
				?><p><?php echo wp_kses( __( 'This widget requires that <strong>WooCommerce</strong> is installed and active.', 'nozama-lite' ), nozama_lite_get_allowed_tags( 'guide' ) ); ?></label></p><?php
				return;
			}

			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>"><?php esc_html_e( 'Subtitle:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>" class="widefat" /></p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>"><?php esc_html_e( 'Layout:', 'nozama-lite' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'layout' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'layout' ) ); ?>">
					<option value=""></option>
					<?php foreach ( $this->get_layout_choices() as $value => $description ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $layout ); ?>><?php echo esc_html( $description ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p><?php esc_html_e( 'Add as many items as you want by pressing the "Add Item" button. Remove any item by selecting "Remove me".', 'nozama-lite' ); ?></p>
			<fieldset class="ci-repeating-fields">
				<div class="inner">
					<?php
						if ( ! empty( $rows ) ) {
							$count = count( $rows );
							for ( $i = 0; $i < $count; $i ++ ) {
								?>
								<div class="post-field">
									<label class="post-field-item" data-value="<?php echo esc_attr( $rows[ $i ]['cat_id'] ); ?>"><?php esc_html_e( 'Category:', 'nozama-lite' ); ?>
										<?php
											wp_dropdown_categories( array(
												'show_option_none'  => ' ',
												'show_count'        => 1,
												'hide_empty'        => 0,
												'selected'          => $rows[ $i ]['cat_id'],
												'name'              => $row_cat_id_name,
												'class'             => 'widefat cats_dropdown',
												'taxonomy'          => 'product_cat',
												'hide_if_empty'     => false,
												'option_none_value' => '',
											) );
										?>
									</label>

									<p class="ci-repeating-remove-action"><a href="#" class="button ci-repeating-remove-field"><i class="dashicons dashicons-dismiss"></i><?php esc_html_e( 'Remove me', 'nozama-lite' ); ?></a></p>
								</div>
								<?php
							}
						}
					?>
					<?php
					//
					// Add an empty and hidden set for jQuery
					//
					?>
					<div class="post-field field-prototype" style="display: none;">
						<label class="post-field-item"><?php esc_html_e( 'Item:', 'nozama-lite' ); ?>
							<?php
								wp_dropdown_categories( array(
									'show_option_none'  => ' ',
									'show_count'        => 1,
									'hide_empty'        => 0,
									'name'              => $row_cat_id_name,
									'class'             => 'widefat cats_dropdown',
									'taxonomy'          => 'product_cat',
									'hide_if_empty'     => false,
									'option_none_value' => '',
								) );
							?>
						</label>

						<p class="ci-repeating-remove-action"><a href="#" class="button ci-repeating-remove-field"><i class="dashicons dashicons-dismiss"></i><?php esc_html_e( 'Remove me', 'nozama-lite' ); ?></a></p>
					</div>
				</div>
				<a href="#" class="ci-repeating-add-field button"><i class="dashicons dashicons-plus-alt"></i><?php esc_html_e( 'Add Item', 'nozama-lite' ); ?></a>
			</fieldset>

			<?php
		}

		protected function sanitize_instance_rows( $instance ) {
			if ( empty( $instance ) || ! is_array( $instance ) ) {
				return array();
			}

			$ids = $instance['row_cat_id'];

			$count = count( $ids );

			$new_fields = array();

			$records_count = 0;

			for ( $i = 0; $i < $count; $i++ ) {
				if ( empty( $ids[ $i ] ) ) {
					continue;
				}

				$new_fields[ $records_count ]['cat_id'] = ! empty( $ids[ $i ] ) ? intval( $ids[ $i ] ) : '';

				$records_count++;
			}
			return $new_fields;
		}

		protected function get_layout_choices() {
			/* translators: %1$d is a layout number. %2$d is the number of categories the layout requires. */
			$label = _n_noop( 'Layout #%1$d (%2$d category)', 'Layout #%1$d (%2$d categories)', 'nozama-lite' );

			return apply_filters( 'nozama_lite_wc_categories_widget_layout_choices', array(
				1 => esc_html( sprintf( translate_nooped_plural( $label, 2, 'nozama-lite' ), 1, 2 ) ),
				7 => esc_html( sprintf( translate_nooped_plural( $label, 1, 'nozama-lite' ), 7, 1 ) ),
			) );

		}

		protected function sanitize_layout( $value ) {
			$value   = intval( $value );
			$choices = $this->get_layout_choices();

			if ( array_key_exists( $value, $choices ) ) {
				return $value;
			}

			return 1;
		}

	}

endif;
