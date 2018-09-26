<?php

class SiteOrigin_Settings_Sanitize {
	static function intval( $val ){
		return intval( $val );
	}

	/**
	 * Sanitize a measurement value.
	 *
	 * @param $val
	 *
	 * @return string
	 */
	static function measurement( $val ){
		$measurements = array_map('preg_quote', array(
			'px',
			'%',
			'in',
			'cm',
			'mm',
			'em',
			'ex',
			'pt',
			'pc',
		) );

		if (preg_match('/(-?[0-9\.,]+).*?(' . implode('|', $measurements) . ')/', $val, $match)) {
			$return = $match[1] . $match[2];
		}
		else {
			$return = '';
		}

		return $return;
	}

	/**
	 * Sanitize a widget value.
	 *
	 * @param $widget
	 *
	 * @return array|mixed|object
	 */
	static function widget( $widget ){
		if( is_string( $widget ) ) {
			$widget = json_decode( $widget );
		}

		if( !empty( $widget['panels_info']['widget']) && class_exists( $widget['panels_info']['widget'] ) ) {
			$the_widget = new $widget['panels_info']['widget'];
			if( is_a( $the_widget, 'WP_Widget' ) ) {
				$info = $widget['panels_info'];
				unset( $widget['panels_info'] );
				$widget = $the_widget->update( $widget, $widget );
				$widget['panels_info'] = $info;

			}
		}

		return $widget;
	}

	/**
	 * Sanitize a boolean value.
	 *
	 * @param $val
	 *
	 * @return bool
	 */
	static function boolean($val){
		return (bool) $val;
	}

	/**
	 * Sanitize a float value
	 *
	 * @param $val
	 *
	 * @return float
	 */
	static function floatval( $val ){
		return floatval( $val );
	}
}
