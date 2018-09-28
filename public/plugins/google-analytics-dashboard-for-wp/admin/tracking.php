<?php
/**
 * Tracking functions for reporting plugin usage to the site for users that have opted in
 *
 * @package     ExactMetrics
 * @subpackage  Admin
 * @copyright   Copyright (c) 2018, Chris Christoff
 * @since       5.3.4
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Usage tracking
 *
 * @access public
 * @since  7.0.0
 * @return void
 */
class ExactMetrics_Tracking {

	public function __construct() {
		$this->gadwp = GADWP();
		add_action( 'init', array( $this, 'schedule_send' ) );
		add_action( 'admin_head', array( $this, 'check_for_optin' ) );
		add_action( 'admin_head', array( $this, 'check_for_optout' ) );
		add_filter( 'cron_schedules', array( $this, 'add_schedules' ) );
		add_action( 'exactmetrics_usage_tracking_cron', array( $this, 'send_checkin' ) );
		add_action( 'exactmetrics_settings_usage_tracking', array( $this, 'check_for_settings_optin' ) );
		add_action( 'admin_notices', array( $this, 'setup_notice' ), 999 );
	}

	private function get_data() {
		$data = array();

		$options         = get_option( 'gadwp_options' );
		if ( empty( $options ) ) {
			$options = array();
		} else {
			$options = (array) json_decode( $options );
		}

		$network_options = get_site_option( 'gadwp_network_options' );
		if ( empty( $network_options ) ) {
			$network_options = array();
		} else {
			$network_options = (array) json_decode( $network_options );
		}

		// Foreach network options, prefix with network
		if ( ! empty ( $network_options ) ) {
			foreach ( $network_options as $noptionid => $noptionvalue ) {
				$new_id 			= 'network_' . $noptionid;
				$options[ $new_id ] = $noptionvalue;
			}
		}

		// Ensure tokens and secrets are never sent to us
		unset( $options['token'] );
		unset( $options['client_secret'] );
		unset( $options['network_token'] );
		unset( $options['network_client_secret'] );

		// Retrieve current theme info
		$theme_data    = wp_get_theme();

		$tracking_mode =  'default';
		if ( ! empty( $options['tracking_type'] ) || ! empty( $options['network_tracking_type'] ) ) {
			$tracking_mode = 'minor';
		}
		if ( ! empty( $options['ga_with_gtag'] ) ||  ! empty( $options['network_ga_with_gtag'] ) ) {
			$tracking_mode = 'gtag';
		}

		$update_mode   	=  'none';
		if ( ! empty( $options['automatic_updates_minorversion'] ) || ! empty( $options['network_automatic_updates_minorversion'] ) ) {
			$update_mode = 'minor';
		}


		$count_b = 1;
		if ( is_multisite() ) {
			if ( function_exists( 'get_blog_count' ) ) {
				$count_b = get_blog_count();
			} else {
				$count_b = 'Not Set';
			}
		}

		$data['php_version']   = phpversion();
		$data['mi_version']    = GADWP_CURRENT_VERSION;
		$data['wp_version']    = get_bloginfo( 'version' );
		$data['server']        = isset( $_SERVER['SERVER_SOFTWARE'] ) ? $_SERVER['SERVER_SOFTWARE'] : '';
		$data['multisite']     = is_multisite();
		$data['url']           = home_url();
		$data['themename']     = $theme_data->Name;
		$data['themeversion']  = $theme_data->Version;
		$data['settings']      = $options;
		$data['tracking_mode'] = $tracking_mode;
		$data['autoupdate']    = $update_mode;
		$data['sites']         = $count_b;
		$data['usagetracking'] = get_option( 'exactmetrics_usage_tracking_config', false );
		$data['usercount']     = function_exists( 'get_user_count' ) ? get_user_count() : 'Not Set';
		$data['timezoneoffset']= date('P');



		// Retrieve current plugin information
		if( ! function_exists( 'get_plugins' ) ) {
			include ABSPATH . '/wp-admin/includes/plugin.php';
		}

		$plugins        = array_keys( get_plugins() );
		$active_plugins = get_option( 'active_plugins', array() );

		foreach ( $plugins as $key => $plugin ) {
			if ( in_array( $plugin, $active_plugins ) ) {
				// Remove active plugins from list so we can show active and inactive separately
				unset( $plugins[ $key ] );
			}
		}

		$data['active_plugins']   = $active_plugins;
		$data['inactive_plugins'] = $plugins;
		$data['locale']           = get_locale();

		return $data;
	}

	public function send_checkin( $override = false, $ignore_last_checkin = false ) {

		$home_url = trailingslashit( home_url() );
		if ( strpos( $home_url, 'exactmetrics.com' ) !== false ) {
			return false;
		}

		if( ! $this->tracking_allowed() && ! $override ) {
			return false;
		}

		// Send a maximum of once per week
		$last_send = get_option( 'exactmetrics_usage_tracking_last_checkin' );
		if ( is_numeric( $last_send ) && $last_send > strtotime( '-1 week' ) && ! $ignore_last_checkin ) {
			return false;
		}

		$request = wp_remote_post( 'https://miusage.com/v1/em-checkin/', array(
			'method'      => 'POST',
			'timeout'     => 5,
			'redirection' => 5,
			'httpversion' => '1.1',
			'blocking'    => false,
			'body'        => $this->get_data(),
			'user-agent'  => 'EM/' . GADWP_CURRENT_VERSION . '; ' . get_bloginfo( 'url' )
		) );

		// If we have completed successfully, recheck in 1 week
		update_option( 'exactmetrics_usage_tracking_last_checkin', time() );
		return true;
	}

	private function tracking_allowed() {
		return (bool) $this->get_option( 'usage_tracking', 0 );
	}

	public function schedule_send() {
		if ( ! wp_next_scheduled( 'exactmetrics_usage_tracking_cron' ) ) {
			$tracking             = array();
			$tracking['day']      = rand( 0, 6  );
			$tracking['hour']     = rand( 0, 23 );
			$tracking['minute']   = rand( 0, 59 );
			$tracking['second']   = rand( 0, 59 );
			$tracking['offset']   = ( $tracking['day']    * DAY_IN_SECONDS    ) +
									( $tracking['hour']   * HOUR_IN_SECONDS   ) +
									( $tracking['minute'] * MINUTE_IN_SECONDS ) +
									 $tracking['second'];
			$tracking['initsend'] = strtotime("next sunday") + $tracking['offset'];

			wp_schedule_event( $tracking['initsend'], 'weekly', 'exactmetrics_usage_tracking_cron' );
			update_option( 'exactmetrics_usage_tracking_config', $tracking );
		}
	}

	public function check_for_settings_optin( $new_value ) {
		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$new_value = intval( $new_value );
		$current_value = $this->get_option( 'usage_tracking', 0 );

		if ( $current_value == $new_value ) {
			return;
		}

		if ( $new_value ) {
			$this->send_checkin( true, true );
			update_option( 'exactmetrics_tracking_notice', 1 );
		} else {
			update_option( 'exactmetrics_tracking_notice', 0 );
		}
	}

	public function check_for_optin() {
		if ( ! ( ! empty( $_REQUEST['em_action'] ) && 'opt_into_tracking' === $_REQUEST['em_action'] ) ) {
			return;
		}

		if ( $this->get_option( 'usage_tracking', 0 ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->set_option( 'usage_tracking', 1 );
		$this->send_checkin( true, true );
		update_option( 'exactmetrics_tracking_notice', 1 );
	}

	public function check_for_optout() {
		if ( ! ( ! empty( $_REQUEST['em_action'] ) && 'opt_out_of_tracking' === $_REQUEST['em_action'] ) ) {
			return;
		}

		if ( $this->get_option( 'usage_tracking', 0 ) ) {
			return;
		}

		if ( ! current_user_can( 'manage_options' ) ) {
			return;
		}

		$this->set_option( 'usage_tracking', 0 );
		update_option( 'exactmetrics_tracking_notice', 1 );
	}

	public function setup_notice(){
    	if ( ! is_network_admin() && ( ! isset( $_GET['page'] ) || ( isset( $_GET['page'] ) && $_GET['page'] !== 'gadwp_tracking_settings' ) && $_GET['page'] !== 'gadwp_settings' ) ) {
         	if ( ! get_option( 'exactmetrics_tracking_notice' ) ) {
             	if ( ! $this->get_option( 'usage_tracking', 0 ) ) {
                	$optin_url  = add_query_arg( 'em_action', 'opt_into_tracking' );
                	$optout_url = add_query_arg( 'em_action', 'opt_out_of_tracking' );
                	echo '<div class="updated"><p>';
                	echo sprintf( esc_html__( 'ExactMetrics would like to better understand how our users use our plugin so we can get a better understanding of which features and bugfixes to prioritize. %1$sCan we collect some %2$sinformation about our plugin usage?%3$s', 'google-analytics-dashboard-for-wp' ), '<br />', '<a href="https://exactmetrics.com/usage-tracking/?utm_source=wpdashboard&utm_campaign=usagetracking&utm_medium=plugin" target="_blank">', '</a>' ); 
                	echo '&nbsp;<a href="' . esc_url( $optin_url ) . '" class="button-secondary">' . __( 'Yes, I\'d like to help out', 'google-analytics-dashboard-for-wp' ) . '</a>';
                	echo '&nbsp;<a href="' . esc_url( $optout_url ) . '" class="button-secondary">' . __( 'No thanks', 'google-analytics-dashboard-for-wp' ) . '</a>';
                	echo '</p></div>';
                	return;
            	}
        	}
    	}
	}

	public function add_schedules( $schedules = array() ) {
		// Adds once weekly to the existing schedules.
		$schedules['weekly'] = array(
			'interval' => 604800,
			'display'  => __( 'Once Weekly', 'google-analytics-dashboard-for-wp' )
		);
		return $schedules;
	}

	public function get_option( $option, $default = false ) {
		if ( ! empty( $this->gadwp->config->options ) && is_array( $this->gadwp->config->options ) && isset( $this->gadwp->config->options[$option] ) ) {
			return $this->gadwp->config->options[$option];
		} else {
			return $default;
		}
	}

	public function set_option( $option, $value ) {
		$new_options = array();
		$new_options[$option] = $value;
		$options = array_merge( $this->gadwp->config->options, $new_options );
		$this->gadwp->config->options = $options;
		$this->gadwp->config->set_plugin_options( false );
	}
}