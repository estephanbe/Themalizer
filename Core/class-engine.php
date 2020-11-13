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
use Themalizer\Luxury\Customizer as Customizer;
use Themalizer\Luxury\Facebook as Facebook;
use Themalizer\Luxury\LoadMorePosts as LoadMorePosts;
use Themalizer\Luxury\MailChimp as MailChimp;
use Themalizer\Luxury\Sharing as Sharing;

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
		self::check_framework();
		$GLOBALS['BoshDev\Themalizer']->init = new Initialization( $args ); // phpcs:ignore
	}

	protected static function get_init() {
		self::check_framework();
		return self::get_container()->init;
	}

	protected static function initialize_setting_page( $args ) {
		self::check_framework();
		self::empty_test( $args, 'Please fill out the settings arguments array.' );

		$GLOBALS['BoshDev\Themalizer']->settings = new Setting( $args );
	}

	protected static function initialize_custom_post_type( $singular, $plural, $description = '', $args = array() ) {
		self::check_framework();

		if ( ! isset( self::get_container()->custom_post_types ) ) {
			$GLOBALS['BoshDev\Themalizer']->custom_post_types = array();
		}

		$GLOBALS['BoshDev\Themalizer']->custom_post_types[ $singular ] = new PostType( $singular, $plural, $description, $args );
	}

	protected static function initialize_custom_taxonomy( $singular, $plural, $posts_scope = '', $args = array() ) {
		self::check_framework();

		if ( ! isset( self::get_container()->custom_taxonomies ) ) {
			$GLOBALS['BoshDev\Themalizer']->custom_taxonomies = array();
		}

		$GLOBALS['BoshDev\Themalizer']->custom_taxonomies[ $singular ] = new Taxonomy( $singular, $plural, $posts_scope, $args );
	}

	protected static function initialize_sidebar( $args = array() ) {
		self::check_framework();
		self::isset_test( $args['name'], 'please add the name of your sidebar' );

		if ( ! isset( self::get_container()->sidebars ) ) {
			$GLOBALS['BoshDev\Themalizer']->sidebars = array();
		}

		$GLOBALS['BoshDev\Themalizer']->sidebars[ $args['name'] ] = new Sidebar( $args );
	}

	protected static function initialize_customizer( $customizer_id, $args ) {
		self::check_framework();
		self::empty_test( $customizer_id, 'Please add the customizer id.' );

		if ( ! isset( $GLOBALS['BoshDev\Themalizer']->customizers ) ) {
			$GLOBALS['BoshDev\Themalizer']->customizers = array();
		}

		$GLOBALS['BoshDev\Themalizer']->customizers[ $customizer_id ] = new Customizer( $args );
	}


	protected static function initialize_sharing( $linkingPlatforms, $sharingPlatforms = array() ) {
		self::check_framework();
		self::empty_test( $linkingPlatforms, 'Please fill out the sharing arguments array.' );

		if ( ! isset( self::get_container()->sharing ) ) {
			$GLOBALS['BoshDev\Themalizer']->sharing = array();
		}

		// TODO: define Setting->id property
		$new_sharing = new Sharing( $linkingPlatforms );

		$GLOBALS['BoshDev\Themalizer']->sharing[ $new_sharing->id ] = $new_sharing;
	}

	protected static function check_framework() {
		// Check if the auto run file was included.
		self::isset_test( $GLOBALS['BoshDev\Themalizer'], 'You didn\'t initialize Themalizer framework' );
	}

	/** Public Methods */

	/**
	 * Return property value
	 *
	 * @param String $input the property which it will be called.
	 * @return mixed returned value the callback
	 */
	public static function get( $input ) {
		self::check_framework();
		// Case should be used here when calling properties of multiple classes.
		return self::get_container()->init->get_property( $input );
	}

	public static function get_container() {
		self::check_framework();
		return $GLOBALS['BoshDev\Themalizer'];
	}



}
