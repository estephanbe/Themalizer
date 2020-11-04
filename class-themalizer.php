<?php
/**
 * Themalizer Class
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

use Themalizer\Core\Engine as Engine;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}


/**
 * Provides direct access to all methods in the framework through static calls
 */
class Themalizer extends Engine {



	/** ================ INITIALIZATIONS ================ */


	/**
	 * Register a new Init class in $GLOBALS and overrides the previous
	 *
	 * @param array $args the initialization params.
	 * @return void Initialization object.
	 */
	public static function init( $args = array() ) {
		self::initialize_theme( $args );
	}

	/**
	 * Create Settings Page.
	 *
	 * @param array $args the settings arguments.
	 * @return void
	 */
	public static function setting( $args ) {
		self::initialize_setting_page( $args );
	}

	/**
	 * Get setting page from the container
	 *
	 * @param string $setting_id
	 * @param boolean $all
	 * @return void
	 */
	public static function get_setting( $setting_id, $all = false ) {
		self::check_framework();
		if ( $all ) {
			return self::get_container()->settings;
		}
		return self::get_container()->settings[ $setting_id ];
	}

	/**
	 * Create sharing buttons.
	 *
	 * @param array $linking_platforms the sharing arguments.
	 * @return array The sharing array.
	 */
	public static function sharing( $linking_platforms ) {
		return self::initialize_sharing( $linking_platforms );
	}

	public static function get_sharing( $sharing_id, $all = false ) {
		self::check_framework();
		if ( $all ) {
			return self::get_container()->sharing;
		}
		return self::get_container()->sharing[ $sharing_id ];
	}

	/**
	 * Customizer
	 *
	 * @param array $linking_platforms the customizer arguments.
	 * @return array The customizer array.
	 */
	public static function customizer( $customizer_name, $init, $args ) {
		return self::initialize_customizer( $customizer_name, $init, $args );
	}

	public static function get_customizer( $name, $all = false ) {
		self::check_framework();
		if ( $all ) {
			return self::get_container()->customizer;
		}
		return $GLOBALS['BoshDev\Themalizer']->customizer[ $name ];
	}

	/** ================ HELPERS ================ */

	/**
	 * Generate full URI to the given path.
	 *
	 * @param string  $path the path to be appended to the assets URI.
	 * @param boolean $echo switch to echo the path or return it as it is.
	 * @return string   return the path if the switch is False.
	 */
	public static function make_assets_uri( $path, $echo = true ) {
		if ( $echo ) {
			echo self::bod_html_url_sanitization( $GLOBALS['BoshDev\Themalizer']->init->get( 'assets_dir_uri' ) . $path ); // phpcs:ignore
		} else {
			return self::bod_html_url_sanitization( $GLOBALS['BoshDev\Themalizer']->init->get( 'assets_dir_uri' ) . $path );
		}
	}

	/**
	 * Get all the registered menus with their locations.
	 *
	 * @return array
	 */
	public static function get_menus_locations() {
		$nav_menus = array_merge( array( 'primary' => 'Header Menu' ), $GLOBALS['BoshDev\Themalizer']->init->get( 'nav_menus' ) );
		$locations = array();
		foreach ( $nav_menus as $location => $desc ) {
			array_push( $locations, $location );
		}
		return $locations;
	}




}
