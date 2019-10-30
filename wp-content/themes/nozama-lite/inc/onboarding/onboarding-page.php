<?php

if ( ! class_exists( 'Nozama_Lite_Onboarding_Page' ) ) {

	/**
	 * Class used to generate the onboarding page.
	 */
	class Nozama_Lite_Onboarding_Page {

		/**
		 * Used to pass all custom onboarding data
		 *
		 * @var null
		 */
		protected $data = null;

		/**
		 * The theme name
		 *
		 * @var null
		 */
		private $theme_name = null;

		/**
		 * The theme slug
		 *
		 * @var null
		 */
		private $theme_slug = null;

		/**
		 * Stores the theme object
		 *
		 * @var null
		 */
		private $theme = null;

		/**
		 * The theme version
		 *
		 * @var null
		 */
		private $theme_version = null;

		/**
		 * The title of the onboarding page on the WP menu
		 *
		 * @var null
		 */
		private $menu_title = null;

		/**
		 * The title of the onboarding page on the page
		 *
		 * @var null
		 */
		private $page_title = null;

		/**
		 * Onboarding page initialization
		 *
		 * @param array $data Custom onboarding data.
		 */
		public function init( $data ) {
			if ( ! empty( $data ) && is_array( $data ) && true === $data['show_page'] ) {
				$this->data = wp_parse_args( $data, $this->default_data() );
				$this->themedata_setup();
				$this->page_setup();
			}
		}

		/**
		 * Setup theme and custom data
		 *
		 * @return void
		 */
		public function themedata_setup() {
			$theme    = wp_get_theme();
			$defaults = $this->default_data();

			if ( is_child_theme() ) {
				$this->theme_name = $theme->parent()->get( 'Name' );
				$this->theme      = $theme->parent();
			} else {
				$this->theme_name = $theme->get( 'Name' );
				$this->theme      = $theme;
			}
			$this->theme_version = $theme->get( 'Version' );
			$this->theme_slug    = $theme->get_template();

			$this->menu_title = ! empty( $this->data['menu_title'] ) ? $this->data['menu_title'] : $defaults['menu_title'];
			$this->page_title = ! empty( $this->data['page_title'] ) ? $this->data['page_title'] : $defaults['page_title'];
		}

		/**
		 * Actions used on the onboarding page
		 *
		 * @return void
		 */
		public function page_setup() {
			if ( $this->data['redirect_on_activation'] ) {
				add_action( 'after_switch_theme', array( $this, 'redirect_to_onboarding' ) );
			}

			add_action( 'admin_notices', array( $this, 'onboarding_notice' ), 99 );
			add_action( 'wp_ajax_nozama_lite_dismiss_onboarding', array( $this, 'dismiss_onboarding' ) );
			add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_admin_styles' ) );
			add_action( 'admin_menu', array( $this, 'register' ) );
			add_action( 'wp_ajax_install_nozama_lite_plugin', array( $this, 'install_plugin' ) );
		}

		/**
		 * Redirect to the onboarding page after activation
		 *
		 * @return void
		 */
		public function redirect_to_onboarding() {
			global $pagenow;
			if ( is_admin() && 'themes.php' === $pagenow && isset( $_GET['activated'] ) && current_user_can( 'manage_options' ) ) {
				wp_safe_redirect( admin_url( 'themes.php?page=' . $this->theme_slug . '-onboard' ) );
				exit;
			}
		}

		/**
		 * Add admin notice for the onboarding page
		 *
		 * @return void
		 */
		public function onboarding_notice() {

			$dismissed = get_theme_mod( 'dismissed_onboarding', false );

			if ( ! current_user_can( 'manage_options' ) || $dismissed ) {
				return;
			}

			$onboarding_page_url = get_admin_url( '', 'themes.php?page=' . $this->theme_slug . '-onboard' );

			?>
			<div class="notice notice-info is-dismissible">
				<div class="nozama-lite-onboarding-notice">
					<p>
						<?php
							/* translators: %1$s is the theme name. %2$s URL to onboarding page. */
							echo wp_kses( sprintf( __( 'Welcome to %1$s. Check out the <a href="%2$s">onboarding page</a> to get things started.', 'nozama-lite' ),
								$this->theme_name,
								esc_url( $onboarding_page_url )
							), array( 'a' => array( 'href' => array() ) ) );
						?>
					</p>
				</div>
			</div>
			<?php

			wp_enqueue_script( 'nozama-lite-onboarding-notice', get_template_directory_uri() . '/inc/onboarding/js/onboarding-notice.js', array(), $this->theme_version, true );

			$settings = array(
				'ajaxurl'       => admin_url( 'admin-ajax.php' ),
				'dismiss_nonce' => wp_create_nonce( 'nozama-lite-dismiss-onboarding' ),
			);
			wp_localize_script( 'nozama-lite-onboarding-notice', 'nozama_lite_Onboarding', $settings );
		}

		/**
		 * Handle dismissal of the admin notice
		 *
		 * @return void
		 */
		public function dismiss_onboarding() {
			check_ajax_referer( 'nozama-lite-dismiss-onboarding', 'nonce' );

			if ( current_user_can( 'manage_options' ) && ! empty( $_POST['dismissed'] ) && 'true' === $_POST['dismissed'] ) {
				set_theme_mod( 'dismissed_onboarding', true );
				wp_send_json_success( 'OK' );
			}

			wp_send_json_error( 'BAD' );
		}

		/**
		 * Enqueue onboarding page styles and scripts
		 *
		 * @return void
		 */
		public function enqueue_admin_styles() {
			if ( get_current_screen()->id !== 'appearance_page_' . $this->theme_slug . '-onboard' ) {
				return;
			}

			wp_enqueue_style( 'plugin-install' );

			wp_enqueue_style( 'nozama-lite-onboarding', get_template_directory_uri() . '/inc/onboarding/css/onboarding-styles.css', array(), $this->theme_version );

			wp_enqueue_script( 'nozama-lite-onboarding', get_template_directory_uri() . '/inc/onboarding/js/onboarding.js', array(
				'plugin-install',
				'updates',
			), $this->theme_version, true );

			wp_localize_script(
				'nozama-lite-onboarding', 'nozama_lite_onboarding', array(
					'onboarding_nonce'   => wp_create_nonce( 'onboarding_nonce' ),
					'ajaxurl'            => admin_url( 'admin-ajax.php' ),
					'template_directory' => get_template_directory_uri(),
					'activating_text'    => esc_html__( 'Activating', 'nozama-lite' ),
					'activate_text'      => esc_html__( 'Activate', 'nozama-lite' ),
					'installing_text'    => esc_html__( 'Installing...', 'nozama-lite' ),
				)
			);
		}

		/**
		 * Register the page
		 *
		 * @return void
		 */
		public function register() {
			add_theme_page( $this->page_title, $this->menu_title, 'edit_theme_options', $this->theme_slug . '-onboard', array( $this, 'render_page' ) );
		}

		/**
		 * Render the onboarding page
		 *
		 * @return void
		 */
		public function render_page() {
			$title = $this->data['title'];
			$title = str_replace(
				array( ':theme_name:', ':theme_version:' ),
				array( $this->theme_name, $this->theme_version ),
			$title );

			if ( defined( 'NOZAMA_LITE_WHITELABEL' ) && NOZAMA_LITE_WHITELABEL ) {
				$logo_src = ! empty( $this->data['logo_src'] ) ? $this->data['logo_src'] : '';
				$logo_url = ! empty( $this->data['logo_url'] ) ? $this->data['logo_url'] : '';
			} else {
				$logo_src = ! empty( $this->data['logo_src'] ) ? $this->data['logo_src'] : get_template_directory_uri() . '/inc/onboarding/assets/cssigniter_logo.svg';
				$logo_url = ! empty( $this->data['logo_url'] ) ? $this->data['logo_url'] : 'https://www.cssigniter.com/themes/' . NOZAMA_LITE_NAME . '/';
			}
			?>
			<div class="wrap about-wrap nozama-lite-onboarding-wrap full-width-layout">
				<h1><?php echo esc_html( $title ); ?></h1>

				<?php if ( ! empty( $this->data['description'] ) ) : ?>
					<p class="about-text"><?php echo wp_kses( $this->data['description'], nozama_lite_get_allowed_tags( 'guide' ) ); ?></p>
				<?php endif; ?>

				<?php if ( $this->data['logo_show'] && $logo_src ) : ?>
					<div class="wp-badge">
						<a href="<?php echo esc_url( $logo_url ); ?>" target="_blank">
							<img src="<?php echo esc_url( $logo_src ); ?>">
						</a>
					</div>
				<?php endif; ?>
				<?php
					if ( array_key_exists( 'tabs', $this->data ) && ! empty( $this->data['tabs'] ) ) {
						$this->generate_tabs();
					}
				?>
			</div>
			<?php
		}

		/**
		 * Create the navigation tabs
		 *
		 * @return void
		 */
		public function generate_tabs() {
			$active_tab = isset( $_GET['tab'] ) ? sanitize_key( wp_unslash( $_GET['tab'] ) ) : $this->data['default_tab'];
			?>
			<h2 class="nav-tab-wrapper wp-clearfix">
				<?php foreach ( $this->data['tabs'] as $tab => $title ) { ?>
					<a href="<?php echo esc_url( get_admin_url( '', 'themes.php?page=' . $this->theme_slug . '-onboard' ) . '&tab=' . $tab ); ?>" class="nav-tab <?php echo esc_attr( $active_tab === $tab ? 'nav-tab-active' : '' ); ?>" role="tab" data-toggle="tab"><?php echo esc_html( $title ); ?></a>
				<?php } ?>
			</h2>
			<div class="tab-content tab-<?php echo esc_attr( $active_tab ); ?>">
				<?php
					if ( is_callable( array( $this, $active_tab ) ) ) {
						$this->$active_tab();
					}
				?>
			</div>
			<?php
		}

		/**
		 * Populate the required plugins tab
		 *
		 * @return false|void
		 */
		public function required_plugins() {
			?><h3><?php esc_html_e( 'The following plugins are required. Please install and activate them.', 'nozama-lite' ); ?></h3><?php

			if ( ! current_user_can( 'install_plugins' ) ) :
				?><p><?php echo wp_kses( __( 'You do not have sufficient permissions to install plugins, please contact your administrator.', 'nozama-lite' ), nozama_lite_get_allowed_tags( 'guide' ) ); ?></p><?php
				return false;
			endif;

			?><div class="three-col"><?php
				$plugins = $this->data['required_plugins_page']['plugins'];

				$actions = $this->get_plugin_action( $plugins );

				$this->get_plugin_boxes( $plugins, $actions );
			?></div><?php
		}

		/**
		 * Populate the recommended plugins tab
		 *
		 * @return false|void
		 */
		public function recommended_plugins() {
			?><h3><?php
				/* translators: %s is the theme's name. */
				echo esc_html( sprintf( __( 'The following plugins are not required but they will provide additional functionality to %s.', 'nozama-lite' ), $this->theme_name ) );
			?></h3><?php

			if ( ! current_user_can( 'install_plugins' ) ) :
				?><p><?php echo wp_kses( __( 'You do not have sufficient permissions to install plugins, please contact your administrator.', 'nozama-lite' ), nozama_lite_get_allowed_tags( 'guide' ) ); ?></p><?php
				return false;
			endif;

			?><div class="three-col"><?php
				$plugins = $this->data['recommended_plugins_page']['plugins'];

				$actions = $this->get_plugin_action( $plugins );

				$this->get_plugin_boxes( $plugins, $actions );
			?></div><?php
		}

		/**
		 * Populate the sample content tab
		 *
		 * @return void
		 */
		public function sample_content() {
			$check = $this->get_plugin_action( array( 'one-click-demo-import' => __( 'One Click Demo Import', 'nozama-lite' ) ) );

			?><h3><?php esc_html_e( 'Import our sample content.', 'nozama-lite' ); ?></h3><?php

			if ( in_array( $check['one-click-demo-import'], array( 'install-plugin', 'activate' ), true ) ) {
				?>
				<div class="nozama-lite-onboarding-box nozama-lite-onboarding-box-warning">
					<h4 class="box-title"><?php esc_html_e( 'Please note:', 'nozama-lite' ); ?></h4>
					<p><?php esc_html_e( 'You need to install and activate One Click Demo Import before proceeding.', 'nozama-lite' ); ?></p>
					<p><a class="button button-primary" href="<?php echo esc_url( get_admin_url( '', 'themes.php?page=' . $this->theme_slug . '-onboard' ) . '&tab=recommended_plugins' ); ?>"><?php esc_html_e( 'Click here to do it now.', 'nozama-lite' ); ?></a></p>
				</div>
				<?php
			} else {
				?>
				<div class="nozama-lite-onboarding-box nozama-lite-onboarding-box-success">
					<h4 class="box-title"><?php esc_html_e( 'Good to go!', 'nozama-lite' ); ?></h4>
					<p><?php esc_html_e( 'Now you can import the sample content and have your theme set up like the demo using the One Click Demo Import Plugin.', 'nozama-lite' ); ?></p>
					<p><a class="button button-primary" href="<?php echo esc_url( get_admin_url( '', 'themes.php?page=pt-one-click-demo-import' ) ); ?>"><?php esc_html_e( 'Get Started', 'nozama-lite' ); ?></a></p>
				</div>
				<?php
			}

		}

		/**
		 * Populate the support tab
		 *
		 * @return void
		 */
		public function support() {
			if ( empty( $this->data['support_page']['sections'] ) ) {
				return;
			}

			?>
			<h3><?php esc_html_e( 'Here are a few useful links to get you started.', 'nozama-lite' ); ?></h3>
			<div class="three-col">
				<?php
					$sections = $this->data['support_page']['sections'];

					foreach ( $sections as $section_id => $section ) {
						?>
						<div class="col">
							<div class="nozama-lite-onboarding-box nozama-lite-support-tab-box-<?php echo esc_attr( $section_id ); ?>">
								<h4 class="box-title"><a href="<?php echo esc_url( $section['link_url'] ); ?>" target="_blank"><?php echo esc_html( $section['title'] ); ?></a></h4>
								<?php if ( ! empty( $section['description'] ) ) : ?>
									<p><?php echo wp_kses( $section['description'], nozama_lite_get_allowed_tags( 'guide' ) ); ?></p>
								<?php endif; ?>
								<p><a class="button" href="<?php echo esc_url( $section['link_url'] ); ?>" target="_blank"><?php echo esc_html( $section['title'] ); ?></a></p>
							</div>
						</div>
						<?php
					}
				?>
			</div>
			<?php
		}

		/**
		 * Layout for the plugin tabs
		 *
		 * @param array $plugins Array of the required or recommended plugins.
		 * @param array $actions Array of the plugin actions.
		 * @return void
		 */
		public function get_plugin_boxes( $plugins, $actions ) {
			foreach ( $actions as $slug => $action ) {
				if ( 'upload-plugin' === $action ) {
					$link = add_query_arg(
						array(
							'action'   => 'activate',
							'plugin'   => rawurlencode( $slug . '/' . $slug . '.php' ),
							'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $slug . '/' . $slug . '.php' ),
						), admin_url( 'plugins.php' )
					);
					?>
					<div class="col">
						<div class="nozama-lite-onboarding-box nozama-lite-onboarding-box-warning">
							<h4 class="box-title"><?php echo esc_html( $plugins[ $slug ]['title'] ); ?></h4>

							<?php if ( ! empty( $plugins[ $slug ]['description'] ) ) : ?>
								<p class="box-description"><?php echo wp_kses( $plugins[ $slug ]['description'], nozama_lite_get_allowed_tags( 'guide' ) ); ?></p>
							<?php endif; ?>

							<div class="plugin-card-<?php echo esc_attr( $slug ); ?>">
								<p>
								<?php
									/* translators: %s is the plugin name. */
									echo esc_html( sprintf( __( 'The %s plugin was not found, click to install and activate.', 'nozama-lite' ), $plugins[ $slug ]['title'] ) );
								?>
								</p>
								<p><a href="<?php echo esc_url( $link ); ?>" class="install-now button ajax-install-plugin" data-plugin-slug="<?php echo esc_attr( $slug ); ?>"><?php esc_html_e( 'Install &amp; activate ', 'nozama-lite' ); ?></a></p>
							</div>	
						</div>
					</div>	
					<?php
				} elseif ( 'install-plugin' === $action ) {
					$link = add_query_arg(
						array(
							'action'   => $action,
							'plugin'   => $slug,
							'_wpnonce' => wp_create_nonce( $action . '_' . $slug ),
						), admin_url( 'update.php' )
					);
					?>
					<div class="col">
						<div class="nozama-lite-onboarding-box nozama-lite-onboarding-box-warning">
							<h4 class="box-title"><?php echo esc_html( $plugins[ $slug ]['title'] ); ?></h4>

							<?php if ( ! empty( $plugins[ $slug ]['description'] ) ) : ?>
								<p class="box-description"><?php echo wp_kses( $plugins[ $slug ]['description'], nozama_lite_get_allowed_tags( 'guide' ) ); ?></p>
							<?php endif; ?>

							<div class="plugin-card-<?php echo esc_attr( $slug ); ?>">
								<p>
								<?php
									/* translators: %s is the plugin name. */
									echo esc_html( sprintf( __( 'The %s plugin was not found, click to install and activate.', 'nozama-lite' ), $plugins[ $slug ]['title'] ) );
								?>
								</p>
								<p><a data-slug="<?php echo esc_attr( $slug ); ?>" href="<?php echo esc_url( $link ); ?>" class="install-now button"><?php esc_html_e( 'Install &amp; activate ', 'nozama-lite' ); ?></a></p>
							</div>	
						</div>
					</div>	
					<?php
				} elseif ( 'activate' === $action ) {
					$link = add_query_arg(
						array(
							'action'   => 'activate',
							'plugin'   => rawurlencode( $slug . '/' . $slug . '.php' ),
							'_wpnonce' => wp_create_nonce( 'activate-plugin_' . $slug . '/' . $slug . '.php' ),
						), admin_url( 'plugins.php' )
					);
					?>
					<div class="col">
						<div class="nozama-lite-onboarding-box nozama-lite-onboarding-box-info">
							<h4 class="box-title"><?php echo esc_html( $plugins[ $slug ]['title'] ); ?></h4>

							<?php if ( ! empty( $plugins[ $slug ]['description'] ) ) : ?>
								<p class="box-description"><?php echo wp_kses( $plugins[ $slug ]['description'], nozama_lite_get_allowed_tags( 'guide' ) ); ?></p>
							<?php endif; ?>

							<div class="plugin-card-<?php echo esc_attr( $slug ); ?>">
								<p>
								<?php
									/* translators: %s is the plugin name. */
									echo esc_html( sprintf( __( 'The %s plugin is installed but not active, click to activate.', 'nozama-lite' ), $plugins[ $slug ]['title'] ) );
								?>
								</p>
								<p><a data-slug="<?php echo esc_attr( $slug ); ?>" href="<?php echo esc_url( $link ); ?>" class="activate-now button button-primary"><?php esc_html_e( 'Activate ', 'nozama-lite' ); ?></a></p>
							</div>	
						</div>
					</div>
					<?php
				} else {
					?>
					<div class="col">
						<div class="nozama-lite-onboarding-box nozama-lite-onboarding-box-success">
							<h4 class="box-title"><?php echo esc_html( $plugins[ $slug ]['title'] ); ?></h4>

							<?php if ( ! empty( $plugins[ $slug ]['description'] ) ) : ?>
								<p class="box-description"><?php echo wp_kses( $plugins[ $slug ]['description'], nozama_lite_get_allowed_tags( 'guide' ) ); ?></p>
							<?php endif; ?>

							<p>
							<?php
								/* translators: %s is the plugin name. */
								echo esc_html( sprintf( __( '%s is installed and activated.', 'nozama-lite' ), $plugins[ $slug ]['title'] ) );
							?>
							</p>
						</div>
					</div>	
					<?php
				}
			}
		}

		/**
		 * Check if a plugin is installed, active or absent
		 *
		 * @param array $plugins Array of the required or recommended plugins.
		 *
		 * @return array
		 */
		public function get_plugin_action( $plugins ) {

			$plugin_action = array();

			foreach ( $plugins as $slug => $data ) {
				$plugin_file      = ! empty( $data['plugin_file'] ) ? $slug . '/' . $data['plugin_file'] : $slug . '/' . $slug . '.php';
				$plugin_file_path = WP_PLUGIN_DIR . '/' . $plugin_file;

				$is_callable  = ! empty( $data['is_callable'] ) ? is_callable( $data['is_callable'] ) : false;
				$is_active    = is_plugin_active( $plugin_file );
				$is_installed = file_exists( $plugin_file_path );
				$is_bundled   = isset( $data['bundled'] ) && true === $data['bundled'] ? true : false;

				if ( $is_callable || $is_active ) {
					$plugin_action[ $slug ] = 'none';
					continue;
				}

				if ( $is_installed ) {
					$plugin_action[ $slug ] = 'activate';
				} else {
					if ( $is_bundled ) {
						$plugin_action[ $slug ] = 'upload-plugin';
					} else {
						$plugin_action[ $slug ] = 'install-plugin';
					}
				}

			}

			return $plugin_action;

		}

		/**
		 * Installs theme specific plugins
		 *
		 * @return void
		 */
		public function install_plugin() {
			if ( ! current_user_can( 'upload_plugins' ) ) {
				wp_die( esc_html__( 'You do not have sufficient permissions to install plugins on this site.', 'nozama-lite' ) );
			}

			// Verify nonce.
			if ( ! isset( $_POST['onboarding_nonce'] ) || ! wp_verify_nonce( sanitize_key( wp_unslash( $_POST['onboarding_nonce'] ) ), 'onboarding_nonce' ) ) {
				die( 'Permission denied' );
			}

			$plugin_slug = isset( $_POST['plugin_slug'] ) ? sanitize_key( wp_unslash( $_POST['plugin_slug'] ) ) : '';

			$plugin_source_url = get_template_directory_uri() . '/plugins/' . $plugin_slug . '.zip';
			include_once ABSPATH . 'wp-admin/includes/class-wp-upgrader.php';

			/* translators: %s is a URL. */
			$title    = sprintf( __( 'Installing Plugin from URL: %s', 'nozama-lite' ), esc_html( $plugin_source_url ) );
			$url      = 'update.php?action=install-plugin';
			$upgrader = new Plugin_Upgrader( new Plugin_Installer_Skin( compact( 'type', 'title', 'url', 'nonce' ) ) );
			$upgrader->install( $plugin_source_url );

		}

		public function default_data() {
			$theme = wp_get_theme();

			return array(
				// Required. Turns the onboarding page on/off.
				'show_page'                => true,
				// Optional. Turns the redirection to the onboarding page on/off.
				'redirect_on_activation'   => true,
				// Optional. The text to be used for the admin menu. If empty, defaults to "About theme_name"
				/* translators: %s is the theme name. */
				'menu_title'               => sprintf( __( 'About %s', 'nozama-lite' ), $theme->get( 'Name' ) ),
				// Optional. The text to be displayed in the page's title tag. If empty, defaults to "About theme_name"
				/* translators: %s is the theme name. */
				'page_title'               => sprintf( __( 'About %s', 'nozama-lite' ), $theme->get( 'Name' ) ),
				// Optional. The onboarding page's title, placeholders available :theme_name:, :theme_version:. If empty, defaults to "Welcome to :theme_name:! - Version :theme_version:"
				'title'                    => __( 'Welcome to :theme_name:! - Version :theme_version:', 'nozama-lite' ),
				// Optional. The theme's description. Some HTML is allowed (no p).
				'description'              => '',
				// Optional. Boolean. Whether to show the logo. Default: true in normal mode. false if whitelabel.
				'logo_show'                => defined( 'NOZAMA_LITE_WHITELABEL' ) && NOZAMA_LITE_WHITELABEL ? false : true,
				// Optional. The logo's image source URL. Defaults to the bundled logo.
				'logo_src'                 => get_template_directory_uri() . '/inc/onboarding/assets/cssigniter_logo.svg',
				// Required. The logo's link URL. Defaults to 'https://www.cssigniter.com/themes/' . NOZAMA_LITE_NAME . '/'
				'logo_url'                 => 'https://www.cssigniter.com/themes/' . NOZAMA_LITE_NAME . '/',
				// Optional. The default active tab. Default 'required_plugins'. Must be one of the keys in the tabs[] array.
				'default_tab'              => 'required_plugins',
				// Optional. slug => label pairs for each tab. Default are as follows:
				'tabs'                     => array(
					'required_plugins'    => __( 'Required Plugins', 'nozama-lite' ),
					'recommended_plugins' => __( 'Recommended Plugins', 'nozama-lite' ),
					'sample_content'      => __( 'Sample Content', 'nozama-lite' ),
					'support'             => __( 'Support', 'nozama-lite' ),
				),
				'required_plugins_page'    => array(
					'plugins' => array(
//						// Each plugin is registered as 'slug' => array()
//						'plugin-slug' => array(
//							// Required. The plugin's title.
//							'title'       => __( 'Plugin Title', 'nozama-lite' ),
//							// Optional. The plugin's description, or why the plugin is required.
//							'description' => '',
//							// Optional. If true, the plugin zip will be searched in the theme's plugins/ directory, named "plugin-slug.zip". Default false.
//							'bundled'     => false,
//							// Optional. If passed string or array is callable, then the plugin will appear as activated.
//							'is_callable' => '',
//						),
					),
				),
				'recommended_plugins_page' => array(
					'plugins' => array(
//						// Each plugin is registered as 'slug' => array()
//						'plugin-slug' => array(
//							// Required. The plugin's title.
//							'title'       => __( 'Plugin Title', 'nozama-lite' ),
//							// Optional. The plugin's description, or why the plugin is required.
//							'description' => '',
//							// Optional. If true, the plugin zip will be searched in the theme's plugins/ directory, named "plugin-slug.zip". Default false.
//							'bundled'     => false,
//							// Optional. If passed string or array is callable, then the plugin will appear as activated.
//							'is_callable' => '',
//						),
					),
				),
				'support_page'             => array(
					'sections' => array(
						'documentation' => array(
							'title'       => __( 'Theme Documentation', 'nozama-lite' ),
							'description' => __( "If you don't want to import our demo sample content, just visit this page and learn how to set things up individually.", 'nozama-lite' ),
							'link_url'    => 'https://www.cssigniter.com/docs/' . NOZAMA_LITE_NAME . '/',
						),
						'kb'            => array(
							'title'       => __( 'Knowledge Base', 'nozama-lite' ),
							'description' => __( 'Browse our library of step by step how-to articles, tutorials, and guides to get quick answers.', 'nozama-lite' ),
							'link_url'    => 'https://www.cssigniter.com/docs/knowledgebase/',
						),
						'support'       => array(
							'title'       => __( 'Request Support', 'nozama-lite' ),
							'description' => __( 'Got stuck? No worries, just visit our support page, submit your ticket and we will be there for you within 24 hours.', 'nozama-lite' ),
							'link_url'    => 'https://www.cssigniter.com/support/',
						),
					),
				),

			);

		}

	} //Nozama_Lite_Onboarding_Page
}
