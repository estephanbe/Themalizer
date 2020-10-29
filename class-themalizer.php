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

namespace BoshDev;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

use BoshDev\Themalizer\Core\Engine as Engine;

/**
 * Provides direct access to all methods in the framework through static calls
 */
class Themalizer extends Engine {


	/**
	 * Return property value
	 *
	 * @param String $input the property which it will be called.
	 * @return mixed returned value the callback
	 */
	public static function get( $input ) {
		self::isset_test( $GLOBALS['BoshDev\Themalizer'], 'You didn\'t initialize Themalizer' );

		// Case should be used here when calling properties of multiple classes.
		return $GLOBALS['BoshDev\Themalizer']->init->get( $input );
	}

	/**
	 * Register a new Init class in $GLOBALS and overrides the previous
	 *
	 * @param [type] $args the initialization params.
	 * @return object
	 */
	public static function init( $args = array() ) {
		self::isset_test( $GLOBALS['BoshDev\Themalizer'], 'You didn\'t initialize Themalizer framework' ); // Check if the auto run file was included.

		$GLOBALS['BoshDev\Themalizer']->init = new Initialization( $args ); // phpcs:ignore

		return $GLOBALS['BoshDev\Themalizer']->init;
	}

	/**
	 * Create Settings Page.
	 *
	 * @param array $args the settings arguments.
	 * @return array The settings array.
	 */
	public static function setting( $args, $return_all = false ) {
		self::isset_test( $GLOBALS['BoshDev\Themalizer'], 'You didn\'t initialize Themalizer framework' ); // Check if the auto run file was included.
		self::empty_test( $args, 'Please fill out the settings arguments array.' );
		if ( ! isset( $GLOBALS['BoshDev\Themalizer']->settings ) ) {
			$GLOBALS['BoshDev\Themalizer']->settings = array();
		}

		$new_setting_page = new Setting( $args );
		array_push( $GLOBALS['BoshDev\Themalizer']->settings, $new_setting_page );

		if ( $return_all ) {
			return $GLOBALS['BoshDev\Themalizer']->settings;
		} else {
			return $new_setting_page;
		}

	}

	/**
	 * Create sharing buttons.
	 *
	 * @param array $linking_platforms the sharing arguments.
	 * @return array The sharing array.
	 */
	public static function sharing( $linking_platforms ) {
		self::isset_test( $GLOBALS['BoshDev\Themalizer'], 'You didn\'t initialize Themalizer framework' ); // Check if the auto run file was included.
		self::empty_test( $linking_platforms, 'Please fill out the sharing arguments array.' );

		if ( ! isset( $GLOBALS['BoshDev\Themalizer']->sharing ) ) {
			$GLOBALS['BoshDev\Themalizer']->sharing = array();
		}

		array_push( $GLOBALS['BoshDev\Themalizer']->sharing, new Sharing( $linking_platforms ) );

		return $GLOBALS['BoshDev\Themalizer']->sharing;
	}

	public static function get_sharing() {
		return $GLOBALS['BoshDev\Themalizer']->sharing;
	}

	/**
	 * Customizer
	 *
	 * @param array $linking_platforms the customizer arguments.
	 * @return array The customizer array.
	 */
	public static function customizer( $customizer_name, $first, $second, $return_all = false ) {
		self::isset_test( $GLOBALS['BoshDev\Themalizer'], 'You didn\'t initialize Themalizer framework' ); // Check if the auto run file was included.

		if ( ! isset( $GLOBALS['BoshDev\Themalizer']->customizer ) ) {
			$GLOBALS['BoshDev\Themalizer']->customizer = array();
		}

		$new_customizer                      = new Customizer( $first, $second );
		$GLOBALS['BoshDev\Themalizer']
			->customizer[ $customizer_name ] = $new_customizer;

		if ( $return_all ) {
			return $GLOBALS['BoshDev\Themalizer']->customizer;
		} else {
			return $new_customizer;
		}

	}

	public static function get_customizer( $name ) {
		return $GLOBALS['BoshDev\Themalizer']->customizer[ $name ];
	}

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
