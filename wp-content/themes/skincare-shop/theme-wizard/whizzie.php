<?php

/**
 * Wizard
 *
 * @package Whizzie
 * @author Catapult Themes
 * @since 1.0.0
 */

class Whizzie {

	protected $version = '1.1.0';

	/** @var string Current theme name, used as namespace in actions. */
	protected $theme_name = '';
	protected $theme_title = '';

	protected $plugin_path = '';
	protected $parent_slug = '';

	/** @var string Wizard page slug and title. */
	protected $page_slug = '';
	protected $page_title = '';

	/** @var array Wizard steps set by user. */
	protected $config_steps = array();

	/**
	 * Relative plugin url for this plugin folder
	 * @since 1.0.0
	 * @var string
	 */
	protected $plugin_url = '';

	/**
	 * TGMPA instance storage
	 *
	 * @var object
	 */
	protected $tgmpa_instance;

	/**
	 * TGMPA Menu slug
	 *
	 * @var string
	 */
	protected $tgmpa_menu_slug = 'tgmpa-install-plugins';

	/**
	 * TGMPA Menu url
	 *
	 * @var string
	 */
	protected $tgmpa_url = 'themes.php?page=tgmpa-install-plugins';

	// Where to find the widget.wie file
	protected $widget_file_url = '';

	/**
	 * Constructor
	 *
	 * @param $config	Our config parameters
	 */
	public function __construct( $config ) {
		$this->set_vars( $config );
		$this->init();
	}

	/**
	 * Set some settings
	 * @since 1.0.0
	 * @param $config	Our config parameters
	 */
	public function set_vars( $config ) {

		// require_once trailingslashit( WHIZZIE_DIR ) . 'tgm/class-tgm-plugin-activation.php';
		require_once trailingslashit( WHIZZIE_DIR ) . 'tgm/tgm.php';
		// require_once trailingslashit( WHIZZIE_DIR ) . 'widgets/class-ti-widget-importer.php';

		if( isset( $config['page_slug'] ) ) {
			$this->page_slug = esc_attr( $config['page_slug'] );
		}
		if( isset( $config['page_title'] ) ) {
			$this->page_title = esc_attr( $config['page_title'] );
		}
		if( isset( $config['steps'] ) ) {
			$this->config_steps = $config['steps'];
		}

		$this->plugin_path = trailingslashit( dirname( __FILE__ ) );
		$relative_url = str_replace( get_template_directory(), '', $this->plugin_path );
		$this->plugin_url = trailingslashit( get_template_directory_uri() . $relative_url );
		$current_theme = wp_get_theme();
		$this->theme_title = $current_theme->get( 'Name' );
		$this->theme_name = strtolower( preg_replace( '#[^a-zA-Z]#', '', $current_theme->get( 'Name' ) ) );
		$this->page_slug = apply_filters( $this->theme_name . '_theme_setup_wizard_page_slug', $this->theme_name . '-wizard' );
		$this->parent_slug = apply_filters( $this->theme_name . '_theme_setup_wizard_parent_slug', '' );

	}

	/*
	 * Hooks and filters
	 * @since 1.0.0
	 */
	public function init() {

		// add_action( 'after_switch_theme', array( $this, 'redirect_to_wizard' ) );
		if ( class_exists( 'TGM_Plugin_Activation' ) && isset( $GLOBALS['tgmpa'] ) ) {
			add_action( 'init', array( $this, 'get_tgmpa_instance' ), 30 );
			add_action( 'init', array( $this, 'set_tgmpa_url' ), 40 );
		}
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'admin_menu', array( $this, 'menu_page' ) );
		add_action( 'admin_init', array( $this, 'get_plugins' ), 30 );
		add_filter( 'tgmpa_load', array( $this, 'tgmpa_load' ), 10, 1 );
		add_action( 'wp_ajax_setup_plugins', array( $this, 'setup_plugins' ) );
		add_action( 'wp_ajax_setup_widgets', array( $this, 'setup_widgets' ) );

	}

	public function enqueue_scripts($hook) {

		wp_enqueue_style( 'theme-wizard-style', get_template_directory_uri() . '/theme-wizard/assets/css/theme-wizard-style.css');

		wp_register_script( 'theme-wizard-script', get_template_directory_uri() . '/theme-wizard/assets/js/theme-wizard-script.js', array( 'jquery' ), time() );
		wp_localize_script(
			'theme-wizard-script',
			'skincare_shop_whizzie_params',
			array(
				'ajaxurl' 		=> admin_url( 'admin-ajax.php' ),
				'wpnonce' 		=> wp_create_nonce( 'whizzie_nonce' ),
				'verify_text'	=> esc_html( 'verifying', 'skincare-shop' )
			)
		);
		wp_enqueue_script( 'theme-wizard-script' );

	}

	public static function get_instance() {
		if ( ! self::$instance ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	public function tgmpa_load( $status ) {
		return is_admin() || current_user_can( 'install_themes' );
	}

	/**
	 * Get configured TGMPA instance
	 *
	 * @access public
	 * @since 1.1.2
	 */
	public function get_tgmpa_instance() {
		$this->tgmpa_instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
	}

	/**
	 * Update $tgmpa_menu_slug and $tgmpa_parent_slug from TGMPA instance
	 *
	 * @access public
	 * @since 1.1.2
	 */
	public function set_tgmpa_url() {
		$this->tgmpa_menu_slug = ( property_exists( $this->tgmpa_instance, 'menu' ) ) ? $this->tgmpa_instance->menu : $this->tgmpa_menu_slug;
		$this->tgmpa_menu_slug = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_menu_slug', $this->tgmpa_menu_slug );
		$tgmpa_parent_slug = ( property_exists( $this->tgmpa_instance, 'parent_slug' ) && $this->tgmpa_instance->parent_slug !== 'themes.php' ) ? 'admin.php' : 'themes.php';
		$this->tgmpa_url = apply_filters( $this->theme_name . '_theme_setup_wizard_tgmpa_url', $tgmpa_parent_slug . '?page=' . $this->tgmpa_menu_slug );
	}

	/**
	 * Make a modal screen for the wizard
	 */
	public function menu_page() {
		add_theme_page( esc_html( $this->page_title ), esc_html( $this->page_title ), 'manage_options', $this->page_slug, array( $this, 'skincare_shop_setup_wizard' ) );
	}

	/**
	 * Make an interface for the wizard
	 */
	public function wizard_page() {
		tgmpa_load_bulk_installer();
		// install plugins with TGM.
		if ( ! class_exists( 'TGM_Plugin_Activation' ) || ! isset( $GLOBALS['tgmpa'] ) ) {
			die( 'Failed to find TGM' );
		}
		$url = wp_nonce_url( add_query_arg( array( 'plugins' => 'go' ) ), 'whizzie-setup' );

		// copied from TGM
		$method = ''; // Leave blank so WP_Filesystem can populate it as necessary.
		$fields = array_keys( $_POST ); // Extra fields to pass to WP_Filesystem.
		if ( false === ( $creds = request_filesystem_credentials( esc_url_raw( $url ), $method, false, false, $fields ) ) ) {
			return true; // Stop the normal page form from displaying, credential request form will be shown.
		}
		// Now we have some credentials, setup WP_Filesystem.
		if ( ! WP_Filesystem( $creds ) ) {
			// Our credentials were no good, ask the user for them again.
			request_filesystem_credentials( esc_url_raw( $url ), $method, true, false, $fields );
			return true;
		}
		/* If we arrive here, we have the filesystem */ ?>
		<div class="main-wrap">

			<?php if ( ! skincare_shop_is_whizzie_dismissed() ) : ?>
				<div class="homepage-setup whizzie-notice skincare-whizzie-notice" data-notice="whizzie">
					<button class="whizzie-dismiss" aria-label="<?php esc_attr_e( 'Dismiss', 'skincare-shop' ); ?>">×</button>

					<div class="homepage-setup-theme-bundle">
						<div class="homepage-setup-theme-bundle-one">
							<h1><?php echo wp_kses_post( 'WP Theme Bundle - Get All Themes For Just <span class="price">$79</span>' ); ?></h1>
							<p><?php esc_html_e( 'Get our all 60+ premium themes now and Transform your website with our Ultimate WordPress Theme Bundle.', 'skincare-shop' ); ?></p>
						</div>

						<div class="homepage-setup-theme-bundle-two">
							<p><?php echo wp_kses_post( '<del>$2440</del> $79' ); ?></p>
							<a href="<?php echo esc_url( SKINCARE_SHOP_BUNDLE_URL ); ?>" target="_blank">
								<p class="buy-themes"><?php esc_html_e( 'BUY ALL THEMES FOR $79', 'skincare-shop' ); ?></p>
							</a>
						</div>

						<div class="homepage-setup-theme-bundle-three">
							<div class="extra-btn"> <p><?php echo wp_kses_post( 'Extra<div>30% OFF</div>', 'skincare-shop' ); ?></p> </div>
							<img src="<?php echo esc_url( get_template_directory_uri() . '/images/notice.png' ); ?>" alt="">
						</div>
					</div>
				</div>
			<?php endif; ?>

			<?php
			echo '<div class="card whizzie-wrap">';
				// The wizard is a list with only one item visible at a time
				$steps = $this->get_steps();
				echo '<ul class="whizzie-menu">';
				foreach( $steps as $step ) {
					$class = 'step step-' . esc_attr( $step['id'] );
					echo '<li data-step="' . esc_attr( $step['id'] ) . '" class="' . esc_attr( $class ) . '">';
						printf( '<h2>%s</h2>', esc_html( $step['title'] ) );
						// $content is split into summary and detail
						$content = call_user_func( array( $this, $step['view'] ) );
						if( isset( $content['summary'] ) ) {
							printf(
								'<div class="summary">%s</div>',
								wp_kses_post( $content['summary'] )
							);
						}
						if( isset( $content['detail'] ) ) {
							// Add a link to see more detail
							printf( '<p><a href="#" class="more-info">%s</a></p>', __( 'More Info', 'skincare-shop' ) );
							printf(
								'<div class="detail">%s</div>',
								$content['detail'] // Need to escape this
							);
						}
						// The next button

						$skincare_shop_import_done = get_option( 'skincare_shop_demo_import_done' );

						if ( isset( $step['button_text'] ) && $step['button_text'] ) {

							// INTRO STEP + DEMO ALREADY IMPORTED → VIEW SITE ONLY
							if ( $skincare_shop_import_done && $step['id'] === 'intro' ) {

								echo '<div class="button-wrap">
										<a href="' . esc_url( home_url() ) . '" 
										class="button button-primary" 
										target="_blank">
										' . esc_html( $step['button_text'] ) . '
										</a>
									</div>';

							} else {

								// NORMAL WIZARD FLOW
								printf(
									'<div class="button-wrap">
										<a href="#" 
										class="button button-primary do-it" 
										data-callback="%s" 
										data-step="%s">%s</a>
									</div>',
									esc_attr( $step['callback'] ),
									esc_attr( $step['id'] ),
									esc_html( $step['button_text'] )
								);
							}
						}

						// The skip button
						if( isset( $step['can_skip'] ) && $step['can_skip'] ) {
							printf(
								'<div class="button-wrap" style="margin-left: 0.5em;"><a href="#" class="button button-secondary do-it" data-callback="%s" data-step="%s">%s</a></div>',
								'do_next_step',
								esc_attr( $step['id'] ),
								__( 'Skip', 'skincare-shop' )
							);
						}

					echo '</li>';
				}
				echo '</ul>';
				echo '<ul class="whizzie-nav">';
					foreach( $steps as $step ) {
						if( isset( $step['icon'] ) && $step['icon'] ) {
							echo '<li class="nav-step-' . esc_attr( $step['id'] ) . '">';
							if (isset($step['icon1'])) {
								require_once $step['icon1'];
							} else {
								echo '<span class="dashicons dashicons-' . esc_attr( $step['icon'] ) . '"></span>';
							}
							echo '</li>';
						}
					}
				echo '</ul>';
				?>
				<div class="step-loading">
					<span class="spinner">
						<svg version="1.2" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 562 656" width="50" height="50"><style>.a{fill:#fff;stroke:#fff;stroke-linejoin:round}</style><path fill-rule="evenodd" class="a" d="m302.5 0c6.4 0.1 8.9 3.3 13 5.5 3.2 1.7 6.9 2.1 10 4 4.5 2.7 9.1 6.2 13.5 9 6.2 3.9 12.8 6.2 19 10 13.6 8.5 26.6 18.5 39.5 28 8.6 6.3 16.8 14 25 21 2.2 1.8 3.2 5.3 5.5 7 18.7 14.2 36.6 36.2 50.5 55.5 7.5 10.4 16.6 19.9 23.5 31 17.3 27.8 31.6 59.7 43 93 1.2 3.7 0.9 6.3 2 10 3 9.9 5.4 21.4 8 31.5q0.5 6.5 1 13c1.8 7.2 2.1 17.6 4 25v12c3.3 14.2 2.1 33.7-1 47v14.5c-4.3 17.5-6.8 38.2-13 54.5-23.5 61.9-63.3 110.2-116 143-19.1 11.9-41.5 20.7-64 29-7.7 2.9-16.3 3.9-24.5 6-3.6 0.9-5.9 0.1-9.5 1q-3.4 0.9-6.8 1.6-3.4 0.7-6.8 1.2-3.5 0.5-7 0.8-3.4 0.3-6.9 0.4c-18.3 0.2-41.6 2.7-58-1l-14.5-1c-17.5-4.3-34.5-7.5-50.5-13-7.9-2.7-15.4-3.5-22.5-7-15.2-7.5-30.4-14.8-44-24-40.3-27-71.6-66.7-92-113.5-8.9-20.5-12.8-45.6-19-68.5-2.4-9 0.5-20.1-2-29.5-1.2-4.4-1-11.6-1-17.5 0-18.5 1.4-34.4 6-48 2.9-8.5 2.9-17.3 6-25.5 6.5-17.2 15.7-37.5 25-52.5 4.7-7.5 10.9-14.4 16-21.5 8.9-12.3 16.9-24.9 29-34l12-12.5c7.5-5.5 14.7-10.8 22.5-16 6-4 8.7-9 19-9 2.3 5.2 2 12.9 3.5 19.5v7.5c6.3 25.1 12.2 62.7 29 76.5 3-0.6 5.3-2.5 8-3.5 4.6-1.7 8.8-1.9 13-4 7.3-3.6 14.8-7.9 21.5-12 38.2-23.4 63.6-53.4 80.5-98 4.3-11.3 6.1-25 9-37q0.5-6.8 1-13.5c2.5-10.7 4.1-36.1 1.5-47.5q-0.3-9-0.5-18c-2.1-8.5-3-17.8-3-28.5q0.3-0.2 0.7-0.4 0.3-0.3 0.6-0.5 0.3-0.3 0.6-0.5 0.3-0.3 0.6-0.6z"></path></svg>
					</span>
				</div>
			</div><!-- .whizzie-wrap -->

		</div><!-- .wrap -->
	<?php }

	public function wz_activate_skincare_shop() {

		if ( is_wp_error( $response ) ) {
			$response = array('status' => false, 'msg' => 'Something Went Wrong!');
			wp_send_json($response);
			exit;
		} else {
			$response_body = wp_remote_retrieve_body( $response );
			$response_body = json_decode($response_body);

			if ( $response_body->is_suspended == 1 ) {
			} else {
			}

			if ($response_body->status === false) {
				$response = array('status' => false, 'msg' => $response_body->msg);
				wp_send_json($response);
				exit;
			} else {
				$response = array('status' => true, 'msg' => 'Theme Activated Successfully!');
				wp_send_json($response);
				exit;
			}
		}
	}

	public function skincare_shop_setup_wizard() {
		?>
			<div class="wrapper-info get-stared-page-wrap">
				<div id="demo_offer">
					<?php $this->wizard_page(); ?>
				</div>
			</div>
		<?php
	}

	/**
	 * Set options for the steps
	 * Incorporate any options set by the theme dev
	 * Return the array for the steps
	 * @return Array
	 */
	public function get_steps() {
		$skincare_shop_import_done = get_option( 'skincare_shop_demo_import_done' );
		$skincare_shop_button_text = $skincare_shop_import_done
			? __( 'View Site', 'skincare-shop' )
			: get_theme_mod(
				'skincare_shop_start_button_text',
				__( 'Start Now', 'skincare-shop' )
			);
		$dev_steps = $this->config_steps;
		$steps = array(
			'intro' => array(
				'id'    => 'intro',
				'title' => __( 'Welcome to ', 'skincare-shop' ) . $this->theme_title,
				'icon'  => 'dashboard',
				'icon1' => get_template_directory() . '/theme-wizard/assets/images/svg/Icon-01.svg',
				'view'  => 'get_step_intro',
				'callback' => $skincare_shop_import_done ? '' : 'do_next_step',
				'button_text' => $skincare_shop_button_text,
				'can_skip' => false
			),
			'plugins' => array(
				'id'			=> 'plugins',
				'title'			=> __( 'Plugins', 'skincare-shop' ),
				'icon'			=> 'admin-plugins',
				'icon1'			=>	get_template_directory() . '/theme-wizard/assets/images/svg/Icon-02.svg',
				'view'			=> 'get_step_plugins',
				'callback'		=> 'install_plugins',
				'button_text'	=> __( 'Install Plugins', 'skincare-shop' ),
				'can_skip'		=> true
			),
			'widgets' => array(
				'id'    => 'widgets',
				'title' => __( 'Demo Importer', 'skincare-shop' ),
				'icon'  => 'welcome-widgets-menus',
				'icon1' => get_template_directory() . '/theme-wizard/assets/images/svg/Icon-03.svg',
				'view'  => 'get_step_widgets',

				'callback' => $skincare_shop_import_done ? '' : 'install_widgets',

				'button_text' => $skincare_shop_import_done
					? __( 'Demo Imported', 'skincare-shop' )
					: __( 'Import Demo', 'skincare-shop' ),

				'can_skip' => true
			),
			'done' => array(
				'id'			=> 'done',
				'title'			=> __( 'All Done', 'skincare-shop' ),
				'icon'			=> 'yes',
				'icon1'			=>	get_template_directory() . '/theme-wizard/assets/images/svg/Icon-04.svg',
				'view'			=> 'get_step_done',
				'callback'		=> ''
			)
		);

		// Iterate through each step and replace with dev config values
		if( $dev_steps ) {
			// Configurable elements - these are the only ones the dev can update from config.php
			$can_config = array( 'title', 'icon', 'button_text', 'can_skip' );
			foreach( $dev_steps as $dev_step ) {
				// We can only proceed if an ID exists and matches one of our IDs
				if( isset( $dev_step['id'] ) ) {
					$id = $dev_step['id'];
					if( isset( $steps[$id] ) ) {
						foreach( $can_config as $element ) {
							if( isset( $dev_step[$element] ) ) {
								$steps[$id][$element] = $dev_step[$element];
							}
						}
					}
				}
			}
		}
		return $steps;
	}

	/**
	 * Print the content for the intro step
	 */
		public function get_step_intro() { ?>
			<div class="summary">
				<p>
					<?php
					printf(
						/* translators: %s: Theme name */
						esc_html__('Thank you for choosing this %s Theme. Using this quick setup wizard, you will be able to configure your new website and get it running in just a few minutes. Just follow these simple steps mentioned in the wizard and get started with your website.', 'skincare-shop'),
						$this->theme_title
					);
					?>
				</p>
				<p>
					<?php esc_html_e('You may even skip the steps and get back to the dashboard if you have no time at the present moment. You can come back any time if you change your mind.','skincare-shop'); ?>
				</p>
			</div>
		<?php }

	/**
	 * Get the content for the plugins step
	 * @return $content Array
	 */
	public function get_step_plugins() {
		$plugins = $this->get_plugins();
		$content = array(); ?>
			<div class="summary">
				<p>
					<?php esc_html_e('Additional plugins always make your website exceptional. Install these plugins by clicking the install button. You may also deactivate them from the dashboard.','skincare-shop') ?>
				</p>
			</div>
		<?php // The detail element is initially hidden from the user
		$content['detail'] = '<ul class="whizzie-do-plugins">';

		$plugins['all'] = $this->moveArrayPosition($plugins['all'], 'woocommerce', 0);
		// Add each plugin into a list
		foreach( $plugins['all'] as $slug=>$plugin ) {
			$content['detail'] .= '<li data-slug="' . esc_attr( $slug ) . '">' . esc_html( $plugin['name'] ) . '<span>';
			$keys = array();
			if ( isset( $plugins['install'][ $slug ] ) ) {
			    $keys[] = 'Installation';
			}
			if ( isset( $plugins['update'][ $slug ] ) ) {
			    $keys[] = 'Update';
			}
			if ( isset( $plugins['activate'][ $slug ] ) ) {
			    $keys[] = 'Activation';
			}
			$content['detail'] .= implode( ' and ', $keys ) . ' required';
			$content['detail'] .= '</span></li>';
		}
		$content['detail'] .= '</ul>';

		return $content;
	}

	function moveArrayPosition(&$array, $key, $new_position) {
	    if (!array_key_exists($key, $array)) {
	        return $array;
	    }
	    $item = $array[$key];
	    unset($array[$key]);
	    $result = [];
	    $position_added = false;

	    foreach ($array as $current_key => $current_value) {
	        if (!$position_added && $new_position === count($result)) {
	            $result[$key] = $item;
	            $position_added = true;
	        }
	        $result[$current_key] = $current_value;
	    }
	    if (!$position_added) {
	        $result[$key] = $item;
	    }
	    $array = $result;
	    return $array;
	}

	/**
	 * Print the content for the widgets step
	 * @since 1.1.0
	 */
	public function get_step_widgets() { ?>
		<div class="summary">
			<p>
				<?php esc_html_e('This theme supports importing the demo content and adding widgets. Get them installed with the below button. Using the Customizer, it is possible to update or even deactivate them','skincare-shop'); ?>
			</p>
		</div>
	<?php }

	/**
	 * Print the content for the final step
	 */
	public function get_step_done() { ?>
		<div id="ti-demo-setup-guid">
			<div class="ti-setup-menu">
				<h3><?php esc_html_e('Setup Navigation Menu','skincare-shop'); ?></h3>
				<p><?php esc_html_e('This theme supports importing the demo content and adding widgets. Get them installed with the below button. Using the Customizer, it is possible to update or even deactivate them','skincare-shop'); ?></p>
				<h4><?php esc_html_e('A) Create Pages','skincare-shop'); ?></h4>
				<ol>
					<li><?php esc_html_e('Go to Dashboard >> Pages >> Add New','skincare-shop'); ?></li>
					<li><?php esc_html_e('Enter Page Details And Save Changes','skincare-shop'); ?></li>
				</ol>
				<h4><?php esc_html_e('B) Add Pages To Menu','skincare-shop'); ?></h4>
				<ol>
					<li><?php esc_html_e('Go to Dashboard >> Appearance >> Menu','skincare-shop'); ?></li>
					<li><?php esc_html_e('Click On The Create Menu Option','skincare-shop'); ?></li>
					<li><?php esc_html_e('Select The Pages And Click On The Add to Menu Button','skincare-shop'); ?></li>
					<li><?php esc_html_e('Select Primary Menu From The Menu Setting','skincare-shop'); ?></li>
					<li><?php esc_html_e('Click On The Save Menu Button','skincare-shop'); ?></li>
				</ol>
			</div>
			<div class="ti-setup-widget">
				<h3><?php esc_html_e('Setup Footer Widgets','skincare-shop'); ?></h3>
				<ol>
					<li><?php esc_html_e('Go to Dashboard >> Appearance >> Widgets','skincare-shop'); ?></li>
					<li><?php esc_html_e('Drag And Add The Widgets In The Footer Columns','skincare-shop'); ?></li>
				</ol>
			</div>
			<div class="ti-setup-dots">
				<button type="button" id="ti-prev" class="nav-btn prev"><?php esc_html_e('Previous','skincare-shop'); ?></button>
				
				<input type="radio" name="r1" id="ti-setup-menu" checked hidden>
				<input type="radio" name="r1" id="ti-setup-widget" hidden>

				<button type="button" id="ti-next" class="nav-btn next"><?php esc_html_e('Next','skincare-shop'); ?></button>
			</div>
			<!-- <div class="ti-setup-finish">
				<a href="
				<?php
				// echo esc_url(admin_url());
				?>
				" class="button button-primary">Finish</a>
			</div> -->
			<div style="display:flex; justify-content:center; flex-wrap: wrap;">
			<div class="ti-setup-finish">
				<a target="_blank" href="<?php echo esc_url(home_url()); ?>" class="button button-primary">	
					<?php esc_html_e('Visit Site','skincare-shop'); ?>
				</a>
			</div>
			<div class="ti-setup-finish">
				<a target="_blank" href="<?php echo esc_url( admin_url('customize.php') ); ?>" class="button button-primary">					
					<?php esc_html_e('Customize Your Demo','skincare-shop'); ?>
				</a>
			</div>
			<div class="ti-setup-finish">
				<a target="_blank" href="<?php echo esc_url( admin_url('themes.php?page=skincare-shop') ); ?>" class="button button-primary"><?php esc_html_e('Dashboard','skincare-shop'); ?></a>
			</div>
		</div>
		</div>

	<?php }

	/**
	 * Get the plugins registered with TGMPA
	 */
	public function get_plugins() {
		$instance = call_user_func( array( get_class( $GLOBALS['tgmpa'] ), 'get_instance' ) );
		$plugins = array(
			'all' 		=> array(),
			'install'	=> array(),
			'update'	=> array(),
			'activate'	=> array()
		);
		foreach( $instance->plugins as $slug=>$plugin ) {
			if( $instance->is_plugin_active( $slug ) && false === $instance->does_plugin_have_update( $slug ) ) {
				// Plugin is installed and up to date
				continue;
			} else {
				$plugins['all'][$slug] = $plugin;
				if( ! $instance->is_plugin_installed( $slug ) ) {
					$plugins['install'][$slug] = $plugin;
				} else {
					if( false !== $instance->does_plugin_have_update( $slug ) ) {
						$plugins['update'][$slug] = $plugin;
					}
					if( $instance->can_plugin_activate( $slug ) ) {
						$plugins['activate'][$slug] = $plugin;
					}
				}
			}
		}
		return $plugins;
	}

	/**
	 * Get the widgets.wie file from the /content folder
	 * @return Mixed	Either the file or false
	 * @since 1.1.0
	 */

	public function setup_plugins() {
		if ( ! check_ajax_referer( 'whizzie_nonce', 'wpnonce' ) || empty( $_POST['slug'] ) ) {
			wp_send_json_error( array( 'error' => 1, 'message' => esc_html__( 'No Slug Found','skincare-shop' ) ) );
		}
		$json = array();
		// send back some json we use to hit up TGM
		$plugins = $this->get_plugins();

		// what are we doing with this plugin?
		foreach ( $plugins['activate'] as $slug => $plugin ) {
			if ( $_POST['slug'] == $slug ) {
				$json = array(
					'url'           => admin_url( $this->tgmpa_url ),
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa_menu_slug,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-activate',
					'action2'       => - 1,
					'message'       => esc_html__( 'Activating Plugin','skincare-shop' ),
				);
				break;
			}
		}
		foreach ( $plugins['update'] as $slug => $plugin ) {
			if ( $_POST['slug'] == $slug ) {
				$json = array(
					'url'           => admin_url( $this->tgmpa_url ),
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa_menu_slug,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-update',
					'action2'       => - 1,
					'message'       => esc_html__( 'Updating Plugin','skincare-shop' ),
				);
				break;
			}
		}
		foreach ( $plugins['install'] as $slug => $plugin ) {
			if ( $_POST['slug'] == $slug ) {
				$json = array(
					'url'           => admin_url( $this->tgmpa_url ),
					'plugin'        => array( $slug ),
					'tgmpa-page'    => $this->tgmpa_menu_slug,
					'plugin_status' => 'all',
					'_wpnonce'      => wp_create_nonce( 'bulk-plugins' ),
					'action'        => 'tgmpa-bulk-install',
					'action2'       => - 1,
					'message'       => esc_html__( 'Installing Plugin','skincare-shop' ),
				);
				break;
			}
		}
		if ( $json ) {
			$json['hash'] = md5( serialize( $json ) ); // used for checking if duplicates happen, move to next plugin
			wp_send_json( $json );
		} else {
			wp_send_json( array( 'done' => 1, 'message' => esc_html__( 'Success','skincare-shop' ) ) );
		}
		exit;
	}

	public static function get_page_id_by_title($pagename){

		$args = array(
			'post_type' => 'page',
			'posts_per_page' => 1,
			'post_status' => 'publish',
			'title' => $pagename
		);
		$query = new WP_Query( $args );
		
		$page_id = '1';
		if (isset($query->post->ID)) {
			$page_id = $query->post->ID;
		}
		
		return $page_id;
	}

	public function create_theme_nav_menu(){

		// ------- Create Nav Menu --------
	   $menuname = 'Primary Menu';
	   $bpmenulocation = 'primary';
	   $menu_exists = wp_get_nav_menu_object( $menuname );

			if( !$menu_exists){
			$menu_id = wp_create_nav_menu($menuname);
			wp_update_nav_menu_item($menu_id, 0, array(
				'menu-item-title' =>  __('Home','skincare-shop'),
				'menu-item-classes' => 'home-page',
				'menu-item-url' => home_url( '/' ),
				'menu-item-status' => 'publish'));

				wp_update_nav_menu_item($menu_id, 0, array(
					'menu-item-title' => __('About Us','skincare-shop'),
					'menu-item-classes' => 'about',
					'menu-item-url' => get_permalink(Whizzie::get_page_id_by_title('About Us')),
					'menu-item-status' => 'publish',
				));

				wp_update_nav_menu_item($menu_id, 0, array(
					'menu-item-title' => __('Services','skincare-shop'),
					'menu-item-classes' => 'services',
					'menu-item-url' => get_permalink(Whizzie::get_page_id_by_title('Services')),
					'menu-item-status' => 'publish',
				));

				wp_update_nav_menu_item($menu_id, 0, array(
					'menu-item-title' => __('Pages','skincare-shop'),
					'menu-item-classes' => 'pages',
					'menu-item-url' => get_permalink(Whizzie::get_page_id_by_title('Pages')),
					'menu-item-status' => 'publish',
				));

				wp_update_nav_menu_item($menu_id, 0, array(
					'menu-item-title' => __('Blogs','skincare-shop'),
					'menu-item-classes' => 'blogs',
					'menu-item-url' => get_permalink(Whizzie::get_page_id_by_title('Blogs')),
					'menu-item-status' => 'publish',
				));

				wp_update_nav_menu_item($menu_id, 0, array(
					'menu-item-title' => __('Contact Us','skincare-shop'),
					'menu-item-classes' => 'contact',
					'menu-item-url' => get_permalink(Whizzie::get_page_id_by_title('Contact Us')),
					'menu-item-status' => 'publish',
				));

			if( !has_nav_menu( $bpmenulocation ) ){
				$locations = get_theme_mod('nav_menu_locations');
				$locations[$bpmenulocation] = $menu_id;
				set_theme_mod( 'nav_menu_locations', $locations );
			}
			}
   		}

	public function setup_widgets() {

		$skincare_shop_home_content = '';
		// Create a front page and assigned the template
		$home_title = 'Home';
		$home_check = get_page_by_title($home_title);
		$home = array(
		   'post_type' => 'page',
		   'post_title' => $home_title,
		   'post_content' => $skincare_shop_home_content,
		   'post_status' => 'publish',
		   'post_author' => 1,
		   'post_slug' => 'home'
		);
		$home_id = wp_insert_post($home);
		//Set the home page template
		add_post_meta( $home_id, '_wp_page_template', '/template-home.php' );

		//Set the static front page
		update_option( 'page_on_front', $home_id );
		update_option( 'show_on_front', 'page' );

		// Create a terms page and assign the template (Blogs page)
		$terms_title = 'About Us';
		$terms_check = get_page_by_title($terms_title);
		$terms = array(
			'post_type'    => 'page',
			'post_title'   => $terms_title,
			'post_status'  => 'publish',
			'post_author'  => 1,
			'post_slug'    => 'about',
			'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
		);
		$terms_id = wp_insert_post($terms);

		// Create a terms page and assigned the template
		$terms_title = 'Services';
		$terms_check = get_page_by_title($terms_title);
		$terms = array(
		'post_type' => 'page',
		'post_title' => $terms_title,
		'post_status' => 'publish',
		'post_author' => 1,
		'post_slug' => 'services',
		'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
		);
		$terms_id = wp_insert_post($terms);

	   //Set the blog with right sidebar template
	   add_post_meta( $about_id, '_wp_page_template', 'page-template/about.php' );

	   	// Create a terms page and assigned the template
		$terms_title = 'Pages';
		$terms_check = get_page_by_title($terms_title);
		$terms = array(
		'post_type' => 'page',
		'post_title' => $terms_title,
		'post_status' => 'publish',
		'post_author' => 1,
		'post_slug' => 'pages',
		'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
		);
		$terms_id = wp_insert_post($terms);

	   //Set the blog with right sidebar template
	   add_post_meta( $about_id, '_wp_page_template', 'page-template/about.php' );

	   	// Create a terms page and assigned the template
		$terms_title = 'Blogs';
		$terms_check = get_page_by_title($terms_title);
		$terms = array(
		'post_type' => 'page',
		'post_title' => $terms_title,
		'post_status' => 'publish',
		'post_author' => 1,
		'post_slug' => 'blogs',
		'post_content' => '',
		);
		$terms_id = wp_insert_post($terms);

		// Set the Blogs page as the posts page
		update_option( 'page_for_posts', $terms_id );

	   //Set the blog with right sidebar template
	   add_post_meta( $about_id, '_wp_page_template', 'page-template/about.php' );

	   	// Create a terms page and assigned the template
		$terms_title = 'Contact Us';
		$terms_check = get_page_by_title($terms_title);
		$terms = array(
		'post_type' => 'page',
		'post_title' => $terms_title,
		'post_status' => 'publish',
		'post_author' => 1,
		'post_slug' => 'contact',
		'post_content' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum.',
		);
		$terms_id = wp_insert_post($terms);

	   //Set the blog with right sidebar template
	   add_post_meta( $about_id, '_wp_page_template', 'page-template/about.php' );

		/*--- Header Start---*/

		set_theme_mod( 'header_site_title', false);

		set_theme_mod( 'skincare_shop_show_hide_search', true);
		
		/*--- Header End---*/

		/*--- Slider Start---*/

		set_theme_mod( 'skincare_shop_slider_setting', true);
		set_theme_mod( 'skincare_shop_slider_text_extra', 'Glow naturally Every Day');
		set_theme_mod( 'skincare_shop_banner_title', 'Experience skincare that deeply cares, and loves your skin always.');
		set_theme_mod( 'skincare_shop_banner_title_url', '#');
		set_theme_mod( 'skincare_shop_banner_desc', 'Discover the secret to radiant, healthy skin with our naturally inspired skincare. Gentle yet powerful, our products nourish deeply, restore balance, and enhance your glow-because your skin deserves the best care every day.');
		set_theme_mod( 'skincare_shop_one_word_heading', 'Dermora');
		set_theme_mod( 'skincare_shop_right_image_box_3_text', '1,27,900+ Happy Customers');
		set_theme_mod( 'skincare_shop_slider_team_image_1', esc_url( get_template_directory_uri().'/theme-wizard/assets/images/review1.png'));
		set_theme_mod( 'skincare_shop_slider_team_image_2', esc_url( get_template_directory_uri().'/theme-wizard/assets/images/review2.png'));
		set_theme_mod( 'skincare_shop_slider_team_image_3', esc_url( get_template_directory_uri().'/theme-wizard/assets/images/review3.png'));
		set_theme_mod( 'skincare_shop_slider_team_image_4', esc_url( get_template_directory_uri().'/theme-wizard/assets/images/review4.png'));
		set_theme_mod( 'skincare_shop_slider_team_image_5', esc_url( get_template_directory_uri().'/theme-wizard/assets/images/review5.png'));

		/*--- Slider End---*/

		/** Product Tab Section Start */

		set_theme_mod( 'skincare_shop_classes_setting', true);

		set_theme_mod( 'skincare_shop_service_title', 'Trending Products');

		$category_slug = 'skincare-product';
		$category_name = 'Skincare Product';
		
		// Create category if not exists
		if (!term_exists($category_name, 'product_cat')) {
			wp_insert_term($category_name, 'product_cat', array('slug' => $category_slug));
		}
		
		// Tabs
		set_theme_mod('skincare_shop_featured_mission_section_tab_1', 'Latest Products');
		set_theme_mod('skincare_shop_trending_post_slider_args_1', $category_slug);
		set_theme_mod('skincare_shop_featured_mission_section_tab_2', 'New Arrivals');
		set_theme_mod('skincare_shop_trending_post_slider_args_2', $category_slug);
		set_theme_mod('skincare_shop_featured_mission_section_tab_3', 'Top Ratings');
		set_theme_mod('skincare_shop_trending_post_slider_args_3', $category_slug);
		set_theme_mod('skincare_shop_featured_mission_section_tab_4', 'Best Sellers');
		set_theme_mod('skincare_shop_trending_post_slider_args_4', $category_slug);
		set_theme_mod('skincare_shop_featured_mission_section_tab_5', 'Luxury Beauty');
		set_theme_mod('skincare_shop_trending_post_slider_args_5', $category_slug);
		set_theme_mod('skincare_shop_number_of_featured_mission_items', 5);
		
		// Demo products (OTT TV shows)
		$demo_products = array(
			array(
				'title' => 'Face Serum 55SPF',
				'price' => '120',
				'sale_price' => '90',
				'image' => get_stylesheet_directory_uri() . '/theme-wizard/assets/images/product1.png',
			),
			array(
				'title' => 'Face Serum 65SPF',
				'price' => '120',
				'sale_price' => '90',
				'image' => get_stylesheet_directory_uri() . '/theme-wizard/assets/images/product2.png',
			),
			array(
				'title' => 'Face Serum 75SPF',
				'price' => '120',
				'sale_price' => '90',
				'image' => get_stylesheet_directory_uri() . '/theme-wizard/assets/images/product3.png',
			),
			array(
				'title' => 'Face Serum 85SPF',
				'price' => '120',
				'sale_price' => '90',
				'image' => get_stylesheet_directory_uri() . '/theme-wizard/assets/images/product4.png',
			),
		);
		
		foreach ($demo_products as $product_data) {
			if (get_page_by_title($product_data['title'], OBJECT, 'product')) continue;
		
			$product_id = wp_insert_post(array(
				'post_title'   => wp_strip_all_tags($product_data['title']),
				'post_status'  => 'publish',
				'post_type'    => 'product',
			));
		
			if ($product_id && !is_wp_error($product_id)) {
				wp_set_object_terms($product_id, 'simple', 'product_type');
				wp_set_object_terms($product_id, $category_slug, 'product_cat');
		
				update_post_meta($product_id, '_regular_price', $product_data['price']);
				update_post_meta($product_id, '_sale_price', $product_data['sale_price']);
				update_post_meta($product_id, '_price', $product_data['sale_price']);
				update_post_meta($product_id, '_featured', 'yes');
				update_post_meta($product_id, '_visibility', 'visible');
		
				if (!empty($product_data['image'])) {
					$image_url = $product_data['image'];
					$image_name = basename($image_url);
					$upload_dir = wp_upload_dir();
					$image_data = @file_get_contents($image_url);
		
					$image_data = @file_get_contents($image_url);

					if ($image_data) {
						$unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
						$file = wp_upload_bits($unique_file_name, null, $image_data);
					
						if (!$file['error']) {
							$wp_filetype = wp_check_filetype($file['file'], null);
							$attachment = array(
								'post_mime_type' => $wp_filetype['type'],
								'post_title'     => sanitize_file_name($unique_file_name),
								'post_content'   => '',
								'post_status'    => 'inherit'
							);
					
							$attach_id = wp_insert_attachment($attachment, $file['file'], $product_id);
							require_once(ABSPATH . 'wp-admin/includes/image.php');
							$attach_data = wp_generate_attachment_metadata($attach_id, $file['file']);
							wp_update_attachment_metadata($attach_id, $attach_data);
							set_post_thumbnail($product_id, $attach_id);
						}
					}					
				}
			}
		}
		
			
		/** Product Tab Section End */

		/*--- Logo Start---*/

		$image_url = get_template_directory_uri().'/theme-wizard/assets/images/logo.png';
        $image_name       = 'logo.png';

        $upload_dir = wp_upload_dir();
        // Set upload folder
        $image_data_1 = file_get_contents(esc_url($image_url));

        // Get image data
        $unique_file_name = wp_unique_filename($upload_dir['path'], $image_name);
        // Generate unique name
        $filename = basename($unique_file_name);
        // Create image file name

        // Check folder permission and define file location
        if (wp_mkdir_p($upload_dir['path'])) {
            $file = $upload_dir['path'].'/'.$filename;
        } else {
            $file = $upload_dir['basedir'].'/'.$filename;
        }

		// Create the image  file on the server
		if ( ! function_exists( 'WP_Filesystem' ) ) {
		    require_once( ABSPATH . 'wp-admin/includes/file.php' );
		}

		WP_Filesystem();
		global $wp_filesystem;

		if ( ! $wp_filesystem->put_contents( $file, $image_data_1, FS_CHMOD_FILE ) ) {
		    wp_die( 'Error saving file!' );
		}


        // Check image file type
        $wp_filetype = wp_check_filetype($filename, null);

        // Set attachment data
        $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title'     => sanitize_file_name($filename),
        'post_type'      => '',
        'post_status'    => 'inherit',
        );

        // Create the attachment
        $attach_id = wp_insert_attachment($attachment, $file);

        set_theme_mod( 'custom_logo', $attach_id );

        /*--- Logo End---*/

		$this->create_theme_nav_menu();
		update_option( 'skincare_shop_demo_import_done', 1 );
	}
}