<?php

class SiteOrigin_Settings_Control_Premium extends WP_Customize_Control {
	public $type = 'siteorigin-premium-notification';

	/**
	 * Render the font selector
	 */
	public function render_content(){
		$theme = wp_get_theme();
		?>
		<p>
			<?php
			printf(
				__( "SiteOrigin Premium adds powerful features to %s. They'll save you time and make your site more professional.", 'siteorigin-corp' ),
				$theme->get( 'Name' )
			);
			?>
		</p>
		<a
			href="<?php echo esc_url( SiteOrigin_Settings::get_premium_url( ) ) ?>"
			class="button-primary so-premium-upgrade"
			target="_blank">
			<?php esc_html_e( 'Find Out More', 'siteorigin-corp' ) ?>
		</a>
		<?php
	}

	/**
	 * Enqueue all the scripts and styles we need
	 */
	public function enqueue() {
	}
}
