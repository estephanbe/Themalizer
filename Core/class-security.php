<?php
/**
 * Class File - Security
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace Themalizer\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}
/**
 * Security Class:
 * Handles all the security issues
 */
class Security extends Engine {

	private $most_recent_php_version = '';
	private $server_php_version      = '';

	/**
	 * Constructor
	 */
	public function __construct() {
		$this->security_practices();

		if ( ! self::$development ) {
			$this->security_headers_and_ini_setups();
			$this->check_php_version();
		}

	}

	/**
	 * Write secured index.php file into every folder in this theme.
	 *
	 * @return void
	 */
	public function check_index_files() {

	}

	/**
	 * Run security functions for the theme.
	 *
	 * @return void
	 */
	private function security_practices() {
		$this->smart_jquery_inclusion();

		define( 'DISALLOW_FILE_EDIT', true );

		add_action( 'init', array( $this, 'remove_header_info' ) );
		add_filter( 'style_loader_src', array( $this, 'at_remove_wp_ver_css_js' ), 9999 );
		add_filter( 'script_loader_src', array( $this, 'at_remove_wp_ver_css_js' ), 9999 );
		add_filter( 'wp_headers', array( $this, 'remove_x_pingback' ) );

		// disable ping back scanner and complete xmlrpc class.
		add_filter( 'wp_xmlrpc_server_class', '__return_false' );
		add_filter( 'xmlrpc_enabled', '__return_false' );
		// remove wp version meta tag and from rss feed.
		add_filter( 'the_generator', '__return_false' );
		// Remove error mesage in login.
		add_filter(
			'login_errors',
			function() {
				return __( 'Something is wrong!' );
			}
		);
		add_filter(
			'login_messages',
			function() {
				return __( 'Something is wrong!' );
			}
		);
		// USECASE : Disable XMLRPC Class compeletely
		/*Disable complete xmlrpc class. */
		add_filter( 'wp_xmlrpc_server_class', '__return_false' );
		add_filter( 'xmlrpc_enabled', '__return_false' );

	}

	public function remove_header_info() {
		remove_action( 'wp_head', 'feed_links_extra', 3 );
		remove_action( 'wp_head', 'rsd_link' );
		remove_action( 'wp_head', 'wlwmanifest_link' );
		remove_action( 'wp_head', 'wp_generator' );
		remove_action( 'wp_head', 'start_post_rel_link' );
		remove_action( 'wp_head', 'index_rel_link' );
		remove_action( 'wp_head', 'parent_post_rel_link', 10, 0 );
		remove_action( 'wp_head', 'adjacent_posts_rel_link_wp_head', 10, 0 ); // for WordPress >= 3.0
	}

	/**
	 * Checks the php version.
	 *
	 * @return void
	 */
	public function check_php_version() {
		if ( is_admin() ) {
			$url      = 'https://www.php.net/releases/?json&version=7';
			$response = wp_remote_get( $url, array( 'timeout' => 15 ) );

			if ( ! is_wp_error( $response )
				&& isset( $response['response']['code'] )
				&& 200 === $response['response']['code'] ) {
				$body                          = wp_remote_retrieve_body( $response );
				$data                          = json_decode( $body );
				$this->most_recent_php_version = $data->version;
				$this->server_php_version      = phpversion();

				if ( $this->server_php_version !== $this->most_recent_php_version ) {
					add_action(
						'admin_notices',
						function() {
							$msg = __( 'Your php version is ' . $this->server_php_version . ' and it is not the most recent one which is ' . $this->most_recent_php_version . ', this may make your site vulnerable. please update the php version to the most recent one!' );
							echo '<div class="error notice"><p>' . $msg . '</p></div>';
						}
					);
				}
			}
		}
	}

	/**
	 * Conduct smart jquery inclusion.
	 *
	 * @return void
	 */
	private function smart_jquery_inclusion() {
		// if ( ! is_admin() ) {
		// 	wp_deregister_script( 'jquery' );
		// 	wp_register_script( 'jquery', ( 'https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js' ), false );
		// 	wp_enqueue_script( 'jquery' );
		// }
	}

	/**
	 * Remove wp version param from any enqueued scripts
	 *
	 * @param string $src the css or js src.
	 * @return string
	 */
	public function at_remove_wp_ver_css_js( $src ) {
		if ( strpos( $src, 'ver=' ) ) {
			$src = remove_query_arg( 'ver', $src );
		}
		return $src;
	}

	/**
	 * Removes X-Pingback header.
	 *
	 * @param array $headers the headers.
	 * @return string
	 */
	public function remove_x_pingback( $headers ) {
		unset( $headers['X-Pingback'] );
		return $headers;

	}

	private function security_headers_and_ini_setups() {
		// reference: https://websitesetup.org/wordpress-security/
		// header( 'Content-Security-Policy: default-src https:' );
		// header( 'X-XSS-Protection: 1; mode=block' );
		// header( 'X-Content-Type-Options: nosniff' );
		// header( 'Strict-Transport-Security:max-age=31536000; includeSubdomains; preload' );

		// @ini_set( 'session.cookie_httponly', true );
		// @ini_set( 'session.cookie_secure', true );
		// @ini_set( 'session.use_only_cookies', true );
	}



}

