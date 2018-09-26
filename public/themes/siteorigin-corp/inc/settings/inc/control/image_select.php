<?php

class SiteOrigin_Settings_Control_Image_Select extends WP_Customize_Control {
	public $type = 'siteorigin-image-select';
	public $choices;

	function render_content() {
		if ( ! empty( $this->label ) ) {
			?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
		}
		if ( ! empty( $this->description ) ) {
			?><span class="description customize-control-description"><?php echo $this->description; ?></span><?php
		}

		?>
		<select <?php $this->link(); ?>>
			<?php foreach( $this->choices as $key => $choice ) : ?>
				<option value="<?php echo esc_attr($key) ?>" data-image="<?php echo esc_url( $choice[1] ) ?>" <?php selected( $key, $this->value() ) ?>>
					<?php echo esc_html($choice[0]) ?>
				</option>
			<?php endforeach ?>
		</select>

		<ul class="image-options">
			<?php foreach( $this->choices as $key => $choice ) : ?>
				<li data-key="<?php echo esc_attr($key) ?>" <?php if( $key == $this->value() ) echo 'class="active"' ?>>
					<label><?php echo esc_html($choice[0]) ?></label>
					<img src="<?php echo esc_url( $choice[1] ) ?>" />
				</li>
			<?php endforeach ?>
		</ul>

		<?php
	}

	public function enqueue() {
		wp_enqueue_script( 'siteorigin-settings-image-select-control', get_template_directory_uri() . '/inc/settings/js/control/image-select-control' . SITEORIGIN_THEME_JS_PREFIX . '.js', array( 'jquery', 'customize-controls' ) );
		wp_enqueue_style( 'siteorigin-settings-image-select-control', get_template_directory_uri() . '/inc/settings/css/control/image-select-control.css', array() );
	}
}
