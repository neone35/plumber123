<?php
/**
 * Plugin Name: Google Analytics Dashboard for WP (GADWP)
 * Plugin URI: https://exactmetrics.com
 * Description: Displays Google Analytics Reports and Real-Time Statistics in your Dashboard. Automatically inserts the tracking code in every page of your website.
 * Author: ExactMetrics
 * Version: 5.3.5
 * Author URI: https://exactmetrics.com
 * Text Domain: google-analytics-dashboard-for-wp
 * Domain Path: /languages
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

// Plugin Version
if ( ! defined( 'GADWP_CURRENT_VERSION' ) ) {
	define( 'GADWP_CURRENT_VERSION', '5.3.5' );
}

if ( ! defined( 'GADWP_ENDPOINT_URL' ) ) {
	define( 'GADWP_ENDPOINT_URL', 'https://gadwp.exactmetrics.com/' );
}


if ( ! class_exists( 'GADWP_Manager' ) ) {

	final class GADWP_Manager {

		private static $instance = null;

		public $config = null;

		public $frontend_actions = null;

		public $common_actions = null;

		public $backend_actions = null;

		public $tracking = null;

		public $frontend_item_reports = null;

		public $backend_setup = null;

		public $frontend_setup = null;

		public $backend_widgets = null;

		public $backend_item_reports = null;

		public $gapi_controller = null;

		public $usage_tracking = null;

		/**
		 * Construct forbidden
		 */
		private function __construct() {
			if ( null !== self::$instance ) {
				_doing_it_wrong( __FUNCTION__, __( "This is not allowed, read the documentation!", 'google-analytics-dashboard-for-wp' ), '4.6' );
			}
		}

		/**
		 * Clone warning
		 */
		private function __clone() {
			_doing_it_wrong( __FUNCTION__, __( "This is not allowed, read the documentation!", 'google-analytics-dashboard-for-wp' ), '4.6' );
		}

		/**
		 * Wakeup warning
		 */
		private function __wakeup() {
			_doing_it_wrong( __FUNCTION__, __( "This is not allowed, read the documentation!", 'google-analytics-dashboard-for-wp' ), '4.6' );
		}

		/**
		 * Creates a single instance for GADWP and makes sure only one instance is present in memory.
		 *
		 * @return GADWP_Manager
		 */
		public static function instance() {
			if ( null === self::$instance ) {
				self::$instance = new self();
				self::$instance->setup();
				self::$instance->config = new GADWP_Config();
				if ( is_admin() && class_exists( 'AM_Notification' ) && defined( 'GADWP_CURRENT_VERSION' ) ) {
					new AM_Notification( 'exact-metrics', GADWP_CURRENT_VERSION );
				}
			}
			return self::$instance;
		}

		/**
		 * Defines constants and loads required resources
		 */
		private function setup() {

			// Plugin Path
			if ( ! defined( 'GADWP_DIR' ) ) {
				define( 'GADWP_DIR', plugin_dir_path( __FILE__ ) );
			}

			// Plugin URL
			if ( ! defined( 'GADWP_URL' ) ) {
				define( 'GADWP_URL', plugin_dir_url( __FILE__ ) );
			}

			// Plugin main File
			if ( ! defined( 'GADWP_FILE' ) ) {
				define( 'GADWP_FILE', __FILE__ );
			}

			/*
			 * Load notifications class
			 */
			if ( is_admin() ) {
				include_once ( GADWP_DIR . 'admin/class-am-notification.php' );
			}

			/*
			 * Load Tools class
			 */
			include_once ( GADWP_DIR . 'tools/tools.php' );

			/*
			 * Load Config class
			 */
			include_once ( GADWP_DIR . 'config.php' );

			/*
			 * Load GAPI Controller class
			 */
			include_once ( GADWP_DIR . 'tools/gapi.php' );

			/*
			 * Plugin i18n
			 */
			add_action( 'init', array( self::$instance, 'load_i18n' ) );

			/*
			 * Plugin Init
			 */
			add_action( 'init', array( self::$instance, 'load' ) );

			/*
			 * Include Install
			 */
			include_once ( GADWP_DIR . 'install/install.php' );
			register_activation_hook( GADWP_FILE, array( 'GADWP_Install', 'install' ) );

			/*
			 * Include Uninstall
			 */
			include_once ( GADWP_DIR . 'install/uninstall.php' );
			register_uninstall_hook( GADWP_FILE, array( 'GADWP_Uninstall', 'uninstall' ) );

			/*
			 * Load Frontend Widgets
			 * (needed during ajax)
			 */
			include_once ( GADWP_DIR . 'front/widgets.php' );

			/*
			 * Add Frontend Widgets
			 * (needed during ajax)
			 */
			add_action( 'widgets_init', array( self::$instance, 'add_frontend_widget' ) );
		}

		/**
		 * Load i18n
		 */
		public function load_i18n() {
			load_plugin_textdomain( 'google-analytics-dashboard-for-wp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages' );
		}

		/**
		 * Register Frontend Widgets
		 */
		public function add_frontend_widget() {
			register_widget( 'GADWP_Frontend_Widget' );
		}

		/**
		 * Conditional load
		 */
		public function load() {
			if ( is_admin() ) {
				if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
					if ( GADWP_Tools::check_roles( self::$instance->config->options['access_back'] ) ) {
						/*
						 * Load Backend ajax actions
						 */
						include_once ( GADWP_DIR . 'admin/ajax-actions.php' );
						self::$instance->backend_actions = new GADWP_Backend_Ajax();
					}

					/*
					 * Load Frontend ajax actions
					 */
					include_once ( GADWP_DIR . 'front/ajax-actions.php' );
					self::$instance->frontend_actions = new GADWP_Frontend_Ajax();

					/*
					 * Load Common ajax actions
					 */
					include_once ( GADWP_DIR . 'common/ajax-actions.php' );
					self::$instance->common_actions = new GADWP_Common_Ajax();

					if ( self::$instance->config->options['backend_item_reports'] ) {
						/*
						 * Load Backend Item Reports for Quick Edit
						 */
						include_once ( GADWP_DIR . 'admin/item-reports.php' );
						self::$instance->backend_item_reports = new GADWP_Backend_Item_Reports();
					}
				} else if ( GADWP_Tools::check_roles( self::$instance->config->options['access_back'] ) ) {

					/*
					 * Load Backend Setup
					 */
					include_once ( GADWP_DIR . 'admin/setup.php' );
					self::$instance->backend_setup = new GADWP_Backend_Setup();

					if ( self::$instance->config->options['dashboard_widget'] ) {
						/*
						 * Load Backend Widget
						 */
						include_once ( GADWP_DIR . 'admin/widgets.php' );
						self::$instance->backend_widgets = new GADWP_Backend_Widgets();
					}

					if ( self::$instance->config->options['backend_item_reports'] ) {
						/*
						 * Load Backend Item Reports
						 */
						include_once ( GADWP_DIR . 'admin/item-reports.php' );
						self::$instance->backend_item_reports = new GADWP_Backend_Item_Reports();
					}

					include_once ( GADWP_DIR . 'admin/tracking.php' );
					self::$instance->usage_tracking = new ExactMetrics_Tracking();
				}
			} else {
				if ( GADWP_Tools::check_roles( self::$instance->config->options['access_front'] ) ) {
					/*
					 * Load Frontend Setup
					 */
					include_once ( GADWP_DIR . 'front/setup.php' );
					self::$instance->frontend_setup = new GADWP_Frontend_Setup();

					if ( self::$instance->config->options['frontend_item_reports'] ) {
						/*
						 * Load Frontend Item Reports
						 */
						include_once ( GADWP_DIR . 'front/item-reports.php' );
						self::$instance->frontend_item_reports = new GADWP_Frontend_Item_Reports();
					}
				}

				if ( ! GADWP_Tools::check_roles( self::$instance->config->options['track_exclude'], true ) && 'disabled' != self::$instance->config->options['tracking_type'] ) {
					/*
					 * Load tracking class
					 */
					include_once ( GADWP_DIR . 'front/tracking.php' );
					self::$instance->tracking = new GADWP_Tracking();
				}
			}
		}
	}
}

/**
 * Returns a unique instance of GADWP
 */
function GADWP() {
	return GADWP_Manager::instance();
}

/*
 * Start GADWP
 */
GADWP();
