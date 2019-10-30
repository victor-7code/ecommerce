<?php
if ( ! class_exists( 'CI_Widget_Home_Latest_Products' ) ) :

	class CI_Widget_Home_Latest_Products extends WP_Widget {

		protected $defaults = array(
			'title'    => '',
			'subtitle' => '',
			'term_id'  => '',
			'orderby'  => 'date',
			'count'    => 3,
			'columns'  => 3,
		);

		public function __construct() {
			$widget_ops  = array( 'description' => __( 'Homepage widget. Displays a number of the latest (or random) products, optionally from a specific category.', 'nozama-lite' ) );
			$control_ops = array();
			parent::__construct( 'ci-home-latest-products', esc_html__( 'Theme (home) - Latest Products', 'nozama-lite' ), $widget_ops, $control_ops );
		}


		public function widget( $args, $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			if ( ! class_exists( 'WooCommerce' ) ) {
				return;
			}

			$id            = isset( $args['id'] ) ? $args['id'] : '';
			$before_widget = $args['before_widget'];
			$after_widget  = $args['after_widget'];

			$title    = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base );
			$subtitle = $instance['subtitle'];
			$term_id  = $instance['term_id'];
			$orderby  = $instance['orderby'];
			$count    = $instance['count'];
			$columns  = $instance['columns'];

			if ( 0 === $count ) {
				return;
			}

			// 'rand' and 'rating' ignore the order. 'title' should be ASC, and the rest DESC.
			$order = 'DESC';
			if ( 'title' === $orderby ) {
				$order = 'ASC';
			}

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

				$more_url = get_permalink( wc_get_page_id( 'shop' ) );

				if ( $term_id ) {
					$more_url = get_term_link( $term_id );
				}

				if ( $title ) {
					echo wp_kses(
						$args['before_title'] . $title . ' <a href="' . esc_url( $more_url ) . '">' . esc_html__( 'See More', 'nozama-lite' ) . '</a>' . $args['after_title'],
						nozama_lite_get_allowed_sidebar_wrappers()
					);
				}

				if ( $subtitle ) {
					?><p class="section-subtitle"><?php echo esc_html( $subtitle ); ?></p><?php
				}

				?>
					</div>

				</div>
				<?php
			}

			echo do_shortcode( sprintf( '[products limit="%1$s" columns="%2$s" orderby="%3$s" order="%4$s" category="%5$s"]',
				$count,
				$columns,
				$orderby,
				$order,
				$term_id
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
			$instance['term_id']  = nozama_lite_sanitize_intval_or_empty( $new_instance['term_id'] );
			$instance['orderby']  = $this->sanitize_product_orderby( $new_instance['orderby'] );
			$instance['count']    = absint( $new_instance['count'] );
			$instance['columns']  = absint( $new_instance['columns'] );

			return $instance;
		} // save

		public function form( $instance ) {
			$instance = wp_parse_args( (array) $instance, $this->defaults );

			$title    = $instance['title'];
			$subtitle = $instance['subtitle'];
			$term_id  = $instance['term_id'];
			$orderby  = $instance['orderby'];
			$count    = $instance['count'];
			$columns  = $instance['columns'];

			if ( ! class_exists( 'WooCommerce' ) ) {
				?><p><?php echo wp_kses( __( 'This widget requires that <strong>WooCommerce</strong> is installed and active.', 'nozama-lite' ), nozama_lite_get_allowed_tags( 'guide' ) ); ?></label></p><?php
				return;
			}

			?>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" class="widefat" /></p>
			<p><label for="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>"><?php esc_html_e( 'Subtitle:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'subtitle' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'subtitle' ) ); ?>" type="text" value="<?php echo esc_attr( $subtitle ); ?>" class="widefat" /></p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'term_id' ) ); ?>"><?php esc_html_e( 'Category to display the latest products from (optional):', 'nozama-lite' ); ?></label>
			<?php wp_dropdown_categories( array(
				'taxonomy'          => 'product_cat',
				'show_option_all'   => '',
				'show_option_none'  => ' ',
				'option_none_value' => '',
				'show_count'        => 1,
				'echo'              => 1,
				'selected'          => $term_id,
				'hierarchical'      => 1,
				'name'              => $this->get_field_name( 'term_id' ),
				'id'                => $this->get_field_id( 'term_id' ),
				'class'             => 'postform widefat',
			) ); ?>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>"><?php esc_html_e( 'Order by:', 'nozama-lite' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'orderby' ) ); ?>" class="widefat" name="<?php echo esc_attr( $this->get_field_name( 'orderby' ) ); ?>">
					<?php foreach ( $this->product_orderby() as $value => $description ) : ?>
						<option value="<?php echo esc_attr( $value ); ?>" <?php selected( $value, $orderby ); ?>><?php echo esc_html( $description ); ?></option>
					<?php endforeach; ?>
				</select>
			</p>

			<p><label for="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>"><?php esc_html_e( 'Number of posts to show:', 'nozama-lite' ); ?></label><input id="<?php echo esc_attr( $this->get_field_id( 'count' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'count' ) ); ?>" type="number" min="1" step="1" value="<?php echo esc_attr( $count ); ?>" class="widefat"/></p>

			<p>
				<label for="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>"><?php esc_html_e( 'Output Columns:', 'nozama-lite' ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'columns' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'columns' ) ); ?>" class="widefat">
					<?php
						$col_options = nozama_lite_post_type_listing_get_valid_columns_options();
						foreach ( $col_options['range'] as $col ) {
							echo sprintf( '<option value="%s" %s>%s</option>',
								esc_attr( $col ),
								selected( $columns, $col, false ),
								/* translators: %d is a number of columns. */
								esc_html( sprintf( _n( '%d Column', '%d Columns', $col, 'nozama-lite' ), $col ) )
							);
						}
					?>
				</select>
			</p>

			<?php

		} // form

		protected function product_orderby() {
			return apply_filters( 'nozama_lite_wc_latest_products_orderby', array(
				'date'       => esc_html__( 'Latest', 'nozama-lite' ),
				'rating'     => esc_html__( 'Rating', 'nozama-lite' ),
				'popularity' => esc_html__( 'Popularity', 'nozama-lite' ),
				'rand'       => esc_html__( 'Random', 'nozama-lite' ),
			) );
		}

		protected function sanitize_product_orderby( $value ) {
			$choices = $this->product_orderby();
			if ( array_key_exists( $value, $choices ) ) {
				return $value;
			}

			return $this->defaults['orderby'];
		}

	}

endif;
