<?php
/**
 * Class File - Security
 *
 * @package BoshDev
 */

namespace BoshDev\Core;

/**
 * Security Class:
 * Handles all the security issues
 */
class Security {

	/**
	 * Added in the beginning of each file to perform the necessary security actions
	 *
	 * @return void
	 */
	public function head_of_file() {
		if ( ! defined( 'ABSPATH' ) ) {
			exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
		}
	}

}

