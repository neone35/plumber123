<?php

/**
 * A color conversions class. Of course, you really spell it colour. Color conversion based on algorithms form EasyRGB <http://www.easyrgb.com/>.
 *
 * @author Greg Priday <greg@siteorigin.com>
 * @copyright Copyright (c) 2011, Greg Priday
 * @license GPL <http://www.gnu.org/copyleft/gpl.html>
 */
class SiteOrigin_Settings_Color_Object extends SiteOrigin_Settings_Color{
	private $changed;

	/**
	 * The hex value of this color before it was varied.
	 */
	private $color;
	private $type;

	const COLOR_HSV = 'hsv';
	const COLOR_RGB = 'rgb';
	const COLOR_LAB = 'lab';

	const COLOR_GREY = 'grey';
	const COLOR_HEX = 'hex';

	const COLOR_RGB_R = 'red';
	const COLOR_RGB_G = 'green';
	const COLOR_RGB_B = 'blue';

	const COLOR_LAB_L = 'lum';
	const COLOR_LAB_A = 'a';
	const COLOR_LAB_B = 'b';

	const COLOR_HSV_H = 'hue';
	const COLOR_HSV_S = 'sat';
	const COLOR_HSV_V = 'val';

	function __construct($color, $type = self::COLOR_HEX){
		if($type == self::COLOR_HEX){
			$this->type = self::COLOR_RGB;
			$this->color = self::hex2rgb($color);
		}
		elseif(is_numeric($color) && $type == self::COLOR_GREY){
			// We're going to assume this is a greyscale color
			$this->type = self::COLOR_HSV;
			$this->color = array(1,0,min(max($color,0),1));
		}
		elseif($type == self::COLOR_GREY){
			if(!is_int($color)) throw Exception('Invalid color');
			$this->type = self::COLOR_RGB;
			$this->color = array($color,$color,$color);
		}
		else{
			$this->color = $color;
			$this->type = $type;
		}

		$this->changed = array();
	}

	/**
	 * Get a color or color part
	 */
	public function __get($name)
	{
		$colors = array(
			self::COLOR_HSV => array(self::COLOR_HSV_H, self::COLOR_HSV_S, self::COLOR_HSV_V),
			self::COLOR_RGB => array(self::COLOR_RGB_R, self::COLOR_RGB_G, self::COLOR_RGB_B),
			self::COLOR_LAB => array(self::COLOR_LAB_L, self::COLOR_LAB_A, self::COLOR_LAB_B)
		);

		if($name == 'hex') {
			return self::rgb2hex($this->rgb);
		}
		elseif(in_array($name, array_keys($colors))){
			// We need a color array
			if($name == $this->type) return $this->color;
			else{
				$func = $this->type.'2'.$name;
				return call_user_func(array($this,$func), $this->color);
			}
		}
		else{
			// We need an individual color element
			foreach($colors as $type => $parts){
				if(in_array($name, $parts)){
					$color = $this->{$type};
					$i = array_search($name, $parts);
					return $color[$i];
				}
			}
		}

	}

	/**
	 * Set a color or color part.
	 *
	 * @param $name
	 * @param $value
	 */

	public function __set($name, $value)
	{
		$this->changed[] = $name;

		$colors = array(
			self::COLOR_HSV => array(self::COLOR_HSV_H, self::COLOR_HSV_S, self::COLOR_HSV_V),
			self::COLOR_RGB => array(self::COLOR_RGB_R, self::COLOR_RGB_G, self::COLOR_RGB_B),
			self::COLOR_LAB => array(self::COLOR_LAB_L, self::COLOR_LAB_A, self::COLOR_LAB_B)
		);

		if($name == 'hex'){
			$this->type = 'rgb';
			$this->color = self::hex2rgb($value);
		}
		elseif(in_array($name, array_keys($colors))){
			$this->type = $name;
			$this->color = $value;
		}
		else{
			foreach($colors as $type => $parts){
				if(in_array($name, $parts)){
					$color = $this->{$type};
					$i = array_search($name, $parts);
					$color[$i] = $value;

					$this->type = $type;
					$this->color = $color;
				}
			}
		}
	}

	/**
	 * @return array
	 */
	public function get_changed(){
		return $this->changed;
	}

	public function __toString() {
		return $this->hex;
	}

	/**
	 * Calculates the percieved difference between 2 colors.
	 *
	 * @param $c1
	 * @param $c2
	 *
	 * @return float Distance between the 2 colors
	 */
	public static function distance(SiteOrigin_Settings_Color_Object $c1, SiteOrigin_Settings_Color_Object $c2){
		return sqrt(
			pow($c1->lab[0]-$c2->lab[0],2) +
			pow($c1->lab[1]-$c2->lab[1],2) +
			pow($c1->lab[2]-$c2->lab[2],2)
		);
	}
}
