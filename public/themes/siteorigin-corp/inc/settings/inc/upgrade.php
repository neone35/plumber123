<?php

/**
 * A legacy upgrade notice that we'll temporarily include for users on Vantage free who have an order number saved.
 *
 * Class SiteOrigin_Settings_Upgrade
 */
class SiteOrigin_Settings_Upgrade {

	function __construct(){
		add_action( 'admin_notices', array( $this, 'display_admin_notice' ) );
		add_action( 'wp_ajax_dismiss_so_premium_notice', array( $this, 'dismiss_action' ) );
	}

	static function single() {
		static $single;

		if( empty($single) ) {
			$single = new self();
		}
		return $single;
	}

	/**
	 * Display the upgrade admin notice.
	 */
	function display_admin_notice(){
		// An issue with WordPress 4.6 caused our premium themes to downgrade to the free versions.
		// Detect if this is the case and display a message to the user.
		if(
			! defined( 'SITEORIGIN_IS_PREMIUM' ) &&
			! get_theme_mod( '_premium_notice_dismissed' ) &&
			current_user_can( 'install_themes' ) &&
			siteorigin_setting( 'premium_order_number' )
		) {
			$theme = wp_get_theme();

			$download_url = 'http://updates.siteorigin.com/premium/?action=download&order=' . urlencode( siteorigin_setting( 'premium_order_number' ) );
			$instructions_url = 'https://siteorigin.com/fixing-wordpress-4-6-downgrade-bug/';
			$dismiss_url = add_query_arg( array(
				'action' => 'dismiss_so_premium_notice',
				'return' => esc_url( add_query_arg( false, false ) ),
			), admin_url( 'admin-ajax.php' ) );

			?>
			<div class="notice notice-error">
				<p>
					<?php
					printf(
						__( 'You\'re currently running the free version of %1$s, but you have a premium order number entered. %2$sDownload%3$s the premium version and %4$sread instructions%5$s on how to upgrade. %6$sDismiss%7$s this notice.', 'siteorigin-corp' ),
						$theme->get( 'Name' ),
						'<a href="' . esc_url( $download_url ) . '" target="_blank">',
						'</a>',
						'<a href="' . esc_url( $instructions_url ) . '" target="_blank">',
						'</a>',
						'<a href="' . esc_url( wp_nonce_url( $dismiss_url, 'dismiss-notice' ) ) . '">',
						'</a>'
					);
					?>
				</p>
			</div>
			<?php
		}
	}

	/**
	 * Dismiss the admin notice
	 */
	function dismiss_action(){
		if( empty( $_GET['_wpnonce'] ) || ! wp_verify_nonce( $_GET['_wpnonce'], 'dismiss-notice' ) ) exit();

		set_theme_mod( '_premium_notice_dismissed', true );
		wp_redirect( $_GET[ 'return' ] );
		exit();
	}
}