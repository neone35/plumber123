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

if ( ! class_exists( 'GADWP_Frontend_Ajax' ) ) {

	final class GADWP_Frontend_Ajax {

		private $gadwp;

		public function __construct() {
			$this->gadwp = GADWP();

			if ( GADWP_Tools::check_roles( $this->gadwp->config->options['access_front'] ) && $this->gadwp->config->options['frontend_item_reports'] ) {
				// Item Reports action
				add_action( 'wp_ajax_gadwp_frontend_item_reports', array( $this, 'ajax_item_reports' ) );
			}

			// Frontend Widget actions
			add_action( 'wp_ajax_ajax_frontwidget_report', array( $this, 'ajax_frontend_widget' ) );
			add_action( 'wp_ajax_nopriv_ajax_frontwidget_report', array( $this, 'ajax_frontend_widget' ) );
		}

		/**
		 * Ajax handler for Item Reports
		 *
		 * @return string|int
		 */
		public function ajax_item_reports() {
			if ( ! isset( $_POST['gadwp_security_frontend_item_reports'] ) || ! wp_verify_nonce( $_POST['gadwp_security_frontend_item_reports'], 'gadwp_frontend_item_reports' ) ) {
				wp_die( - 30 );
			}

			$from = $_POST['from'];
			$to = $_POST['to'];
			$query = $_POST['query'];
			$uri = $_POST['filter'];
			if ( isset( $_POST['metric'] ) ) {
				$metric = $_POST['metric'];
			} else {
				$metric = 'pageviews';
			}

			$query = $_POST['query'];
			if ( ob_get_length() ) {
				ob_clean();
			}

			if ( ! GADWP_Tools::check_roles( $this->gadwp->config->options['access_front'] ) || 0 == $this->gadwp->config->options['frontend_item_reports'] ) {
				wp_die( - 31 );
			}

			if ( $this->gadwp->config->options['token'] && $this->gadwp->config->options['tableid_jail'] ) {
				if ( null === $this->gadwp->gapi_controller ) {
					$this->gadwp->gapi_controller = new GADWP_GAPI_Controller();
				}
			} else {
				wp_die( - 24 );
			}

			if ( $this->gadwp->config->options['tableid_jail'] ) {
				$projectId = $this->gadwp->config->options['tableid_jail'];
			} else {
				wp_die( - 26 );
			}

			$profile_info = GADWP_Tools::get_selected_profile( $this->gadwp->config->options['ga_profiles_list'], $projectId );

			if ( isset( $profile_info[4] ) ) {
				$this->gadwp->gapi_controller->timeshift = $profile_info[4];
			} else {
				$this->gadwp->gapi_controller->timeshift = (int) current_time( 'timestamp' ) - time();
			}

			$uri = '/' . ltrim( $uri, '/' );

			// allow URL correction before sending an API request
			$filter = apply_filters( 'gadwp_frontenditem_uri', $uri );

			$lastchar = substr( $filter, - 1 );

			if ( isset( $profile_info[6] ) && $profile_info[6] && '/' == $lastchar ) {
				$filter = $filter . $profile_info[6];
			}

			// Encode URL
			$filter = rawurlencode( rawurldecode( $filter ) );

			$queries = explode( ',', $query );

			$results = array();

			foreach ( $queries as $value ) {
				$results[] = $this->gadwp->gapi_controller->get( $projectId, $value, $from, $to, $filter, $metric );
			}

			wp_send_json( $results );
		}

		/**
		 * Ajax handler for getting analytics data for frontend Widget
		 *
		 * @return string|int
		 */
		public function ajax_frontend_widget() {
			if ( ! isset( $_POST['gadwp_number'] ) || ! isset( $_POST['gadwp_optionname'] ) || ! is_active_widget( false, false, 'gadwp-frontwidget-report' ) ) {
				wp_die( - 30 );
			}
			$widget_index = $_POST['gadwp_number'];
			$option_name = $_POST['gadwp_optionname'];
			$options = get_option( $option_name );
			if ( isset( $options[$widget_index] ) ) {
				$instance = $options[$widget_index];
			} else {
				wp_die( - 32 );
			}
			switch ( $instance['period'] ) { // make sure we have a valid request
				case '7daysAgo' :
					$period = '7daysAgo';
					break;
				case '14daysAgo' :
					$period = '14daysAgo';
					break;
				default :
					$period = '30daysAgo';
					break;
			}
			if ( ob_get_length() ) {
				ob_clean();
			}
			if ( $this->gadwp->config->options['token'] && $this->gadwp->config->options['tableid_jail'] ) {
				if ( null === $this->gadwp->gapi_controller ) {
					$this->gadwp->gapi_controller = new GADWP_GAPI_Controller();
				}
			} else {
				wp_die( - 24 );
			}
			$projectId = $this->gadwp->config->options['tableid_jail'];
			$profile_info = GADWP_Tools::get_selected_profile( $this->gadwp->config->options['ga_profiles_list'], $projectId );
			if ( isset( $profile_info[4] ) ) {
				$this->gadwp->gapi_controller->timeshift = $profile_info[4];
			} else {
				$this->gadwp->gapi_controller->timeshift = (int) current_time( 'timestamp' ) - time();
			}
			wp_send_json( $this->gadwp->gapi_controller->frontend_widget_stats( $projectId, $period, (int) $instance['anonim'] ) );
		}
	}
}
