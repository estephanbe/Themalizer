<?php
/**
 * Framework auto load files.
 * This autoloads the necessary classes when needed.
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

if ( class_exists( 'Themalizer' ) ) {
	throw new exception("There is an initialization for a plugin or a theme which has the same name of Themalizer. You need to uninstall it in order to run this framework.");
	exit();
}

/** Include Themalizer library */
require_once 'vendor/autoload.php';

/** Check if file exists and load the classes automatically */
spl_autoload_register(
	function ( $class_name ) {
		$class_uri = get_class_uri( $class_name );
		if ( file_exists( $class_uri ) ) {
			include_once $class_uri;
		}
	}
);



$GLOBALS['BoshDev\Themalizer'] = new Themalizer();

/**
 * Return the class URI to load the class
 *
 * @param String $class_name the class name which will be processed. Example: Themalizer\Core\Init.
 * @return String processed class URI.
 */
function get_class_uri( $class_name ) {

	$final_class_uri = '';

	// Explode $class_name in order to mirror the class name with its file.
	$class_path_array = explode( '\\', $class_name );

	// Check if the loaded class is from Themalizer framework and stop excution if it is not.
	if ( ! is_themalizer( $class_path_array ) ) {
		return;
	}

	// Check if it is the Main class and return its URI DIR without proceed processing.
	if ( 'Themalizer' === $class_name ) {
		return __DIR__ . '/class-' . strtolower( $class_path_array[0] ) . '.php';
	} else {
		// unset the Themalizer file path as this file is already in themalizer folder.
		unset( $class_path_array[0] );
	}

	$class_path_array = array_values( $class_path_array );

	/** Loop through the array which was generated from $class_name and generate the URI */
	foreach ( $class_path_array as $value ) {
		/**
		 * Check if $value is the requested class or the directory that prepended the class in the namespace.
		 * Example: Themalizer (directory) \Core (directory) \Init (the class)
		 */
		if ( end( $class_path_array ) !== $value ) {
			$final_class_uri .= $value . '/';
		} else {
			/**
			 * Check if the file belongs to a trait or a class and append it as needed.
			 * All traits are located in Helper directory.
			 */
			if ( 'Helper' === $class_path_array[0] ) {
				$final_class_uri .= 'trait-' . strtolower( end( $class_path_array ) ) . '.php';
			} else {
				$final_class_uri .= 'class-' . strtolower( end( $class_path_array ) ) . '.php';
			}
		}
	}

	return __DIR__ . '/' . $final_class_uri; // Appened the class file URI with the full URI DIR.
}

/**
 * Check if the provided class belongs to Themalizer
 *
 * @param Array $arr the class names array.
 * @return Boolean
 */
function is_themalizer( $arr ) {

	// if the array's count is less than 2, it doesn't follow the framework namespace.
	if ( 1 > count( $arr ) ) {
		return false;
	} elseif ( 'Themalizer' !== $arr[0] ) {
		return false;
	}

	return true;
}


