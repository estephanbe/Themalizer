<?php
/**
 * Sanitizers.php
 *
 * @package Themalizer
 */

namespace Themalizer\Helper;

/**
 * Sanitizing mehtods to sanitize outputs and inputs
 */
trait Sanitizers {

	/**
	 * Get Sanitization method to perform the sanitization according to the input and call the suitable sanitization method.
	 *
	 * @param string $type the sanitization type.
	 * @return string
	 */
	public static function get_sanitizing_method( $type ) {
		$method = '';
		switch ( $type ) {
			case 'text':
				$method = 'text_field_sanitization';
				break;

			case 'textarea':
				$method = 'textarea_sanitization';
				break;

			case 'url':
				$method = 'url_sanitization';
				break;

			case 'image':
				$method = 'image_sanitization';
				break;

			case 'color':
				$method = 'color_sanitization';
				break;

			default:
				break;
		}

		return $method;
	}

	// =============================================== Input sanitization

	/**
	 * Sanitize text field values.
	 * Input sanitization.
	 *
	 * @param string $input Input to be sanitized.
	 * @return string Sanitized string.
	 */
	public static function text_field_sanitization( $input ) {
		return sanitize_text_field( $input );
	}

	/**
	 * Sanitize textarea values.
	 * Input sanitization.
	 *
	 * @param string $input Input to be sanitized.
	 * @return string Sanitized string.
	 */
	public static function textarea_sanitization( $input ) {
		return sanitize_textarea_field( $input );
	}

	/**
	 * Sanitize url values.
	 * Input sanitization.
	 *
	 * @param string $input URL to be sanitized.
	 * @return string Sanitized URL.
	 */
	public static function url_sanitization( $input ) {
		return esc_url_raw( $input );
	}

	/**
	 * Sanitize image src values.
	 * Input sanitization.
	 *
	 * @param string $input Image source to be sanitized.
	 * @return string Sanitized image source.
	 */
	public static function image_sanitization( $input ) {
		return esc_url_raw( $input );
	}

	/**
	 * Sanitize color hex values.
	 * Input sanitization.
	 *
	 * @param string $input Hex color to be sanitized.
	 * @return string Sanitized hex color.
	 */
	public static function color_sanitization( $input ) {
		return sanitize_hex_color( $input );
	}

	// =============================================== Output sanitization

	/**
	 * Sanitize general HTML values.
	 * Output sanitization.
	 *
	 * @param string $html_value HTML to be sanitized.
	 * @return string Sanitized HTML.
	 */
	public static function html_sanitization( $html_value ) {
		return esc_html( $html_value );
	}

	/**
	 * Sanitize URL values.
	 * Output sanitization.
	 *
	 * @param string $url_value URL to be sanitized.
	 * @return string Sanitized URL.
	 */
	public static function html_url_sanitization( $url_value ) {
		return esc_url( $url_value );
	}

	/**
	 * Sanitize javascript values.
	 * Output sanitization.
	 *
	 * @param string $js_value JS to be sanitized.
	 * @return string Sanitized JS.
	 */
	public static function html_js_sanitization( $js_value ) {
		return esc_js( $js_value );
	}

	/**
	 * Sanitize HTML attributes.
	 * Output sanitization.
	 *
	 * @param string $attr_value attributes to be sanitized.
	 * @return string Sanitized attributes.
	 */
	public static function html_attr_sanitization( $attr_value ) {
		return esc_attr( $attr_value );
	}

	/**
	 * Sanitize textarea values.
	 * Output sanitization.
	 *
	 * @param string $textarea_value Textarea values to be sanitized.
	 * @return string Sanitized textarea values.
	 */
	public static function html_textarea_sanitization( $textarea_value ) {
		return esc_textarea( $textarea_value );
	}

	public static function html_int_sanitization ( $int ) {
		return absint( $int );
	}


} 
