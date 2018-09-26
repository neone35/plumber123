<?php
/**
 * Some color classes to help the widgets class
 *
 * @license GPL 2.0
 * @author Greg Priday
 */


/**
 * This is a very simple color conversion class. It just offers some static function for conversions.
 */
class SiteOrigin_Settings_Color {
	/**
	 * @param mixed $input A color representation.
	 *
	 * @return array An RGB array.
	 */
	public static function rgb($input){
		if(is_array($input)) return $input;
		elseif(is_float($input)) $input = 255*$input;

		return array($input,$input,$input);
	}

	public static function hex2rgb($hex) {
		$hex = (string) $hex;
		if(!is_string($hex) || $hex[0] != '#') throw new Exception('Invalid hex color ['.$hex.']');
		$hex = preg_replace("/[^0-9A-Fa-f]/", '', $hex); // Gets a proper hex string
		$rgb = array();

		if (strlen($hex) == 6) { //If a proper hex code, convert using bitwise operation. No overhead... faster
			$color_val = hexdec($hex);
			$rgb[0] = 0xFF & ($color_val >> 0x10);
			$rgb[1] = 0xFF & ($color_val >> 0x8);
			$rgb[2] = 0xFF & $color_val;
		}
		elseif (strlen($hex) == 3) { //if shorthand notation, need some string manipulations
			$rgb[0] = hexdec(str_repeat(substr($hex, 0, 1), 2));
			$rgb[1] = hexdec(str_repeat(substr($hex, 1, 1), 2));
			$rgb[2] = hexdec(str_repeat(substr($hex, 2, 1), 2));
		}
		else {
			throw new Exception('Invalid hex color');
		}

		foreach($rgb as $i => $p) $rgb[$i] = self::maxmin(round($p),0,255);
		return $rgb;
	}

	/**
	 * Convert RGB to HEX
	 */
	public static function rgb2hex($rgb){
		$hex = '#';
		foreach($rgb as $p){
			$p = base_convert($p,10,16);
			$p = str_pad($p,2,'0',STR_PAD_LEFT);
			$hex .= $p;
		}
		return strtoupper($hex);
	}

	/**
	 * Convert a HSV color to an RGB color.
	 *
	 * @param array $hsv HSV array with values 0-1
	 *
	 * @return array
	 */
	public static function hsv2rgb ($hsv)
	{
		// The return RGB value
		$rgb = array();

		if($hsv[1] == 0){
			$rgb = array_fill(0,3,$hsv[2] * 255);
		}
		else{
			// Break hue into 6 possible segments
			$hue = $hsv[0] * 6;
			$hue_range = floor( $hue );

			$v = array(
				$hsv[2] * ( 1 - $hsv[1] ),
				$hsv[2] * ( 1 - $hsv[1] * ( $hue - $hue_range ) ),
				$hsv[2] * (1 - $hsv[1] * (1 - ($hue-$hue_range)))
			);

			switch($hue_range){
				case 0:
					$rgb[0] = $hsv[2]; $rgb[1] = $v[2]; $rgb[2] = $v[0];
					break;
				case 1:
					$rgb[0] = $v[1]; $rgb[1] = $hsv[2]; $rgb[2] = $v[0];
					break;
				case 2:
					$rgb[0] = $v[0]; $rgb[1] = $hsv[2]; $rgb[2] = $v[2];
					break;
				case 3:
					$rgb[0] = $v[0]; $rgb[1] = $v[1]; $rgb[2] = $hsv[2];
					break;
				case 4:
					$rgb[0] = $v[2]; $rgb[1] = $v[0]; $rgb[2] = $hsv[2];
					break;
				default :
					$rgb[0] = $hsv[2]; $rgb[1] = $v[0]; $rgb[2] = $v[1];
					break;
			}

			$rgb[0] = round($rgb[0] * 255);
			$rgb[1] = round($rgb[1] * 255);
			$rgb[2] = round($rgb[2] * 255);
		}

		// Make sure the parts are in the proper range
		foreach($rgb as $i => $p) $rgb[$i] = self::maxmin(round($p),0,255);
		return $rgb;
	}

	/**
	 * Converts an RGB color to an XYZ color.
	 *
	 * @param array $color The input color. Values from 0-255.
	 *
	 * @return array
	 */
	public static function rgb2xyz(array $rgb)
	{
		foreach($rgb as $i => $c) $rgb[$i] /= 255;

		foreach($rgb as $i => $c){
			if ($c > 0.04045){ $rgb[$i] = pow(($c + 0.055) / 1.055, 2.4); }
			else { $rgb[$i] = $c / 12.92; }

			$rgb[$i] = $rgb[$i] * 100;
		}

		//Observer. = 2¡, Illuminant = D65
		$xyz = array(0,0,0);
		$xyz[0] = $rgb[0] * 0.4124 + $rgb[1] * 0.3576 + $rgb[2] * 0.1805;
		$xyz[1] = $rgb[0] * 0.2126 + $rgb[1] * 0.7152 + $rgb[2] * 0.0722;
		$xyz[2] = $rgb[0] * 0.0193 + $rgb[1] * 0.1192 + $rgb[2] * 0.9505;

		return $xyz;
	}

	/**
	 * Convert a RGB color to a HSV color
	 *
	 * @param array $rgb RGB array with values 0-255
	 *
	 * @return array
	 */
	public static function rgb2hsv ($rgb)
	{
		$rgb = self::rgb($rgb);

		$rgb[0] = ($rgb[0] / 255);
		$rgb[1] = ($rgb[1] / 255);
		$rgb[2] = ($rgb[2] / 255);

		$min = min($rgb[0], $rgb[1], $rgb[2]);
		$max = max($rgb[0], $rgb[1], $rgb[2]);
		$del_max = $max - $min;

		$hsv = array(0,0,$max);

		if ($del_max != 0){
			$hsv[1] = $del_max / $max;

			$del_r = ( ( ( $del_max - $rgb[0] ) / 6 ) + ( $del_max / 2 ) ) / $del_max;
			$del_g = ( ( ( $del_max - $rgb[1] ) / 6 ) + ( $del_max / 2 ) ) / $del_max;
			$del_b = ( ( ( $del_max - $rgb[2] ) / 6 ) + ( $del_max / 2 ) ) / $del_max;

			if ($rgb[0] == $max) $hsv[0] = $del_b - $del_g;
			else if ($rgb[1] == $max) $hsv[0] = ( 1 / 3 ) + $del_r - $del_b;
			else if ($rgb[2] == $max) $hsv[0] = ( 2 / 3 ) + $del_g - $del_r;

			if ($hsv[0] < 0) $hsv[0]++;
			if ($hsv[0] > 1) $hsv[0]--;
		}

		return $hsv;
	}

	/**
	 * Converts a LAB color into RGB
	 */
	public static function lab2xyz(array $lab)
	{
		foreach($lab as $i => $c) $lab[$i] *= 100;

		// Observer= 2¡, Illuminant= D65
		$REF_X = 95.047;
		$REF_Y = 100.000;
		$REF_Z = 108.883;

		$xyz = array();

		$xyz[1] = ($lab[0] + 16) / 116;
		$xyz[0] = $lab[1] / 500 + $xyz[1];
		$xyz[2] = $xyz[1] - $lab[2] / 200;

		foreach($xyz as $i => $c){
			if ( pow( $c , 3 ) > 0.008856 ) { $xyz[$i] = pow( $c , 3 ); }
			else { $xyz[$i] = ( $c - 16 / 116 ) / 7.787; }
		}

		$xyz[0] *= $REF_X;
		$xyz[1] *= $REF_Y;
		$xyz[2] *= $REF_Z;

		return $xyz;
	}


	/**
	 * Convert XYZ color to a LAB color
	 */
	public static function xyz2lab(array $xyz)
	{
		// Observer= 2¡, Illuminant= D65
		$REF_X = 95.047;
		$REF_Y = 100.000;
		$REF_Z = 108.883;

		$xyz[0] = $xyz[0] / $REF_X;
		$xyz[1] = $xyz[1] / $REF_Y;
		$xyz[2] = $xyz[2] / $REF_Z;

		foreach($xyz as $i => $c){
			if ($c > 0.008856 ) { $xyz[$i] = pow( $c , 1/3 ); }
			else { $xyz[$i] = ( 7.787 * $c ) + ( 16/116 ); }
		}

		$lab = array();
		$lab[0] = ( 116 * $xyz[1] ) - 16;
		$lab[1] = 500 * ( $xyz[0] - $xyz[1] );
		$lab[2] = 200 * ( $xyz[1] - $xyz[2] );

		foreach($lab as $i => $c) $lab[$i] /= 100;

		return $lab;
	}

	/**
	 * Convert an XYZ color to an RGB color
	 */
	public static function xyz2rgb($xyz)
	{
		// (Observer = 2¡, Illuminant = D65)
		$xyz[0] /= 100; //X from 0 to  95.047
		$xyz[1] /= 100; //Y from 0 to 100.000
		$xyz[2] /= 100; //Z from 0 to 108.883

		$rgb = array();

		$rgb[0] = $xyz[0] * 3.2406 + $xyz[1] * -1.5372 + $xyz[2] * -0.4986;
		$rgb[1] = $xyz[0] * -0.9689 + $xyz[1] * 1.8758 + $xyz[2] * 0.0415;
		$rgb[2] = $xyz[0] * 0.0557 + $xyz[1] * -0.2040 + $xyz[2] * 1.0570;

		foreach($rgb as $i => $c){
			if ( $c > 0.0031308 ) { $rgb[$i] = 1.055 * pow( $c , ( 1 / 2.4 ) ) - 0.055; }
			else { $rgb[$i] = 12.92 * $c; }
		}

		$rgb[0] = round(min(max($rgb[0],0),1) * 255);
		$rgb[1] = round(min(max($rgb[1],0),1) * 255);
		$rgb[2] = round(min(max($rgb[2],0),1) * 255);

		return $rgb;
	}

	// Combine the primary functions to create all 6 conversion functions

	/**
	 * Convert an RGB color to a LAB color.
	 */
	public static function rgb2lab($rgb)
	{
		$xyx = self::rgb2xyz(self::rgb($rgb));
		return self::xyz2lab($xyx);
	}

	/**
	 * Convert a LAB color to a
	 */
	public static function lab2rgb($lab)
	{
		$xyx = self::lab2xyz($lab);
		return self::xyz2rgb($xyx);
	}

	/**
	 * Convert a LAB color to HSV
	 */
	public static function lab2hsv($lab)
	{
		$rgb = self::lab2rgb($lab);
		return self::rgb2hsv($rgb);
	}

	/**
	 * Convert an HSV color to LAB
	 */
	public static function hsv2lab($hsv)
	{
		$rgb = self::hsv2rgb($hsv);
		return self::rgb2lab($rgb);
	}

	/**
	 * Makes sure that the given value falls inside a range.
	 */
	public static function maxmin($i, $min, $max){
		return min(max($i,$min),$max);
	}

	public static function float2hex($float){
		$hsv = array(
			0,
			0,
			$float
		);

		return self::rgb2hex(self::hsv2rgb($hsv));
	}
}