<?php


class SiteOrigin_Settings_Webfont_Manager {

	private $fonts;

	function __construct(){
		$this->fonts = array();

		add_action( 'wp_enqueue_scripts', array($this, 'enqueue') );
	}

	static function single() {
		static $single;

		if( empty($single) ) {
			$single = new SiteOrigin_Settings_Webfont_Manager();
		}
		return $single;
	}

	function add_font( $name, $weights = array() ) {
		if( empty( $this->fonts[$name] ) ) {
			$this->fonts[$name] = $weights;
		}
		else {
			$this->fonts[$name] = array_merge( $this->fonts[$name], $weights );
			$this->fonts[$name] = array_unique( $this->fonts[$name] );
		}
	}

	function remove_font( $name ){
		unset( $this->fonts[$name] );
	}

	function enqueue() {
		$default_font_settings = apply_filters( 'siteorigin_settings_font_settings', array() );
		if( !empty($default_font_settings) ) {
			$settings = SiteOrigin_Settings::single();
			foreach( $default_font_settings as $setting => $webfont ) {
				$value = json_decode( $settings->get( $setting ), true );

				if( empty($value) || empty($value['font']) ) {
					// We need to enqueue the default fonts
					$this->add_font( $webfont['name'], $webfont['weights'] );
				}
			}
		}

		if( empty( $this->fonts ) ) return;

		$family = array();
		foreach($this->fonts as $name => $weights) {

			if( !empty($weights) ) {
				$family[] = $name . ':' . implode(',', $weights);
			}
			else {
				$family[] = $name;
			}
		}

		wp_enqueue_style(
			'siteorigin-google-web-fonts',
			add_query_arg('family', implode( '|', $family ), '//fonts.googleapis.com/css')
		);
	}

}
SiteOrigin_Settings_Webfont_Manager::single();