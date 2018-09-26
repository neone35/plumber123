<?php

class SiteOrigin_Settings_CSS_Functions {

	function __construct(){
	}

	static function single(){
		static $single;
		if( empty( $single ) ) {
			$single = new self();
		}

		return $single;
	}

	function font( $match ) {
		if( empty($match[2]) ) return '';

		$return = '';
		$args = json_decode( trim($match[2]), true );
		if( empty($args['font']) ) {
			return '';
		}

		if( $args['webfont'] ) {
			// We need to import this too
			$query = add_query_arg(array(
				'family' => rawurlencode( $args['font'] ) . ':' . rawurlencode( $args['variant'] ),
				'subset' => rawurlencode( $args['subset'] )
			), '//fonts.googleapis.com/css');
			$return .= '@import url(' . $query . '); ';
		}

		// Now lets add all the css styling
		$return .= 'font-family: "' . esc_attr( $args['font'] ) . '", ' . $args['category'] . '; ';
		if( strpos( $args['variant'], 'italic' ) !== false ) {
			$weight = str_replace('italic', '', $args['variant']);
			$return .= 'font-style: italic; ';
		}
		else {
			$weight = $args['variant'];
		}

		if( empty( $weight ) ) $weight = 'normal';
		if( $weight == 'regular' ) $weight = 'normal';
		$return .= 'font-weight: ' . esc_attr( $weight ) . '; ';

		return $return;
	}

	function rgba( $match ) {
		if( empty( $match[2] ) ) return '';
		$args = explode( ',', $match[2] );
		$rgb = SiteOrigin_Settings_Color::hex2rgb( trim( $args[0] ) );

		return 'rgba(' . implode( ',', array_merge( $rgb, array( floatval( $args[1] ) ) ) ) . ')';
	}

	function lighten( $match ) {
		$args = explode( ',', $match[2] );
		$rgb = SiteOrigin_Settings_Color::hex2rgb( trim( $args[0] ) );
		$hsv = SiteOrigin_Settings_Color::rgb2hsv( $rgb );
		if( strpos( $args[1], '%' ) !== false ) {
			$percent = intval( trim($args[1]) ) / 100;
		} else {
			$percent = floatval( trim($args[1]) );
		}

		$hsv[2] += $percent;
		return SiteOrigin_Settings_Color::rgb2hex( SiteOrigin_Settings_Color::hsv2rgb( $hsv ) );
	}

	function darken( $match ) {
		$args = explode( ',', $match[2] );
		$rgb = SiteOrigin_Settings_Color::hex2rgb( trim( $args[0] ) );
		$hsv = SiteOrigin_Settings_Color::rgb2hsv( $rgb );
		if( strpos( $args[1], '%' ) !== false ) {
			$percent = intval( trim($args[1]) ) / 100;
		} else {
			$percent = floatval( trim($args[1]) );
		}

		$hsv[2] -= $percent;
		return SiteOrigin_Settings_Color::rgb2hex( SiteOrigin_Settings_Color::hsv2rgb( $hsv ) );
	}

}
