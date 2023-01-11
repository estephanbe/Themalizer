<?php

/**
 * Constants Class
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace  Themalizer\Core;

if (!defined('ABSPATH')) {
	exit('You are not allowed to get here, TINKY WINKY!!'); // Exit if accessed directly.
}

/**
 * Provides direct access to all methods in the framework through static calls
 */
class Constants
{

	/**
	 * Define constants.
	 */
	public function __construct()
	{

		$this->set_env();

		define('THEMALIZER_REST_API_NAMESPACE', 'themalizer/v1');

		/** MailChimp */
		define('THEMALIZER_REST_API_MAILCHIMP_ENDPOINT', 'mailchimp');
		define('THEMALIZER_MAILCHIMP_MENU_SLUG', 'themalizer_mailchimp_settings');
		define('THEMALIZER_MAILCHIMP_API_KEY_OPTION_NAME', 'themalizer_plugin_mail_chimp_api_key');
		define('THEMALIZER_MAILCHIMP_LIST_ID_OPTION_NAME', 'themalizer_plugin_mail_chimp_list_id');

		/** Constents to be defined */
		// Init::define( 'THEMALIZER_THEME_PREFIX' );.
		// Init::define( 'THEMALIZER_STYLE_NAME',  );.
		// Init::define( 'THEMALIZER_SCRIPT_NAME',  );.
		// Init::define( 'THEMALIZER_THEME_VERSION',  );.
		// Init::define( 'THEMALIZER_ENQ_PRIORITY', 100 );.
	}

	private function set_env()
	{
		$path = \dirname(__DIR__, 2) . "/.env";
		if (!file_exists($path)) {
			throw new \InvalidArgumentException(sprintf('%s does not exist', \dirname(".env", 2)));
			die;
		}

		if (!is_readable($path)) {
			throw new \RuntimeException(sprintf('%s file is not readable', $path));
			die;
		}

		$lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
		foreach ($lines as $line) {

			if (strpos(trim($line), '#') === 0) {
				continue;
			}

			list($name, $value) = explode('=', $line, 2);
			$name = trim($name);
			$value = trim($value);

			if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
				putenv(sprintf('%s=%s', $name, $value));
				$_ENV[$name] = $value;
				$_SERVER[$name] = $value;
			}
		}
	}
}
