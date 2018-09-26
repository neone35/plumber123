<?php

/**
 * Helper Functions.
 *
 * @since        5.0.5
 * @package      Shortcodes_Ultimate
 * @subpackage   Shortcodes_Ultimate/includes
 */

/**
 * Retrieve the URL of the plugin directory (with trailing slash).
 *
 * @since  5.0.5
 * @return string The URL of the plugin directory (with trailing slash).
 */
function su_get_plugin_url() {
	return plugin_dir_url( SU_PLUGIN_FILE );
}

/**
 * Retrieve the filesystem path of the plugin directory (with trailing slash).
 *
 * @since  5.0.5
 * @return string The filesystem path of the plugin directory (with trailing slash).
 */
function su_get_plugin_path() {
	return plugin_dir_path( SU_PLUGIN_FILE );
}

/**
 * Get plugin config.
 *
 * @since  5.0.5
 * @param string  $key
 * @return mixed      Config data if found, Flase otherwise.
 */
function su_get_config( $key = null ) {

	static $config = array();

	if (
		empty( $key ) ||
		preg_match( "/^(?!-)[a-z0-9-_]+(?<!-)(\/(?!-)[a-z0-9-_]+(?<!-))*$/", $key ) !== 1
	) {
		return false;
	}

	if ( isset( $config[ $key ] ) ) {
		return $config[ $key ];
	}

	$config_file = su_get_plugin_path() . 'includes/config/' . $key . '.php';

	if ( ! file_exists( $config_file ) ) {
		return false;
	}

	return $config[ $key ] = require_once $config_file;

}

/**
 * Create an error message.
 *
 * @since  5.0.5
 * @param string  $title   Error title.
 * @param string  $message Error message.
 * @return string          Error message markup.
 */
function su_error_message( $title = '', $message = '' ) {

	if ( $title ) {
		$title = "<strong>${title}:</strong> ";
	}

	return sprintf(
		'<p class="su-error" style="padding:5px 10px;color:#ff685e;border-left:3px solid #ff685e;background:#fff">%1$s%2$s</p>',
		$title,
		$message
	);

}

/**
 * Validate filter callback name.
 *
 * @since  5.0.5
 * @param string  $filter Filter callback name.
 * @return boolean         True if filter name contains word 'filter', False otherwise.
 */
function su_is_filter_safe( $filter ) {
	return is_string( $filter ) && strpos( $filter, 'filter' ) !== false;
}

/**
 * Range converter.
 *
 * Converts string range like '1, 3-5, 10' into an array like [1, 3, 4, 5, 10].
 *
 * @since  5.0.5
 * @param string  $string Range string.
 * @return array          Parsed range.
 */
function su_parse_range( $string = '' ) {

	$parsed = array();

	foreach ( explode( ',', $string ) as $range ) {

		if ( strpos( $range, '-' ) === false ) {
			$parsed[] = intval( $range );
			continue;
		}

		$range = explode( '-', $range );

		if ( ! is_numeric( $range[0] ) ) {
			$range[0] = 0;
		}

		if ( ! is_numeric( $range[1] ) ) {
			$range[1] = 0;
		}

		foreach ( range( $range[0], $range[1] ) as $value ) {
			$parsed[] = $value;
		}

	}

	sort( $parsed );
	$parsed = array_unique( $parsed );

	return $parsed;

}

/**
 * Extract CSS class name(s) from shortcode $atts and prepend with a space.
 *
 * @since  5.0.5
 * @param array   $atts Shortcode atts.
 * @return string       Extra CSS class(es) prepended by a space.
 */
function su_get_css_class( $atts ) {
	return $atts['class'] ? ' ' . trim( $atts['class'] ) : '';
}

/**
 * Get shortcode prefix.
 *
 * @since  5.0.5
 * @return string Shortcode prefix.
 */
function su_get_shortcode_prefix() {
	return get_option( 'su_option_prefix' );
}

/**
 * Do shortcodes in attributes.
 *
 * Replace braces with square brackets: {shortcode} => [shortcode], applies do_shortcode() filter.
 *
 * @since  5.0.5
 * @param string  $value Attribute value with shortcodes.
 * @return string        Parsed string.
 */
function su_do_attribute( $value ) {

	$value = str_replace( array( '{', '}' ), array( '[', ']' ), $value );
	$value = do_shortcode( $value );

	return $value;

}

/**
 * Custom do_shortcode function for nested shortcodes
 *
 * @since  5.0.4
 * @param string  $content Shortcode content.
 * @param string  $pre     First shortcode letter.
 * @return string          Formatted content.
 */
function su_do_nested_shortcodes_alt( $content, $pre ) {

	if ( strpos( $content, '[_' ) !== false ) {
		$content = preg_replace( '@(\[_*)_(' . $pre . '|/)@', "$1$2", $content );
	}

	return do_shortcode( $content );

}

/**
 * Remove underscores from nested shortcodes.
 *
 * @since  5.0.4
 * @param string  $content   String with nested shortcodes.
 * @param string  $shortcode Shortcode tag name (without prefix).
 * @return string            Parsed string.
 */
function su_do_nested_shortcodes( $content, $shortcode ) {

	if ( get_option( 'su_option_do_nested_shortcodes_alt' ) ) {
		return su_do_nested_shortcodes_alt( $content, substr( $shortcode, 0, 1 ) );
	}

	$prefix = su_get_shortcode_prefix();

	if ( strpos( $content, '[_' . $prefix . $shortcode ) !== false ) {

		$content = str_replace(
			array( '[_' . $prefix . $shortcode, '[_/' . $prefix . $shortcode, ),
			array( '[' . $prefix . $shortcode,  '[/' . $prefix . $shortcode, ),
			$content
		);

	}

	else {
		$content = wptexturize( $content );
	}

	return do_shortcode( $content );

}
