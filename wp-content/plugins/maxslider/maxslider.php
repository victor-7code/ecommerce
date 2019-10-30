<?php
/*
 * Plugin Name: MaxSlider
 * Plugin URI: https://www.cssigniter.com/ignite/plugins/maxslider/
 * Description: MaxSlider is a free WordPress slider plugin that lets you create responsive sliders for your website. Shortcode and Visual Composer support included.
 * Author: The CSSIgniter Team
 * Author URI: https://www.cssigniter.com
 * Version: 1.1.7
 * Text Domain: maxslider
 * Domain Path: languages
 *
 * MaxSlider is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * MaxSlider is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with MaxSlider. If not, see <http://www.gnu.org/licenses/>.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}


class MaxSlider {

	/**
	 * MaxSlider version.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public static $version = '1.1.7';

	/**
	 * Instance of this class.
	 *
	 * @var MaxSlider
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * Sanitizer instance.
	 *
	 * @var MaxSlider_Sanitizer
	 * @since 1.0.0
	 */
	public $sanitizer = null;

	/**
	 * The URL directory path (with trailing slash) of the main plugin file.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected static $plugin_url = '';

	/**
	 * The filesystem directory path (with trailing slash) of the main plugin file.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	protected static $plugin_path = '';


	/**
	 * Slider post type name.
	 *
	 * @var string
	 * @since 1.0.0
	 */
	public $post_type = 'maxslider_slider';


	/**
	 * User-selectable image sizes.
	 *
	 * @var array
	 * @since 1.0.0
	 */
	protected $usable_image_sizes;


	/**
	 * User-selectable image sizes.
	 *
	 * @var bool
	 * @since 1.1.0
	 */
	protected $whitelabel = null;


	/**
	 * MaxSlider Instance.
	 *
	 * Instantiates or reuses an instance of MaxSlider.
	 *
	 * @since 1.0.0
	 * @static
	 * @see MaxSlider()
	 * @return MaxSlider - Single instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}


	/**
	 * MaxSlider constructor. Intentionally left empty so that instances can be created without
	 * re-loading of resources (e.g. scripts/styles), or re-registering hooks.
	 * http://wordpress.stackexchange.com/questions/70055/best-way-to-initiate-a-class-in-a-wp-plugin
	 * https://gist.github.com/toscho/3804204
	 *
	 * @since 1.0.0
	 */
	public function __construct() {}

	/**
	 * Kickstarts plugin loading.
	 *
	 * @since 1.0.0
	 */
	public function plugin_setup() {
		self::$plugin_url  = plugin_dir_url( __FILE__ );
		self::$plugin_path = plugin_dir_path( __FILE__ );

		load_plugin_textdomain( 'maxslider', false, dirname( self::plugin_basename() ) . '/languages' );

		include_once( dirname( __FILE__ ) . '/class-maxslider-sanitizer.php' );
		$this->sanitizer = new MaxSlider_Sanitizer();

		include_once( dirname( __FILE__ ) . '/class-maxslider-template-hooks.php' );
		new MaxSlider_Template_Hooks();

		include_once( dirname( __FILE__ ) . '/class-maxslider-back-compat.php' );
		new MaxSlider_Back_Compat();

		// Initialization needed in every request.
		$this->init();

		// Initialization needed in admin requests.
		if ( is_admin() ) {
			$this->admin_init();
		}

		// Initialization needed in frontend requests.
		if ( ! is_admin() ) {
			$this->frontend_init();
		}

		do_action( 'maxslider_loaded' );
	}

	/**
	 * Registers actions that need to be run on both admin and frontend
	 *
	 * @version 1.1.0
	 * @since 1.0.0
	 */
	protected function init() {
		add_action( 'init', array( $this, 'register_post_types' ) );
		add_action( 'init', array( $this, 'register_scripts' ) );
		add_action( 'init', array( $this, 'register_image_sizes' ) );
		add_action( 'init', array( $this, 'register_shortcodes' ) );
		add_action( 'vc_before_init', array( $this, 'register_vc_shortcodes' ) );

		add_action( 'init', array( $this, 'maybe_upgrade' ) );

		do_action( 'maxslider_init' );
	}


	/**
	 * Registers actions that need to be run on admin only.
	 *
	 * @since 1.0.0
	 */
	protected function admin_init() {
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_scripts' ) );
		add_action( 'add_meta_boxes', array( $this, 'add_meta_boxes' ) );
		add_action( 'save_post', array( $this, 'save_post' ) );

		// Make slides sortable.
		add_filter( 'maxslider_metabox_slides_container_classes', array( $this, 'filter_metabox_slides_container_classes_sortable' ) );
		add_action( 'maxslider_metabox_slides_repeatable_slide_field_before_title', array( $this, 'action_metabox_slides_move_handle' ) );

		// Add batch upload button.
		add_action( 'maxslider_metabox_slides_field_controls', array( $this, 'action_metabox_slides_field_controls_batch_upload' ) );

		do_action( 'maxslider_admin_init' );
	}

	/**
	 * Registers actions that need to be run on frontend only.
	 *
	 * @since 1.0.0
	 */
	protected function frontend_init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		do_action( 'maxslider_frontend_init' );
	}

	/**
	 * Register (but not enqueue) all scripts and styles to be used throughout the plugin.
	 *
	 * @since 1.0.0
	 */
	public function register_scripts() {
		wp_register_style( 'alpha-color-picker', $this->plugin_url() . 'assets/vendor/alpha-color-picker/alpha-color-picker.css', array( 'wp-color-picker' ), self::$version );
		wp_register_style( 'slick', $this->plugin_url() . 'assets/vendor/slick/slick.css', array(), '1.6.0' );
		wp_register_style( 'maxslider', $this->plugin_url() . 'assets/css/maxslider.css', array( 'slick', 'dashicons' ), self::$version );
		wp_register_style( 'maxslider-admin', $this->plugin_url() . 'assets/css/admin-styles.css', array(
			'wp-color-picker',
			'alpha-color-picker',
		), self::$version );

		wp_register_style( 'maxslider-footer', false, array(), self::$version );

		wp_register_script( 'alpha-color-picker', $this->plugin_url() . 'assets/vendor/alpha-color-picker/alpha-color-picker.js', array( 'jquery', 'wp-color-picker' ), self::$version, true );
		wp_register_script( 'slick', $this->plugin_url() . 'assets/vendor/slick/slick.js', array( 'jquery' ), '1.6.0', true );
		wp_register_script( 'maxslider', $this->plugin_url() . 'assets/js/maxslider.js', array( 'slick' ), self::$version, true );
		wp_register_script( 'maxslider-admin', $this->plugin_url() . 'assets/js/maxslider-admin.js', array(
			'wp-color-picker',
			'alpha-color-picker',
		), self::$version, true );

		wp_localize_script( 'maxslider-admin', 'maxslider_scripts', array(
			'messages' => array(
				'confirm_clear_slides'     => esc_html__( 'Do you really want to remove all slides? (This will not delete your image files).', 'maxslider' ),
				'media_title_upload_cover' => esc_html__( 'Select a slide image', 'maxslider' ),
			),
		) );
	}

	/**
	 * Enqueues frontend scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'maxslider' );
		wp_enqueue_script( 'maxslider' );
	}

	/**
	 * Enqueues admin scripts and styles.
	 *
	 * @since 1.0.0
	 */
	public function enqueue_admin_scripts( $hook ) {
		$screen = get_current_screen();

		if ( 'post' === $screen->base && $screen->post_type === $this->post_type ) {
			wp_enqueue_media();
			wp_enqueue_style( 'maxslider-admin' );
			wp_enqueue_script( 'maxslider-admin' );
		}
	}

	/**
	 * Post types registration.
	 *
	 * @since 1.0.0
	 */
	public function register_post_types() {
		$labels = array(
			'name'               => esc_html_x( 'Sliders', 'post type general name', 'maxslider' ),
			'singular_name'      => esc_html_x( 'Slider', 'post type singular name', 'maxslider' ),
			'menu_name'          => esc_html_x( 'MaxSlider', 'admin menu', 'maxslider' ),
			'name_admin_bar'     => esc_html_x( 'Slider', 'add new on admin bar', 'maxslider' ),
			'add_new'            => esc_html__( 'Add New', 'maxslider' ),
			'add_new_item'       => esc_html__( 'Add New Slider', 'maxslider' ),
			'edit_item'          => esc_html__( 'Edit Slider', 'maxslider' ),
			'new_item'           => esc_html__( 'New Slider', 'maxslider' ),
			'view_item'          => esc_html__( 'View Sliders', 'maxslider' ),
			'search_items'       => esc_html__( 'Search Sliders', 'maxslider' ),
			'not_found'          => esc_html__( 'No sliders found', 'maxslider' ),
			'not_found_in_trash' => esc_html__( 'No sliders found in the trash', 'maxslider' ),
		);

		$args = array(
			'labels'          => $labels,
			'singular_label'  => esc_html_x( 'Slider', 'post type singular name', 'maxslider' ),
			'public'          => false,
			'show_ui'         => true,
			'capability_type' => 'post',
			'hierarchical'    => false,
			'has_archive'     => false,
			'supports'        => array( 'title' ),
			'menu_icon'       => 'dashicons-images-alt',
		);

		register_post_type( $this->post_type, $args );
	}


	/**
	 * Registers metaboxes for the maxslider_playlist post type.
	 *
	 * @since 1.0.0
	 */
	public function add_meta_boxes() {
		add_meta_box( 'maxslider-meta-box-slides', esc_html__( 'Slides', 'maxslider' ), array( $this, 'metabox_slides' ), $this->post_type, 'normal', 'high' );
		add_meta_box( 'maxslider-meta-box-settings', esc_html__( 'Settings', 'maxslider' ), array( $this, 'metabox_settings' ), $this->post_type, 'normal', 'high' );
		add_meta_box( 'maxslider-meta-box-shortcode', esc_html__( 'Shortcode', 'maxslider' ), array( $this, 'metabox_shortcode' ), $this->post_type, 'normal', 'high' );
	}

	/**
	 * Echoes the Slides metabox markup.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $object
	 * @param array $box
	 */
	public function metabox_slides( $object, $box ) {
		$slides = $this->get_post_meta( $object->ID, '_maxslider_slides', array() );

		wp_nonce_field( basename( __FILE__ ), $object->post_type . '_nonce' );
		?>

		<?php $this->metabox_slides_header(); ?>

		<div class="maxslider-container">
			<?php $this->metabox_slides_field_controls(); ?>

			<?php $container_classes = apply_filters( 'maxslider_metabox_slides_container_classes', array( 'maxslider-fields-container' ) ); ?>

			<?php $this->metabox_slides_repeatable_slide_field( array(), array( 'is_template' => true ) ); ?>

			<div class="<?php echo esc_attr( implode( ' ', $container_classes ) ); ?>">
				<?php
					if ( ! empty( $slides ) ) {
						foreach ( $slides as $slide ) {
							$this->metabox_slides_repeatable_slide_field( $slide );
						}
					} else {
						$this->metabox_slides_repeatable_slide_field();
					}
				?>
			</div>

			<?php $this->metabox_slides_field_controls(); ?>
		</div>

		<?php $this->metabox_slides_footer(); ?>

		<input type="hidden" name="maxslider_nonce" id="maxslider_nonce" value="<?php echo esc_attr( wp_create_nonce( self::plugin_basename() ) ); ?>"/>
		<?php
	}


	/**
	 * Echoes the Slides metabox header.
	 *
	 * @since 1.0.0
	 */
	protected function metabox_slides_header() {
		if ( $this->is_whitelabel() ) {
			return;
		}

		?>
		<div class="maxslider-header maxslider-brand-module">
			<div class="maxslider-row">
				<div class="maxslider-col-left">
					<a href="https://www.cssigniter.com/ignite/plugins/maxslider?utm_source=dashboard&utm_medium=link&utm_content=maxslider&utm_campaign=logo"
					   target="_blank" class="maxslider-logo">
						<img
							src="<?php echo esc_url( $this->plugin_url() . 'assets/images/logo.svg' ); ?>"
							alt="<?php esc_attr_e( 'MaxSlider Logo', 'maxslider' ); ?>"
						>
					</a>
				</div>
			</div>
		</div>
		<?php
	}


	/**
	 * Echoes the Slides metabox footer.
	 *
	 * @since 1.0.0
	 */
	protected function metabox_slides_footer() {
		if ( $this->is_whitelabel() ) {
			return;
		}

		?>
		<div class="maxslider-footer maxslider-brand-module">
			<div class="maxslider-row">
				<div class="maxslider-col-left">
					<ul class="maxslider-list-inline">
						<?php
							$links = apply_filters( 'maxslider_metabox_slides_footer_links', array(
								'support'       => array(
									'title' => __( 'Support', 'maxslider' ),
									'url'   => 'https://wordpress.org/support/plugin/maxslider',
								),
								'documentation' => array(
									'title' => __( 'Documentation', 'maxslider' ),
									'url'   => 'https://www.cssigniter.com/docs/maxslider/',
								),
								'rate_plugin'   => array(
									'title' => __( 'Rate this plugin', 'maxslider' ),
									'url'   => 'https://wordpress.org/support/view/plugin-reviews/maxslider',
								),
							) );

							foreach ( $links as $link ) {
								if ( empty( $link['url'] ) || empty( $link['title'] ) ) {
									continue;
								}

								echo sprintf( '<li><a href="%s" target="_blank">%s</a></li>',
									esc_url( $link['url'] ),
									esc_html( $link['title'] )
								);
							}
						?>
					</ul>
				</div>

				<div class="maxslider-col-right">
					<?php
						$url  = 'https://www.cssigniter.com/ignite/plugins/maxslider?utm_source=dashboard&utm_medium=link&utm_content=maxslider&utm_campaign=footer-link';
						$copy = sprintf( __( 'Thank you for creating with <a href="%s" target="_blank">MaxSlider</a>', 'maxslider' ),
							esc_url( $url )
						);
					?>
					<div class="maxslider-brand-module-actions">
						<p class="maxslider-note"><?php echo wp_kses( $copy, array( 'a' => array( 'href' => true, 'target' => true ) ) ); ?></p>
					</div>
				</div>
			</div>
		</div>
		<?php
	}


	protected function metabox_slides_repeatable_slide_field( $slide = array(), $args = array() ) {
		$slide = wp_parse_args( $slide, self::get_default_slide_values() );
		$args  = wp_parse_args( $args, array(
			'is_template' => false,
		) );

		$template_id = '';
		if ( true === $args['is_template'] ) {
			$template_id = 'maxslider-fields-template';
		}

		$image_url = wp_get_attachment_image_src( intval( $slide['image_id'] ), 'thumbnail' );
		if ( ! empty( $image_url[0] ) ) {
			$image_url  = $image_url[0];
			$image_data = wp_prepare_attachment_for_js( intval( $slide['image_id'] ) );
		} else {
			$image_url  = '';
			$image_data = '';
		}

		$uid = uniqid();

		$field_classes = array_filter( apply_filters( 'maxslider_metabox_slide_classes', array( 'maxslider-field-repeatable' ), $slide ) );
		?>
		<div id="<?php echo esc_attr( $template_id ); ?>" class="<?php echo esc_attr( implode( ' ', $field_classes ) ); ?>" data-uid="<?php echo esc_attr( $uid ); ?>">

			<input type="hidden" class="maxslider-input-template" name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][is_template]" value="<?php echo esc_attr( intval( $args['is_template'] ) ); ?>">

			<div class="maxslider-field-head">

				<?php
					/**
					 * maxslider_metabox_slides_repeatable_slide_field_before_title hook.
					 *
					 * @hooked $this->action_metabox_slides_move_handle - 10
					 */
					do_action( 'maxslider_metabox_slides_repeatable_slide_field_before_title' );
				?>

				<span class="maxslider-field-title"><?php echo wp_kses( $slide['title'], array() ); ?></span>

				<button type="button" class="maxslider-field-toggle button-link">
					<span class="screen-reader-text">
						<?php esc_html_e( 'Toggle slide visibility', 'maxslider' ); ?>
					</span>
					<span class="toggle-indicator"></span>
				</button>
			</div>

			<div class="maxslider-field-container">
				<div class="maxslider-field-image">
					<a href="#" class="maxslider-field-upload-image <?php echo ! empty( $image_url ) ? 'maxslider-has-image' : ''; ?>">
						<span class="maxslider-remove-image">
							<span class="screen-reader-text">
								<?php esc_html_e( 'Remove Image', 'maxslider' ); ?>
							</span>
							<span class="dashicons dashicons-no-alt"></span>
						</span>

						<?php if ( ! empty( $image_url ) ) : ?>
							<img src="<?php echo esc_url( $image_url ); ?>" alt="<?php echo esc_attr( $image_data['alt'] ); ?>">
						<?php else : ?>
							<img src="#" alt="">
						<?php endif; ?>

						<div class="maxslider-field-image-placeholder">
							<span class="maxslider-cover-prompt">
								<?php esc_html_e( 'Upload Image', 'maxslider' ); ?>
							</span>
						</div>
					</a>

					<input
						type="hidden"
						id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-image_id"
						name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][image_id]"
						value="<?php echo esc_attr( $slide['image_id'] ); ?>"
					/>
				</div>

				<div class="maxslider-field-split">
					<div class="maxslider-field-left">
						<div class="maxslider-field-row-inline">
							<div class="maxslider-form-field">
								<label
									for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-title"
								>
									<?php esc_html_e( 'Title', 'maxslider' ); ?>
								</label>
								<input
									type="text"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-title"
									class="maxslider-slide-title"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][title]"
									placeholder="<?php esc_attr_e( 'Title', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['title'] ); ?>"
								/>
							</div>
							<div class="maxslider-form-field">
								<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-title-size">
									<?php esc_html_e( 'Title size', 'maxslider' ); ?>
								</label>
								<input
									type="number"
									min="0"
									step="1"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-title-size"
									class="maxslider-slide-title-size"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][title_size]"
									placeholder="<?php esc_attr_e( 'Title size', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['title_size'] ); ?>"
								/>
							</div>
							<div class="maxslider-form-field">
								<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-title-color">
									<?php esc_html_e( 'Title color', 'maxslider' ); ?>
								</label>
								<input
									type="text"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-title-color"
									class="maxslider-slide-title-color maxslider-colorpckr"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][title_color]"
									placeholder="<?php esc_attr_e( 'Title color', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['title_color'] ); ?>"
								/>
							</div>
						</div>

						<div class="maxslider-field-row-inline">
							<div class="maxslider-form-field">
								<label
									for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-subtitle"
								>
									<?php esc_html_e( 'Subtitle', 'maxslider' ); ?>
								</label>
								<input
									type="text"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-subtitle"
									class="maxslider-slide-subtitle"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][subtitle]"
									placeholder="<?php esc_attr_e( 'Subtitle', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['subtitle'] ); ?>"
								/>
							</div>
							<div class="maxslider-form-field">
								<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-subtitle-size">
									<?php esc_html_e( 'Subtitle size', 'maxslider' ); ?>
								</label>
								<input
									type="number"
									min="0"
									step="1"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-subtitle-size"
									class="maxslider-slide-subtitle-size"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][subtitle_size]"
									placeholder="<?php esc_attr_e( 'Subtitle size', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['subtitle_size'] ); ?>"
								/>
							</div>
							<div class="maxslider-form-field">
								<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-subtitle-color">
									<?php esc_html_e( 'Subtitle color', 'maxslider' ); ?>
								</label>
								<input
									type="text"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-subtitle-color"
									class="maxslider-slide-subtitle-color maxslider-colorpckr"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][subtitle_color]"
									placeholder="<?php esc_attr_e( 'Subtitle color', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['subtitle_color'] ); ?>"
								/>
							</div>
						</div>

						<div class="maxslider-field-row-inline field-row-equal-two">
							<div class="maxslider-form-field">
								<label
									for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button"
								>
									<?php esc_html_e( 'Button text', 'maxslider' ); ?>
								</label>
								<input
									type="text"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button"
									class="maxslider-slide-button"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][button]"
									placeholder="<?php esc_attr_e( 'Button text', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['button'] ); ?>"
								/>
							</div>
							<div class="maxslider-form-field">
								<label
									for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button-url"
								>
									<?php esc_html_e( 'Button URL', 'maxslider' ); ?>
								</label>
								<input
									type="text"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button-url"
									class="maxslider-slide-button-url"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][button_url]"
									placeholder="<?php esc_attr_e( 'Button URL', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['button_url'] ); ?>"
								/>
							</div>
						</div>

						<div class="maxslider-field-row-inline field-row-free-width">
							<div class="maxslider-form-field">
								<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button-bg-color">
									<?php esc_html_e( 'Button background color', 'maxslider' ); ?>
								</label>
								<input
									type="text"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button-bg-color"
									class="maxslider-slide-button-bg-color maxslider-colorpckr"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][button_bg_color]"
									placeholder="<?php esc_attr_e( 'Button background color', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['button_bg_color'] ); ?>"
								/>
							</div>
							<div class="maxslider-form-field">
								<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button-fg-color">
									<?php esc_html_e( 'Button text color', 'maxslider' ); ?>
								</label>
								<input
									type="text"
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button-fg-color"
									class="maxslider-slide-button-fg-color maxslider-colorpckr"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][button_fg_color]"
									placeholder="<?php esc_attr_e( 'Button text color', 'maxslider' ); ?>"
									value="<?php echo esc_attr( $slide['button_fg_color'] ); ?>"
								/>
							</div>
							<div class="maxslider-form-field">
								<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button-size">
									<?php esc_html_e( 'Button size', 'maxslider' ); ?>
								</label>
								<select
									id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-button-size"
									class="maxslider-slide-button-size"
									name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][button_size]"
									placeholder="<?php esc_attr_e( 'Button size', 'maxslider' ); ?>">
									<?php
										$options = self::get_slide_button_sizes();

										foreach ( $options as $value => $text ) {
											echo sprintf( '<option value="%s" %s>%s</option>',
												esc_attr( $value ),
												selected( $value, $slide['button_size'], false ),
												wp_kses( $text, array() )
											);
										}
									?>
								</select>
							</div>
						</div>
					</div>

					<div class="maxslider-field-right">
						<div class="maxslider-form-field">
							<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-content-align">
								<?php esc_html_e( 'Horizontal content alignment', 'maxslider' ); ?>
							</label>
							<select
								id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-content-align"
								class="maxslider-slide-content-align"
								name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][content_align]"
							>
								<?php
									$options = self::get_slide_content_align_options();

									foreach ( $options as $value => $text ) {
										echo sprintf( '<option value="%s" %s>%s</option>',
											esc_attr( $value ),
											selected( $value, $slide['content_align'], false ),
											wp_kses( $text, array() )
										);
									}
								?>
							</select>
						</div>
						<div class="maxslider-form-field">
							<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-content-valign">
								<?php esc_html_e( 'Vertical content alignment', 'maxslider' ); ?>
							</label>
							<select
								id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-content-valign"
								class="maxslider-slide-content-valign"
								name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][content_valign]"
							>
								<?php
									$options = self::get_slide_content_valign_options();

									foreach ( $options as $value => $text ) {
										echo sprintf( '<option value="%s" %s>%s</option>',
											esc_attr( $value ),
											selected( $value, $slide['content_valign'], false ),
											wp_kses( $text, array() )
										);
									}
								?>
							</select>
						</div>
						<div class="maxslider-form-field">
							<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-content-bg-color">
								<?php esc_html_e( 'Content background color', 'maxslider' ); ?>
							</label>
							<input
								type="text"
								id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-content-bg-color"
								class="maxslider-slide-content-bg-color maxslider-alpha-colorpckr"
								name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][content_bg_color]"
								placeholder="<?php esc_attr_e( 'Content background color', 'maxslider' ); ?>"
								value="<?php echo esc_attr( $slide['content_bg_color'] ); ?>"
							/>
						</div>

						<div class="maxslider-form-field">
							<label for="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-overlay-color">
								<?php esc_html_e( 'Slide overlay color', 'maxslider' ); ?>
							</label>
							<input
								type="text"
								id="maxslider_slides_slides-<?php echo esc_attr( $uid ); ?>-overlay-color"
								class="maxslider-slide-overlay-color maxslider-alpha-colorpckr"
								name="maxslider_slides_slides[<?php echo esc_attr( $uid ); ?>][overlay_color]"
								placeholder="<?php esc_attr_e( 'Slide overlay color', 'maxslider' ); ?>"
								value="<?php echo esc_attr( $slide['overlay_color'] ); ?>"
							/>
						</div>
					</div>
				</div>
			</div>

			<div class="maxslider-field-footer">
				<button type="button" class="button maxslider-remove-field">
					<span class="dashicons dashicons-dismiss"></span>
					<?php esc_html_e( 'Remove Slide', 'maxslider' ); ?>
				</button>
			</div>
		</div>
		<?php
	}


	protected function metabox_slides_field_controls() {
		?>
		<div class="maxslider-field-controls-wrap">
			<div class="maxslider-field-controls">
				<button type="button" class="button maxslider-add-field">
					<span class="dashicons dashicons-plus-alt"></span>
					<?php esc_html_e( 'Add Slide', 'maxslider' ); ?>
				</button>

				<?php do_action( 'maxslider_metabox_slides_field_controls' ); ?>

				<button type="button" class="button maxslider-remove-all-fields">
					<span class="dashicons dashicons-dismiss"></span>
					<?php esc_html_e( 'Remove all slides', 'maxslider' ); ?>
				</button>
			</div>

			<div class="maxslider-field-controls-visibility">
				<a href="#" class="maxslider-fields-expand-all">
					<?php esc_html_e( 'Expand All', 'maxslider' ); ?>
				</a>
				<a href="#" class="maxslider-fields-collapse-all">
					<?php esc_html_e( 'Collapse All', 'maxslider' ); ?>
				</a>
			</div>
		</div>
		<?php
	}


	/**
	 * Echoes the Settings metabox markup.
	 *
	 * @version 1.1.0
	 * @since 1.0.0
	 *
	 * @param WP_Post $object
	 * @param array $box
	 */
	function metabox_settings( $object, $box ) {
		$defaults = self::get_default_slider_values();

		$autoslide           = $this->get_post_meta( $object->ID, '_maxslider_autoslide', $defaults['autoslide'] );
		$effect              = $this->get_post_meta( $object->ID, '_maxslider_effect', $defaults['effect'] );
		$slide_speed         = $this->get_post_meta( $object->ID, '_maxslider_slide_speed', $defaults['slide_speed'] );
		$height              = $this->get_post_meta( $object->ID, '_maxslider_height', $defaults['height'] );
		$navigation          = $this->get_post_meta( $object->ID, '_maxslider_navigation', $defaults['navigation'] );
		$navigation_position = $this->get_post_meta( $object->ID, '_maxslider_navigation_position', $defaults['navigation_position'] );
		$navigation_fg_color = $this->get_post_meta( $object->ID, '_maxslider_navigation_fg_color', $defaults['navigation_fg_color'] );
		$navigation_bg_color = $this->get_post_meta( $object->ID, '_maxslider_navigation_bg_color', $defaults['navigation_bg_color'] );
		$image_size          = $this->get_post_meta( $object->ID, '_maxslider_image_size', $defaults['image_size'] );

		wp_nonce_field( basename( __FILE__ ), $object->post_type . '_nonce' );
		?>
		<div class="maxslider-module maxslider-module-settings">
			<div class="maxslider-form-field">

				<label for="_maxslider_autoslide">
					<input
						type="checkbox"
						id="_maxslider_autoslide"
						name="_maxslider_autoslide"
						class="maxslider-checkbox"
						value="1" <?php checked( $autoslide, 1 ); ?>
					/>
					<?php esc_html_e( 'Auto slide', 'maxslider' ); ?>
				</label>
			</div>

			<div class="maxslider-form-field">
				<label for="_maxslider_effect">
					<?php esc_html_e( 'Slide effect', 'maxslider' ); ?>
				</label>

				<select id="_maxslider_effect" name="_maxslider_effect">
					<?php
						$options = self::get_slide_effects();

						foreach ( $options as $value => $text ) {
							echo sprintf( '<option value="%s" %s>%s</option>',
								esc_attr( $value ),
								selected( $value, $effect, false ),
								wp_kses( $text, array() )
							);
						}
					?>
				</select>
			</div>

			<div class="maxslider-form-field">
				<label for="_maxslider_slide_speed">
					<?php esc_html_e( 'Pause between slides', 'maxslider' ); ?>
				</label>

				<input
					type="number"
					min="0"
					step="50"
					id="_maxslider_slide_speed"
					name="_maxslider_slide_speed"
					class="maxslider-slide-title"
					placeholder="<?php esc_attr_e( 'Pause between slides', 'maxslider' ); ?>"
					value="<?php echo esc_attr( $slide_speed ); ?>"
				/>

				<p class="maxslider-field-help">
					<?php esc_html_e( 'Time in milliseconds.', 'maxslider' ); ?>
				</p>
			</div>

			<div class="maxslider-form-field">
				<label for="_maxslider_height">
					<?php esc_html_e( 'Height', 'maxslider' ); ?>
				</label>

				<input
					type="number"
					id="_maxslider_height"
					name="_maxslider_height"
					class="maxslider-slide-title"
					placeholder="<?php esc_attr_e( 'Height', 'maxslider' ); ?>"
					value="<?php echo esc_attr( $height ); ?>"
				/>

				<p class="maxslider-field-help">
					<?php esc_html_e( 'Provide the height of this slider (leaving this empty will apply the default of 600px).', 'maxslider' ); ?>
				</p>
			</div>

			<div class="maxslider-form-field">
				<label for="_maxslider_navigation">
					<?php esc_html_e( 'Navigation type', 'maxslider' ); ?>
				</label>

				<select id="_maxslider_navigation" name="_maxslider_navigation">
					<?php
						$options = self::get_slider_navigation_options();

						foreach ( $options as $value => $text ) {
							echo sprintf( '<option value="%s" %s>%s</option>',
								esc_attr( $value ),
								selected( $value, $navigation, false ),
								wp_kses( $text, array() )
							);
						}
					?>
				</select>

				<p class="maxslider-field-help">
					<?php esc_html_e( 'Please note that on mobile devices the slider will always display dots instead of arrows regardless of this setting. The "none" setting will always be respected.', 'maxslider' ); ?>
				</p>
			</div>

			<div class="maxslider-form-field">
				<label for="_maxslider_navigation_position">
					<?php esc_html_e( 'Arrows position', 'maxslider' ); ?>
				</label>

				<select id="_maxslider_navigation_position" name="_maxslider_navigation_position">
					<?php
						$options = self::get_slider_navigation_position_options();

						foreach ( $options as $value => $text ) {
							echo sprintf( '<option value="%s" %s>%s</option>',
								esc_attr( $value ),
								selected( $value, $navigation_position, false ),
								wp_kses( $text, array() )
							);
						}
					?>
				</select>

				<p class="maxslider-field-help">
					<?php esc_html_e( 'Applies only to arrow navigation.', 'maxslider' ); ?>
				</p>
			</div>


			<div class="maxslider-form-field">
				<label for="_maxslider_navigation_fg_color">
					<?php esc_html_e( 'Navigation color', 'maxslider' ); ?>
				</label>
				<input
					type="text"
					id="_maxslider_navigation_fg_color"
					class="maxslider-slide-navigation-fg-color maxslider-colorpckr"
					name="_maxslider_navigation_fg_color"
					placeholder="<?php esc_attr_e( 'Navigation color', 'maxslider' ); ?>"
					value="<?php echo esc_attr( $navigation_fg_color ); ?>"
				/>
			</div>

			<div class="maxslider-form-field">
				<label for="_maxslider_navigation_bg_color">
					<?php esc_html_e( 'Navigation background color', 'maxslider' ); ?>
				</label>
				<input
					type="text"
					id="_maxslider_navigation_bg_color"
					class="maxslider-slide-navigation-bg-color maxslider-colorpckr"
					name="_maxslider_navigation_bg_color"
					placeholder="<?php esc_attr_e( 'Navigation background color', 'maxslider' ); ?>"
					value="<?php echo esc_attr( $navigation_bg_color ); ?>"
				/>
			</div>

			<div class="maxslider-form-field">
				<label for="_maxslider_image_size">
					<?php esc_html_e( 'Image size', 'maxslider' ); ?>
				</label>
				<select
					type="text"
					id="_maxslider_image_size"
					class="maxslider-slide-image-size"
					name="_maxslider_image_size"
				>
					<?php echo $this->get_image_sizes_html_options( $image_size, $defaults['image_size'] ); ?>
				</select>
			</div>

		</div>
		<?php
	}


	/**
	 * Echoes the Shortcode metabox markup.
	 *
	 * @since 1.0.0
	 *
	 * @param WP_Post $object
	 * @param array $box
	 */
	public function metabox_shortcode( $object, $box ) {
		?>
		<div class="maxslider-module maxslider-module-shortcode">
			<div class="maxslider-form-field">
				<label for="maxslider_shortcode">
					<?php esc_html_e( 'Grab the shortcode', 'maxslider' ); ?>
				</label>

				<input
					type="text"
					class="code"
					id="maxslider_shortcode"
					name="maxslider_shortcode"
					value="<?php echo esc_attr( sprintf( '[maxslider id="%s"]', $object->ID ) ); ?>"
				/>

			</div>
		</div>
		<?php
	}

	/**
	 * Saves the slider's metabox values.
	 *
	 * @version 1.1.0
	 * @since 1.0.0
	 */
	public function save_post( $post_id ) {
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) { return false; }
		if ( isset( $_POST['post_view'] ) && $_POST['post_view'] === 'list' ) { return false; }
		if ( ! isset( $_POST['post_type'] ) || $_POST['post_type'] !== $this->post_type ) { return false; }
		if ( ! isset( $_POST[ $this->post_type . '_nonce' ] ) || ! wp_verify_nonce( $_POST[ $this->post_type . '_nonce' ], basename( __FILE__ ) ) ) { return false; }
		$post_type_obj = get_post_type_object( $this->post_type );
		if ( ! current_user_can( $post_type_obj->cap->edit_post, $post_id ) ) { return false; }

		update_post_meta( $post_id, '_maxslider_slides', $this->sanitizer->metabox_slider( $_POST['maxslider_slides_slides'], $post_id ) );

		update_post_meta( $post_id, '_maxslider_autoslide', $this->sanitizer->checkbox_ref( $_POST['_maxslider_autoslide'] ) );
		update_post_meta( $post_id, '_maxslider_effect', $this->sanitizer->slide_effect( $_POST['_maxslider_effect'] ) );
		update_post_meta( $post_id, '_maxslider_slide_speed', absint( $_POST['_maxslider_slide_speed'] ) );
		update_post_meta( $post_id, '_maxslider_height', $this->sanitizer->intval_or_empty( $_POST['_maxslider_height'] ) );
		update_post_meta( $post_id, '_maxslider_navigation', $this->sanitizer->slider_navigation( $_POST['_maxslider_navigation'] ) );
		update_post_meta( $post_id, '_maxslider_navigation_position', $this->sanitizer->slider_navigation_position( $_POST['_maxslider_navigation_position'] ) );
		update_post_meta( $post_id, '_maxslider_navigation_fg_color', $this->sanitizer->hex_color( $_POST['_maxslider_navigation_fg_color'] ) );
		update_post_meta( $post_id, '_maxslider_navigation_bg_color', $this->sanitizer->hex_color( $_POST['_maxslider_navigation_bg_color'] ) );
		update_post_meta( $post_id, '_maxslider_image_size', $this->sanitizer->usable_image_size( $_POST['_maxslider_image_size'] ) );
	}


	/**
	 * Adds the necessary classes to enable sortable slides.
	 *
	 * Applied as a filter on 'maxslider_metabox_slides_container_classes'
	 *
	 * @since 1.1.0
	 *
	 * @param array $classes
	 *
	 * @return array
	 */
	public function filter_metabox_slides_container_classes_sortable( $classes ) {
		// Enable sortable for the repeatable slides.
		$classes[] = 'maxslider-fields-sortable';

		return $classes;
	}

	/**
	 * Outputs the necessary markup to enable sortable slides.
	 *
	 * @since 1.1.0
	 */
	public function action_metabox_slides_move_handle() {
		?>
		<span class="maxslider-field-sort-handle">
			<span class="screen-reader-text"><?php esc_html_e( 'Move slide', 'maxslider' ); ?></span>
			<span class="dashicons dashicons-move"></span>
		</span>
		<?php
	}

	/**
	 * Outputs the necessary markup to enable batch upload of slides.
	 *
	 * @since 1.1.0
	 */
	public function action_metabox_slides_field_controls_batch_upload() {
		?>
		<button type="button" class="button maxslider-add-field-batch">
			<span class="dashicons dashicons-upload"></span>
			<?php esc_html_e( 'Batch Upload', 'maxslider' ); ?>
		</button>
		<?php
	}

	/**
	 * Compares to images by width. For use in ascending sorting, with functions such as usort() and uasort().
	 *
	 * @since 1.1.0
	 */
	public function image_size_width_compare_callback( $a, $b ) {
	   return $a['width'] - $b['width'];
	}


	/**
	 * Returns the default values for a new slide.
	 *
	 * @version 1.0.0
	 * @since 1.0.0
	 */
	public static function get_default_slide_values() {
		return apply_filters( 'maxslider_default_slide_values', array(
			'image_id'         => '',
			'title'            => '',
			'title_size'       => '',
			'title_color'      => '',
			'subtitle'         => '',
			'subtitle_size'    => '',
			'subtitle_color'   => '',
			'button'           => '',
			'button_url'       => '',
			'button_bg_color'  => '',
			'button_fg_color'  => '',
			'button_size'      => 'maxslider-btn-default',
			'content_align'    => 'maxslider-align-center',
			'content_valign'   => 'maxslider-align-middle',
			'content_bg_color' => '',
			'overlay_color'    => '',
		) );
	}

	/**
	 * Returns the default values for a new slider.
	 *
	 * @version 1.1.0
	 * @since 1.0.0
	 */
	public static function get_default_slider_values() {
		return apply_filters( 'maxslider_default_slider_values', array(
			'autoslide'           => 1,
			'effect'              => 'fade',
			'slide_speed'         => 5000,
			'height'              => '',
			'navigation'          => 'arrows',
			'navigation_position' => '',
			'navigation_fg_color' => '',
			'navigation_bg_color' => '',
			'image_size'          => 'maxslider_slide',
		) );
	}

	public function register_image_sizes() {
		$sizes = $this->image_sizes();

		foreach ( $sizes as $name => $size ) {
			add_image_size( $name, $size['width'], $size['height'], $size['crop'] );
		}
	}

	protected function image_sizes() {
		return apply_filters( 'maxslider_image_sizes', array(
			'maxslider_slide' => array(
				'width'  => 750,
				'height' => 450,
				'crop'   => true,
			),
		) );
	}


	/**
	 * Return an array of usable image sizes along with dimension and crop information.
	 *
	 * @global $wp_additional_image_sizes
	 * @uses get_option
	 * @return array
	 */
	public function usable_image_sizes() {
		if ( empty( $this->usable_image_sizes ) ) {
			global $_wp_additional_image_sizes;
			$sizes = array(
				'thumbnail' => array(
					'width'  => absint( get_option( 'thumbnail_size_w' ) ),
					'height' => absint( get_option( 'thumbnail_size_h' ) ),
					'crop'   => (bool) get_option( 'thumbnail_crop' ),
				),
				'medium'    => array(
					'width'  => absint( get_option( 'medium_size_w' ) ),
					'height' => absint( get_option( 'medium_size_h' ) ),
					'crop'   => (bool) get_option( 'medium_crop' ),
				),
				'large'     => array(
					'width'  => absint( get_option( 'large_size_w' ) ),
					'height' => absint( get_option( 'large_size_h' ) ),
					'crop'   => (bool) get_option( 'large_crop' ),
				),
				'full'      => array(
					'width'  => 0,
					'height' => 0,
					'crop'   => false,
				),
			);

			$sizes = array_merge( $_wp_additional_image_sizes, $sizes );

			$this->usable_image_sizes = apply_filters( 'maxslider_usable_image_sizes', $sizes );
		}

		return $this->usable_image_sizes;
	}


	public function get_image_sizes_html_options( $selected = '', $default = 'maxslider_slide' ) {
		$sizes = $this->usable_image_sizes();

		uasort( $sizes, array( $this, 'image_size_width_compare_callback' ) );

		// Fallback to the default image size, if the selected isn't available (e.g. theme changed).
		if ( ! array_key_exists( $selected, $sizes ) && array_key_exists( $default, $sizes ) ) {
			$selected = $default;
		}

		$group_wordpress = array();
		$group_maxslider = array();
		$group_other     = array();


		foreach ( $sizes as $img_name => $img_size ) {
			if ( in_array( $img_name, array( 'thumbnail', 'medium', 'large', 'full' ), true ) ) {
				$group_wordpress[ $img_name ] = $img_size;
			} elseif ( array_key_exists( $img_name, $this->image_sizes() ) ) {
				$group_maxslider[ $img_name ] = $img_size;
			} else {
				$group_other[ $img_name ] = $img_size;
			}
		}

		/* translators: %1$s is width. %2$s is height. */
		$size_label = __( '%1$s&times;%2$s', 'maxslider' );
		$full_label = __( 'Full size', 'maxslider' );
		$label      = '';
		$options    = array();

		if ( ! empty( $group_other ) ) {
			$options[] = sprintf( '<optgroup label="%s">', esc_attr__( 'Set by: Theme / Plugin', 'ci-theme' ) );
			foreach ( $group_other as $img_name => $img_size ) {
				$label     = $size_label;
				$options[] = sprintf( '<option value="%s" %s>%s</option>',
					esc_attr( $img_name ),
					selected( $selected, $img_name, false ),
					wp_kses( sprintf( $label, $img_size['width'], $img_size['height'] ), 'strip' )
				);
			}
			$options[] = '</optgroup>';
		}

		if ( ! empty( $group_maxslider ) ) {
			$options[] = sprintf( '<optgroup label="%s">', esc_attr__( 'Set by: MaxSlider', 'ci-theme' ) );
			foreach ( $group_maxslider as $img_name => $img_size ) {
				$label     = $size_label;
				$options[] = sprintf( '<option value="%s" %s>%s</option>',
					esc_attr( $img_name ),
					selected( $selected, $img_name, false ),
					wp_kses( sprintf( $label, $img_size['width'], $img_size['height'] ), 'strip' )
				);
			}
			$options[] = '</optgroup>';
		}

		if ( ! empty( $group_wordpress ) ) {
			$options[] = sprintf( '<optgroup label="%s">', esc_attr__( 'Set by: Settings > Media', 'ci-theme' ) );
			foreach ( $group_wordpress as $img_name => $img_size ) {
				$label = $size_label;
				if ( 'full' === $img_name ) {
					$label = $full_label;
				}
				$options[] = sprintf( '<option value="%s" %s>%s</option>',
					esc_attr( $img_name ),
					selected( $selected, $img_name, false ),
					wp_kses( sprintf( $label, $img_size['width'], $img_size['height'] ), 'strip' )
				);
			}
			$options[] = '</optgroup>';
		}

		return implode( PHP_EOL, $options );
	}

	public static function get_slide_button_sizes() {
		return apply_filters( 'maxslider_button_sizes', array(
			'maxslider-btn-xs'      => esc_html__( 'Extra small', 'maxslider' ),
			'maxslider-btn-sm'      => esc_html__( 'Small', 'maxslider' ),
			'maxslider-btn-default' => esc_html__( 'Default', 'maxslider' ),
			'maxslider-btn-lg'      => esc_html__( 'Large', 'maxslider' ),
		) );
	}

	public static function get_slide_content_align_options() {
		return apply_filters( 'maxslider_content_align_options', array(
			'maxslider-align-left'   => esc_html__( 'Left', 'maxslider' ),
			'maxslider-align-center' => esc_html__( 'Center', 'maxslider' ),
			'maxslider-align-right'  => esc_html__( 'Right', 'maxslider' ),
		) );
	}

	public static function get_slide_content_valign_options() {
		return apply_filters( 'maxslider_content_valign_options', array(
			'maxslider-align-top'    => esc_html__( 'Top', 'maxslider' ),
			'maxslider-align-middle' => esc_html__( 'Middle', 'maxslider' ),
			'maxslider-align-bottom' => esc_html__( 'Bottom', 'maxslider' ),
		) );
	}

	/**
	 * Returns an array of valid navigation options.
	 *
	 * @version 1.1.0
	 * @since 1.1.0
	 */
	public static function get_slider_navigation_options() {
		return apply_filters( 'maxslider_navigation_options', array(
			''       => esc_html__( 'None', 'maxslider' ),
			'arrows' => esc_html__( 'Arrows', 'maxslider' ),
			'dots'   => esc_html__( 'Dots', 'maxslider' ),
		) );
	}

	/**
	 * Returns an array of valid navigation position options.
	 *
	 * @version 1.1.0
	 * @since 1.1.0
	 */
	public static function get_slider_navigation_position_options() {
		return apply_filters( 'maxslider_navigation_position_options', array(
			''                           => esc_html__( 'Default (vertically centered)', 'maxslider' ),
			'maxslider-nav-top-left'     => esc_html__( 'Top Left', 'maxslider' ),
			'maxslider-nav-top-right'    => esc_html__( 'Top Right', 'maxslider' ),
			'maxslider-nav-bottom-left'  => esc_html__( 'Bottom Left', 'maxslider' ),
			'maxslider-nav-bottom-right' => esc_html__( 'Bottom Right', 'maxslider' ),
		) );
	}

	public static function get_slide_effects() {
		return apply_filters( 'maxslider_effects', array(
			'fade'  => esc_html__( 'Fade', 'maxslider' ),
			'slide' => esc_html__( 'Slide', 'maxslider' ),
		) );
	}

	public function register_shortcodes() {
		add_shortcode( 'maxslider', array( $this, 'shortcode_maxslider' ) );
	}

	public function register_vc_shortcodes() {
		require_once( 'class-maxslider-visual-composer-slider.php' );
		$vc_shortcode = new MaxSlider_Visual_Composer_Slider();
		$vc_shortcode->load();
	}

	public function get_slider_array( $id ) {
		$slider = array();

		if ( empty( $id ) ) {
			return $slider;
		}

		$id   = intval( $id );
		$post = get_post( $id );

		if ( empty( $post ) || ! is_object( $post ) || ! ( $post instanceof WP_Post ) || $post->post_type !== $this->post_type ) {
			return $slider;
		}

		$slides = $this->get_post_meta( $id, '_maxslider_slides', array() );
		$params = $this->get_slider_parameters_array( $id );

		foreach ( $slides as $slide_id => $slide ) {
			$slides[ $slide_id ] = wp_parse_args( $slide, self::get_default_slide_values() );

			// Slide needs to be aware of its index within the slides array, so that
			// meaningful CSS selectors can be constructed.
			$slides[ $slide_id ]['id'] = $slide_id;
		}

		$slider = array(
			'id'          => $id,
			'params'      => $params,
			'slides'      => $slides,
			'data_string' => $this->get_slider_parameters_data_string( $id ),
		);

		return apply_filters( 'maxslider_get_slider_array', $slider, $id );
	}

	/**
	 * Returns an array of valid slider options. Defaults are used where there are no values set.
	 *
	 * @version 1.1.0
	 * @since 1.0.0
	 */
	public function get_slider_parameters_array( $id ) {
		$defaults = self::get_default_slider_values();

		return apply_filters( 'maxslider_get_slider_parameters_array', array(
			'autoslide'           => (bool) $this->get_post_meta( $id, '_maxslider_autoslide', $defaults['autoslide'] ),
			'effect'              => $this->get_post_meta( $id, '_maxslider_effect', $defaults['effect'] ),
			'slide-speed'         => absint( $this->get_post_meta( $id, '_maxslider_slide_speed', $defaults['slide_speed'] ) ),
			'height'              => $this->sanitizer->intval_or_empty( $this->get_post_meta( $id, '_maxslider_height', $defaults['height'] ) ),
			'navigation'          => $this->get_post_meta( $id, '_maxslider_navigation', $defaults['navigation'] ),
			'navigation_position' => $this->get_post_meta( $id, '_maxslider_navigation_position', $defaults['navigation_position'] ),
			'navigation_fg_color' => $this->get_post_meta( $id, '_maxslider_navigation_fg_color', $defaults['navigation_fg_color'] ),
			'navigation_bg_color' => $this->get_post_meta( $id, '_maxslider_navigation_bg_color', $defaults['navigation_bg_color'] ),
			'image_size'          => $this->get_post_meta( $id, '_maxslider_image_size', $defaults['image_size'] ),
		), $id );
	}

	/**
	 * Returns a string of slider-related, valid data attributes, for inclusion inside an HTML tag.
	 *
	 * @version 1.1.0
	 * @since 1.0.0
	 */
	public function get_slider_parameters_data_string( $id ) {
		$params = $this->get_slider_parameters_array( $id );

		// Do necessary conversions for the slider script.
		$params['autoslide'] = $this->convert_bool_string( $params['autoslide'] );

		// Navigation position is applied as a class via the 'maxslider_slider_classes' filter.
		unset( $params['navigation_position'] );

		$string = '';
		foreach ( $params as $attribute => $value ) {
			$string .= sprintf( 'data-%s="%s" ', sanitize_key( $attribute ), esc_attr( $value ) );
		}

		return apply_filters( 'maxslider_slider_parameters_data_string', $string, $id );
	}

	public function shortcode_maxslider( $atts, $content = null, $tag ) {
		$atts = shortcode_atts( array(
			'id'       => '',
			'template' => '',
		), $atts, $tag );


		$id = intval( $atts['id'] );
		$id = apply_filters( 'wpml_object_id', $id, $this->post_type, true );

		$slider = $this->get_slider_array( $id );
		if ( empty( $slider ) ) {
			return '';
		}

		$slider['template'] = $atts['template'];

		ob_start();

		$this->get_template_part( 'slider', $atts['template'], array(
			'slider'   => $slider,
			'template' => $atts['template'],
		) );

		$output = ob_get_clean();

		return $output;
	}


	/**
	 * Generates and enqueues CSS for a specific slider.
	 *
	 * @since 1.1.0
	 *
	 * @param array $slider The slider array to generate/enqueue the CSS for.
	 */
	public function enqueue_slider_css( $slider ) {
		ob_start();

		if ( ! empty( $slider['params']['height'] ) ) :
			?>
			#maxslider-<?php echo sanitize_html_class( $slider['id'] ); ?> {
				height: <?php echo intval( $slider['params']['height'] ); ?>px;
			}
			<?php
		endif;

		if ( ! empty( $slider['params']['navigation_fg_color'] ) || ! empty( $slider['params']['navigation_bg_color'] ) ) :
			?>
			#maxslider-<?php echo sanitize_html_class( $slider['id'] ); ?> .slick-arrow,
			#maxslider-<?php echo sanitize_html_class( $slider['id'] ); ?> .slick-dots button {
				<?php if ( ! empty( $slider['params']['navigation_fg_color'] ) ) : ?>
					color: <?php echo $slider['params']['navigation_fg_color']; ?>;
				<?php endif; ?>

				<?php if ( ! empty( $slider['params']['navigation_bg_color'] ) ) : ?>
					background-color: <?php echo $slider['params']['navigation_bg_color']; ?>;
				<?php endif; ?>
			}
			<?php
		endif;

		foreach ( $slider['slides'] as $slide ) {
			$slide_class = "maxslider-{$slider['id']}-slide-{$slide['id']}";

			$image_url = self::get_image_src( intval( $slide['image_id'] ), $slider['params']['image_size'] );

			if ( ! empty( $image_url ) ) {
				?>
				.<?php echo sanitize_html_class( $slide_class ); ?> {
					background-image: url(<?php echo esc_url_raw( $image_url ); ?>);
				}
				<?php
			}

			if ( ! empty( $slide['content_bg_color'] ) ) {
				?>
				.<?php echo sanitize_html_class( $slide_class ); ?> .maxslider-slide-content-pad {
					padding: 25px;
					background-color: <?php echo $slide['content_bg_color']; ?>;
				}
				<?php
			}

			if ( ! empty( $slide['overlay_color'] ) ) {
				?>
				.<?php echo sanitize_html_class( $slide_class ); ?>::before {
					background-color: <?php echo $slide['overlay_color']; ?>;
				}
				<?php
			}

			if ( ! empty( $slide['title'] ) ) {
				$style = '';
				if ( ! empty( $slide['title_size'] ) ) {
					$style .= sprintf( 'font-size: %spx; ', $slide['title_size'] );
				}
				if ( ! empty( $slide['title_color'] ) ) {
					$style .= sprintf( 'color: %s; ', $slide['title_color'] );
				}
				?>
				.<?php echo sanitize_html_class( $slide_class ); ?> .maxslider-slide-title {
					<?php echo $style; ?>
				}
				<?php
			}

			if ( ! empty( $slide['subtitle'] ) ) {
				$style = '';
				if ( ! empty( $slide['subtitle_size'] ) ) {
					$style .= sprintf( 'font-size: %spx; ', $slide['subtitle_size'] );
				}
				if ( ! empty( $slide['subtitle_color'] ) ) {
					$style .= sprintf( 'color: %s; ', $slide['subtitle_color'] );
				}
				?>
				.<?php echo sanitize_html_class( $slide_class ); ?> .maxslider-slide-subtitle {
					<?php echo $style; ?>
				}
				<?php
			}

			if ( ! empty( $slide['button'] ) && ! empty( $slide['button_url'] ) ) {
				$style = '';
				if ( ! empty( $slide['button_fg_color'] ) ) {
					$style .= sprintf( 'color: %s; ', $slide['button_fg_color'] );
				}
				if ( ! empty( $slide['button_bg_color'] ) ) {
					$style .= sprintf( 'background-color: %s; ', $slide['button_bg_color'] );
				}
				?>
				.<?php echo sanitize_html_class( $slide_class ); ?> .maxslider-btn,
				.<?php echo sanitize_html_class( $slide_class ); ?> .maxslider-btn:hover,
				.<?php echo sanitize_html_class( $slide_class ); ?> .maxslider-btn:focus {
					<?php echo $style; ?>
				}
				<?php
			}

		}

		$css = ob_get_clean();
		$css = apply_filters( 'maxslider_enqueue_slider_css', $css, $slider );

		wp_enqueue_style( 'maxslider-footer' );
		wp_add_inline_style( 'maxslider-footer', $css );
	}


	public function get_template_part( $slug, $name = '', $template_vars = array() ) {
		$templates = array();
		$name = (string) $name;
		if ( '' !== $name ) {
			$templates[] = "{$slug}-{$name}.php";
		}

		$templates[] = "{$slug}.php";

		$located = $this->locate_template( $templates );

		if ( ! empty( $located ) ) {
			include( $located );
		}
	}

	public function locate_template( $templates ) {
		$default_path = trailingslashit( trailingslashit( $this->plugin_path() ) . 'templates' );
		$theme_path   = trailingslashit( apply_filters( 'maxslider_locate_template_theme_path', 'maxslider' ) );

		$theme_templates = array();
		foreach ( $templates as $template ) {
			$theme_templates[] = $theme_path . $template;
		}

		// Try to find a theme-overriden template.
		$located = locate_template( $theme_templates, false );

		if ( empty( $located ) ) {
			// Nope. Try the plugin templates instead.
			foreach ( $templates as $template ) {
				if ( file_exists( $default_path . $template ) ) {
					$located = $default_path . $template;
					break;
				}
			}
		}

		return $located;
	}

	public function convert_bool_string( $value ) {
		if ( $value ) {
			return 'true';
		}

		return 'false';
	}

	public function get_all_sliders( $orderby = 'date', $order = 'DESC' ) {
		$q = new WP_Query( array(
			'post_type'      => $this->post_type,
			'posts_per_page' => - 1,
			'orderby'        => $orderby,
			'order'          => $order,
		) );

		return $q->posts;
	}

	/**
	 * Returns just the URL of an image attachment.
	 *
	 * @since 1.0.0
	 * @version 1.0.0
	 *
	 * @param int $image_id The Attachment ID of the desired image.
	 * @param string $size Optional. The size of the image to return. Default 'full'.
	 * @return bool|string False on failure, image URL on success.
	 */
	public static function get_image_src( $image_id, $size = 'full' ) {
		$img_attr = wp_get_attachment_image_src( intval( $image_id ), $size );
		if ( ! empty( $img_attr[0] ) ) {
			return $img_attr[0];
		}

		return false;
	}


	/**
	 * Retrieve post meta field for a post.
	 *
	 * @since 1.0.0
	 * @version 1.1.0
	 *
	 * @uses get_post_meta(), get_post_custom_keys()
	 *
	 * @param int $post_id Post ID.
	 * @param string $key The meta key to retrieve.
	 * @param mixed $default Optional. Value to return if meta doesn't exist. Default empty string.
	 *
	 * @return mixed
	 */
	public function get_post_meta( $post_id, $key, $default = '' ) {
		$keys = get_post_custom_keys( $post_id );

		$value = $default;

		if ( is_array( $keys ) && in_array( $key, $keys, true ) ) {
			$value = get_post_meta( $post_id, $key, true );
		}

		return apply_filters( 'maxslider_get_post_meta', $value, $post_id, $key, $default );
	}


	/**
	 * Whitelabel status.
	 *
	 * Returns true if the plugin is white-labeled, false otherwise.
	 * In order to white label the plugin, you'll need to add a call:
	 * add_filter( 'maxslider_whitelabel', '__return_true' );
	 *
	 * @since 1.1.0
	 * @public
	 * @return bool
	 */
	public function is_whitelabel() {
		if ( is_bool( $this->whitelabel ) ) {
			return $this->whitelabel;
		}

		if ( apply_filters( 'maxslider_whitelabel', false ) || ( defined( 'MAXSLIDER_WHITELABEL' ) && MAXSLIDER_WHITELABEL === true ) ) {
			$this->whitelabel = true;
		} else {
			$this->whitelabel = false;
		}

		return $this->whitelabel;
	}


	/**
	 * Runs on plugin activation.
	 *
	 * @version 1.1.0
	 * @since 1.0.0
	 */
	public function plugin_activated() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		if ( false === get_option( 'maxslider_db_version' ) ) {
			update_option( 'maxslider_db_version', self::$version );
		}

		$this->register_post_types();

		do_action( 'maxslider_activated' );

		flush_rewrite_rules();
	}

	public function plugin_deactivated() {
		if ( ! current_user_can( 'activate_plugins' ) ) {
			return;
		}

		unregister_post_type( $this->post_type );

		do_action( 'maxslider_deactivated' );

		flush_rewrite_rules();
	}

	/**
	 * Determines and starts a database upgrade if needed.
	 *
	 * @version 1.1.0
	 * @since 1.1.0
	 */
	public function maybe_upgrade() {
		include_once( dirname( __FILE__ ) . '/class-maxslider-upgrader.php' );
		$db_upgrader = new MaxSlider_Upgrader();
		$db_upgrader->do_upgrade();
	}

	public static function plugin_basename() {
		return plugin_basename( __FILE__ );
	}

	public function plugin_url() {
		return self::$plugin_url;
	}

	public function plugin_path() {
		return self::$plugin_path;
	}
}


/**
 * Main instance of MaxSlider.
 *
 * Returns the working instance of MaxSlider. No need for globals.
 *
 * @since  1.0.0
 * @return MaxSlider
 */
function MaxSlider() {
	return MaxSlider::instance();
}

add_action( 'plugins_loaded', array( MaxSlider(), 'plugin_setup' ) );
register_activation_hook( __FILE__, array( MaxSlider(), 'plugin_activated' ) );
register_deactivation_hook( __FILE__, array( MaxSlider(), 'plugin_deactivated' ) );
