<?php

class SiteOrigin_Settings_Control_Text_Select extends WP_Customize_Control {
	public $type = 'siteorigin-text-select';
	public $choices;

	function render_content() {
		if ( ! empty( $this->label ) ) {
			?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
		}
		if ( ! empty( $this->description ) ) {
			?><span class="description customize-control-description"><?php echo $this->description; ?></span><?php
		}

		?>
		<input type="text" <?php $this->input_attrs(); ?> value="<?php echo esc_attr( $this->value() ); ?>" <?php $this->link(); ?> class="widefat" />

		<select class="text-options">
			<?php foreach( $this->choices as $key => $choice ) : ?>
				<option value="<?php echo esc_attr($key) ?>" <?php selected( $key, $this->value() ) ?>>
					<?php echo esc_html($choice) ?>
				</option>
			<?php endforeach ?>
		</select>
		<?php
	}

	public function enqueue() {
		wp_enqueue_script( 'siteorigin-settings-text-select-control', get_template_directory_uri() . '/inc/settings/js/control/text-select-control' . SITEORIGIN_THEME_JS_PREFIX . '.js', array( 'jquery', 'customize-controls' ) );
	}
}
