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

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

/**
 * Provides direct access to all methods in the framework through static calls
 */
class Constants {

	/**
	 * Define constants.
	 */
	public function __construct() {

		// THEMALIZER_THEME_PREFIX will be defined in Init class.
		// THEMALIZER_STYLE_NAME will be defined in Init class.
		// THEMALIZER_SCRIPT_NAME will be defined in Init class.
		define( 'THEMALIZER_REST_API_NAMESPACE', 'themalizer/v1/' );

		/** MailChimp */
		define( 'THEMALIZER_REST_API_MAILCHIMP_ENDPOINT', 'mailchimp' );
		define( 'THEMALIZER_MAILCHIMP_MENU_SLUG', 'themalizer_mailchimp_settings' );
		define( 'THEMALIZER_MAILCHIMP_API_KEY_OPTION_NAME', 'themalizer_plugin_mail_chimp_api_key' );
		define( 'THEMALIZER_MAILCHIMP_LIST_ID_OPTION_NAME', 'themalizer_plugin_mail_chimp_list_id' );
	}
}
