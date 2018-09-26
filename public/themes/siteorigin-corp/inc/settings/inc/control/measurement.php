<?php

class SiteOrigin_Settings_Control_Measurement extends WP_Customize_Control {

	public $type = 'siteorigin-measurement';

	static $measurements = array(
		'px',
		'%',
		'in',
		'cm',
		'mm',
		'em',
		'ex',
		'pt',
		'pc',
		'rem'
	);

	public function render_content( ){
		$value = $this->value();

		if ( ! empty( $this->label ) ) {
			?><span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span><?php
		}
		if ( ! empty( $this->description ) ) {
			?><span class="description customize-control-description"><?php echo $this->description; ?></span><?php
		}

		list( $amount, $measurement ) = self::sanitize_value( $value, false );
		?>
		<div class="measurement-fields">
			<input type="text" class="amount" value="<?php echo esc_attr( $amount ) ?>" />
			<select class="measurement">
				<?php foreach ( self::$measurements as $m ):?>
					<option value="<?php echo esc_html( $m ) ?>" <?php selected( $m, $measurement ) ?>><?php echo esc_html( $m ) ?></option>
				<?php endforeach?>
			</select>
		</div>
		<?php

	}

	static function sanitize_value( $value, $join = true ) {
		$amount = '';
		$measurement = '';

		if( ! empty( $value ) ) {
			$measurements = array_map('preg_quote', self::$measurements );
			if( preg_match( '/(-?[0-9\.,]+).*?(' . implode('|', $measurements) . ')/', $value, $match ) ) {
				$amount = $match[1];
				$measurement = $match[2];
			}
		}

		return $join ? ( $amount . $measurement ) : array( $amount, $measurement );
	}

	public function enqueue() {
		wp_enqueue_script( 'siteorigin-settings-measurement-control', get_template_directory_uri() . '/inc/settings/js/control/measurement-control' . SITEORIGIN_THEME_JS_PREFIX . '.js', array( 'jquery', 'customize-controls' ) );
		wp_enqueue_style( 'siteorigin-settings-measurement-control', get_template_directory_uri() . '/inc/settings/css/control/measurement-control.css', array() );
	}

}