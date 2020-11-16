<?php
/**
 * Engine Class
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
 * Including all the classes in the framework
 */
use Themalizer\Core\Init as Initialization;
use Themalizer\Core\Security as Security;
use Themalizer\Register\PostType as PostType;
use Themalizer\Register\SettingPage as Setting;
use Themalizer\Register\Sidebar as Sidebar;
use Themalizer\Register\Taxonomy as Taxonomy;
use Themalizer\Register\ThemeHeader as ThemeHeader;
use Themalizer\Luxury\Customizer as Customizer;
use Themalizer\Luxury\Facebook as Facebook;
use Themalizer\Luxury\LoadMorePosts as LoadMorePosts;
use Themalizer\Luxury\MailChimp as MailChimp;
use Themalizer\Luxury\Sharing as Sharing;
use Themalizer\Luxury\ImageSize as ImageSize;

/** Traits */
use Themalizer\Helper\Tests;
use Themalizer\Helper\Sanitizers;

/**
 * Provides general methods and calls used internally in all classes in the framework.
 */
class Engine {

	// Including all the traits in the framework.
	use Tests;
	use Sanitizers;

	/**
	 * Define if theme is under development or not.
	 *
	 * @var boolean
	 */
	public static $development = false;

	/**
	 * Initialize the security class
	 *
	 * @return class
	 */
	protected static function initialize_security() {
		return new Security();
	}

	protected static function initialize_theme( $args ) {
		$GLOBALS['BoshDev\Themalizer']->init = new Initialization( $args ); // phpcs:ignore
	}

	protected static function get_init() {
		return self::get_container()->init;
	}

	protected static function initialize_setting_page( $args ) {
		self::empty_test( $args, 'Please fill out the settings arguments array.' );

		$GLOBALS['BoshDev\Themalizer']->settings = new Setting( $args );
	}

	protected static function initialize_custom_post_type( $singular, $plural, $description = '', $args = array() ) {

		if ( ! isset( self::get_container()->custom_post_types ) ) {
			$GLOBALS['BoshDev\Themalizer']->custom_post_types = array();
		}

		$GLOBALS['BoshDev\Themalizer']->custom_post_types[ $singular ] = new PostType( $singular, $plural, $description, $args );
	}

	protected static function initialize_custom_taxonomy( $singular, $plural, $posts_scope = '', $args = array() ) {

		if ( ! isset( self::get_container()->custom_taxonomies ) ) {
			$GLOBALS['BoshDev\Themalizer']->custom_taxonomies = array();
		}

		$GLOBALS['BoshDev\Themalizer']->custom_taxonomies[ $singular ] = new Taxonomy( $singular, $plural, $posts_scope, $args );
	}

	protected static function initialize_sidebar( $args = array() ) {
		self::isset_test( $args['name'], 'please add the name of your sidebar' );

		if ( ! isset( self::get_container()->sidebars ) ) {
			$GLOBALS['BoshDev\Themalizer']->sidebars = array();
		}

		$GLOBALS['BoshDev\Themalizer']->sidebars[ $args['name'] ] = new Sidebar( $args );
	}

	protected static function initialize_customizer( $customizer_id, $args ) {
		self::empty_test( $customizer_id, 'Please add the customizer id.' );

		if ( ! isset( $GLOBALS['BoshDev\Themalizer']->customizers ) ) {
			$GLOBALS['BoshDev\Themalizer']->customizers = array();
		}

		$GLOBALS['BoshDev\Themalizer']->customizers[ $customizer_id ] = new Customizer( $args );
	}


	protected static function initialize_sharing( $linkingPlatforms, $sharingPlatforms = array() ) {
		self::empty_test( $linkingPlatforms, 'Please fill out the sharing arguments array.' );

		if ( ! isset( self::get_container()->sharing ) ) {
			$GLOBALS['BoshDev\Themalizer']->sharing = array();
		}

		// TODO: define Setting->id property
		$new_sharing = new Sharing( $linkingPlatforms );

		$GLOBALS['BoshDev\Themalizer']->sharing[ $new_sharing->id ] = $new_sharing;
	}

	protected static function echo_start_header( $html_classes = '', $title_seperator = '' ) {

		ThemeHeader::top_of_the_header( $html_classes, $title_seperator );
	}

	protected static function generate_header_css_link( $link, $url = false ) {
		ThemeHeader::echo_generated_header_css_link( $link, $url );
	}

	protected static function prioratize_wp_head() {
		ThemeHeader::echo_wp_head();
	}

	protected static function echo_close_header( $body_class = '' ) {

		ThemeHeader::bottom_of_the_header( $body_class );
	}

	protected static function generate_new_image_size( $slug, $width, $height, $crop = false ) {
		$new_image = new ImageSize( $slug, $width, $height, $crop );
	}

	protected static function customized_image_size( $url, $size_slug ) {
		return ImageSize::change_image_size( $url, $size_slug );
	}


	/** ================================ Public Methods ================================= */

	public static function check_framework() {
		// Check if the auto run file was included.
		self::isset_test( $GLOBALS['BoshDev\Themalizer'], 'You didn\'t initialize Themalizer framework' );
	}

	/**
	 * Return property value
	 *
	 * @param String $input the property which it will be called.
	 * @return mixed returned value the callback
	 */
	public static function get( $input ) {
		// Case should be used here when calling properties of multiple classes.
		return self::get_container()->init->get_property( $input );
	}

	public static function get_container() {
		return $GLOBALS['BoshDev\Themalizer'];
	}

	/**
	 * Generate full URI to the given path for assets directory.
	 *
	 * @param string  $path the path to be appended to the assets URI.
	 * @param boolean $echo switch to echo the path or return it as it is.
	 * @return string   return the path if the switch is False.
	 */
	public static function make_assets_uri( $path = '', $echo = true ) {
		if ( $echo ) {
			echo self::html_url_sanitization( $GLOBALS['BoshDev\Themalizer']->init->get( 'assets_dir_uri' ) . $path ); // phpcs:ignore
		} else {
			return self::html_url_sanitization( $GLOBALS['BoshDev\Themalizer']->init->get( 'assets_dir_uri' ) . $path );
		}
	}
	/**
	 * Generate full URI to the given path.
	 *
	 * @param string  $path the path to be appended to the assets URI.
	 * @param boolean $echo switch to echo the path or return it as it is.
	 * @return string   return the path if the switch is False.
	 */
	public static function make_dir_uri( $path = '', $echo = true ) {
		if ( $echo ) {
			echo self::html_url_sanitization( $GLOBALS['BoshDev\Themalizer']->init->get( 'dir_uri' ) . $path ); // phpcs:ignore
		} else {
			return self::html_url_sanitization( $GLOBALS['BoshDev\Themalizer']->init->get( 'dir_uri' ) . $path );
		}
	}



}
