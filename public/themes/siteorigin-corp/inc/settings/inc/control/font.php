<?php

class SiteOrigin_Settings_Control_Font extends WP_Customize_Control {
	public $type = 'siteorigin-font';

	/**
	 * Render the font selector
	 */
	public function render_content(){
		if ( ! empty( $this->label ) ) {
			?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
		}
		if ( ! empty( $this->description ) ) {
			?><span class="description customize-control-description"><?php echo $this->description; ?></span><?php
		}

		static $fonts = false;
		static $websafe = false;
		if( empty($fonts) ) {
			$fonts = include dirname(__FILE__) . '/../../data/fonts.php';
		}
		if( empty($websafe) ) {
			$websafe = include dirname(__FILE__) . '/../../data/websafe.php';
		}

		?>
		<div class="font-wrapper">
			<select class="font">
				<!-- Unchanged -->
				<option value="" data-webfont="false"></option>

				<optgroup label="Web Safe">
					<?php foreach( $websafe as $name => $attr ) : ?>
						<option
							value="<?php echo esc_attr($name) ?>"
							data-variants="<?php echo esc_attr( implode( ',', $attr['variants'] ) ) ?>"
							data-subsets="<?php echo esc_attr( implode( ',', $attr['subsets'] ) ) ?>"
							data-category="<?php echo esc_attr($attr['category']) ?>"
							data-webfont="false"
							style="font-family: '<?php echo esc_attr($name) ?>', <?php echo esc_attr($attr['category']) ?>, __websafe">
							<?php echo esc_html($name) ?>
						</option>
					<?php endforeach; ?>
				</optgroup>

				<optgroup label="Google Webfonts">
					<?php foreach( $fonts as $name => $attr ) : ?>
						<option
							value="<?php echo esc_attr($name) ?>"
							data-variants="<?php echo esc_attr( implode( ',', $attr['variants'] ) ) ?>"
							data-subsets="<?php echo esc_attr( implode( ',', $attr['subsets'] ) ) ?>"
							data-category="<?php echo esc_attr($attr['category']) ?>"
							data-webfont="true"
							style="font-family: '<?php echo esc_attr($name) ?>', <?php echo esc_attr($attr['category']) ?>">
							<?php echo esc_html($name) ?>
						</option>
					<?php endforeach; ?>
				</optgroup>
			</select>
		</div>

		<div class="field-wrapper">
			<label><?php esc_html_e( 'Variant', 'siteorigin-corp' ) ?></label>
			<select class="font-variant"></select>
		</div>

		<div class="field-wrapper">
			<label><?php esc_html_e( 'Subset', 'siteorigin-corp' ) ?></label>
			<select class="font-subset"></select>
		</div>

		<input type="hidden" value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> />

		<?php
	}

	/**
	 * Enqueue all the scripts and styles we need
	 */
	public function enqueue() {
		// We'll use chosen for the font selector
		wp_enqueue_script( 'siteorigin-settings-chosen', get_template_directory_uri() . '/inc/settings/chosen/chosen.jquery.min.js', array('jquery'), '1.4.2' );
		wp_enqueue_style( 'siteorigin-settings-chosen', get_template_directory_uri() . '/inc/settings/chosen/chosen.min.css', array(), '1.4.2' );

		// The main font controls
		wp_enqueue_script( 'siteorigin-settings-font-control', get_template_directory_uri() . '/inc/settings/js/control/font-control' . SITEORIGIN_THEME_JS_PREFIX . '.js', array( 'jquery', 'customize-controls' ) );
		wp_enqueue_style( 'siteorigin-settings-font-control', get_template_directory_uri() . '/inc/settings/css/control/font-control.css', array() );
	}
}
