<?php

/**
 * Connector Class
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace Themalizer\Core;

if (!defined('ABSPATH')) {
	exit('You are not allowed to get here, TINKY WINKY!!'); // Exit if accessed directly.
}

/** Traits */

use Themalizer\Helper\Tests;
use Themalizer\Helper\Sanitizers;

/**
 * Provides general methods and calls used internally in all classes in the framework.
 */
class Connector
{

	use Tests;
	use Sanitizers;

	/**
	 * The theme panel id.
	 *
	 * @var string
	 */
	public static $panel_id = '';

	/**
	 * The theme text-domain.
	 *
	 * @var string
	 */
	public static $text_domain = '';

	/**
	 * The theme prefix.
	 *
	 * @var string
	 */
	public static $prefix = '';

	/**
	 * The theme script name/id.
	 *
	 * @var string
	 */
	public static $script_name = '';

	/**
	 * The theme navagation menus.
	 *
	 * @var array
	 */
	public static $nav_menus = array();

	/**
	 * Get the global object of the framework.
	 *
	 * @return object Themalizer container in $GLOBALS.
	 */
	public static function container()
	{
		return $GLOBALS['BoshDev\Themalizer'];
	}

	/**
	 * Checks if the framework is loaded or not.
	 *
	 * @return void
	 */
	public static function check_framework()
	{
		self::isset_test(self::container(), 'You didn\'t initialize Themalizer framework');
	}

	/**
	 * Generate full URI to the given path for assets directory.
	 *
	 * @param string  $path the path to be appended to the assets URI.
	 * @param boolean $echo switch to echo the path or return it as it is.
	 * @return string   return the path if the switch is False.
	 */
	public static function make_assets_uri($path = '', $echo = true)
	{
		if ($echo) {
			echo self::html_url_sanitization(self::container()->init->get_property('assets_dir_uri') . $path); // phpcs:ignore
		} else {
			return self::html_url_sanitization(self::container()->init->get_property('assets_dir_uri') . $path);
		}
	}

	/**
	 * Generate full URI to the given path.
	 *
	 * @param string  $path the path to be appended to the assets URI.
	 * @param boolean $echo switch to echo the path or return it as it is.
	 * @return string   return the path if the switch is False.
	 */
	public static function make_dir_uri($path = '', $echo = true)
	{
		if ($echo) {
			echo self::html_url_sanitization(self::container()->init->get_property('dir_uri') . $path); // phpcs:ignore
		} else {
			return self::html_url_sanitization(self::container()->init->get_property('dir_uri') . $path);
		}
	}

	public static function mailchimp_action_url($echo = true)
	{
		$url = \esc_url(\get_rest_url() . THEMALIZER_REST_API_NAMESPACE . THEMALIZER_REST_API_MAILCHIMP_ENDPOINT);
		if ($echo) {
			echo $url;
		}
		return $url;
	}

	public static function recursive_iterator(array $arr): \RecursiveIteratorIterator
	{
		$arrayIterator = new \RecursiveArrayIterator($arr);
		return new \RecursiveIteratorIterator($arrayIterator, \RecursiveIteratorIterator::SELF_FIRST);
	}

	public static function get_env(string $env)
	{
		try {
			if (!isset($_ENV[$env])) {
				throw new \Exception("$env variable was not set!");
			}
			return $_ENV[$env];
		} catch (\Exception $err) {
			echo "<table>{$err->xdebug_message}</table>";
			die;
		}
	}
}
