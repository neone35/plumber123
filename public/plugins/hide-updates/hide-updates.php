<?php
/**
 * Plugin Name: Hide Updates
 * Description: This plugin hides update notices for core, plugin, and theme updates in WordPress admin for all users **except super admins**. It's useful for agencies or developers who take care of updates and maintenance of a client's site and wants to hide the notices for other users.
 * Version: 1.0
 * Author: Upperdog
 * Author URI: https://upperdog.com
 * Author Email: hello@upperdog.com
 * License: GPLv2 or later

 Copyright 2017 Upperdog (email : hello@upperdog.com)

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU General Public License, version 2, as 
 published by the Free Software Foundation.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program; if not, write to the Free Software
 Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

if ( !defined( 'ABSPATH' ) ) {
	exit;
}

if ( !function_exists( 'wp_get_current_user' ) ) {
    include( ABSPATH . 'wp-includes/pluggable.php' );
}

function hide_updates_get_users() {
	$hide_updates_users = array(
		'super_admins' => get_super_admins(), 
		'current_user' => wp_get_current_user(), 
	);
	
	return $hide_updates_users;
}

class HideUpdates {
	
	function __construct() {
		$hide_updates_users = hide_updates_get_users();
		
		if ( !in_array( $hide_updates_users[ 'current_user' ]->user_login, $hide_updates_users[ 'super_admins' ] ) ) {
			add_filter( 'pre_site_transient_update_core', array( $this, 'hide_updates' ) );
			add_filter( 'pre_site_transient_update_plugins', array( $this, 'hide_updates' ) );
			add_filter( 'pre_site_transient_update_themes', array( $this, 'hide_updates' ) );
			add_action( 'admin_menu', array( $this, 'hide_updates_submenu_page' ) );
			add_action( 'admin_init', array( $this, 'block_admin_pages') );
		}
	}
	
	function hide_updates() {
		global $wp_version;
		return (object) array( 
			'last_checked' => time(), 
			'version_checked' => $wp_version, 
			'updates' => array(), 
		);
	}
	
	function hide_updates_submenu_page() {
		remove_submenu_page( 'index.php', 'update-core.php' );
	}
	
	function block_admin_pages() {
		global $pagenow;
		
		$blocked_admin_pages = array( 'update-core.php' );
		
		if ( in_array( $pagenow, $blocked_admin_pages ) ) {
			wp_redirect( admin_url( '/' ) );
			exit;
		}
	}
}

$hide_updates = new HideUpdates();