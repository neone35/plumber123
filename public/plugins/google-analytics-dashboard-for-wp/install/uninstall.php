<?php
/**
 * Author: ExactMetrics team
 * Author URI: https://exactmetrics.com
 * Copyright 2018 ExactMetrics team
 * License: GPLv2 or later
 * License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) )
	exit();

class GADWP_Uninstall {

	public static function uninstall() {
		global $wpdb;
		if ( is_multisite() ) { // Cleanup Network install
			foreach ( GADWP_Tools::get_sites( array( 'number' => apply_filters( 'gadwp_sites_limit', 100 ) ) ) as $blog ) {
				switch_to_blog( $blog['blog_id'] );
				$sqlquery = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'gadwp_cache_%%'" );
				delete_option( 'gadwp_options' );
				delete_option( 'exactmetrics_tracking_notice');
				delete_option( 'exactmetrics_usage_tracking_last_checkin');
				delete_option( 'exactmetrics_usage_tracking_config');
				wp_clear_scheduled_hook( 'exactmetrics_usage_tracking_cron' );
				restore_current_blog();
			}
			delete_site_option( 'gadwp_network_options' );
		} else { // Cleanup Single install
			$sqlquery = $wpdb->query( "DELETE FROM $wpdb->options WHERE option_name LIKE 'gadwp_cache_%%'" );
			delete_option( 'gadwp_options' );
			delete_option( 'exactmetrics_tracking_notice');
			delete_option( 'exactmetrics_usage_tracking_last_checkin');
			delete_option( 'exactmetrics_usage_tracking_config');
				wp_clear_scheduled_hook( 'exactmetrics_usage_tracking_cron' );
		}
		GADWP_Tools::unset_cookie( 'default_metric' );
		GADWP_Tools::unset_cookie( 'default_dimension' );
		GADWP_Tools::unset_cookie( 'default_view' );
	}
}
