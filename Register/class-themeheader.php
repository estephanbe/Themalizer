<?php

/**
 * Class File - Theme Header
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace Themalizer\Register;

use Themalizer\Core\Connector;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

/**
 * Register the theme header.
 */
class ThemeHeader {


	public static $wp_head = true;

	public static function simple_header() {
		self::top_of_the_header();
		self::bottom_of_the_header();
	}

	public static function top_of_the_header( $html_classes = '', $html_attrs = array(), $title_seperator = '>' ) {    
		$attrs = '';
		if (!empty($html_attrs)){
			foreach ($html_attrs as $attr => $value) {
				$attrs .= $attr . '="' . $value . '"';
			}
		}
		?>
		<!DOCTYPE html>
		<html <?php echo !empty($html_classes) ? 'class="' . $html_classes . '"' : ''; ?> <?php echo ' ' . $attrs . ' '; language_attributes(); ?>>

		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="viewport" content="width=device-width, initial-scale=1">
			<?php
	}

	public static function echo_generated_header_css_link( $link, $url = false ) {
		if ( ! $url ) {
			?>
				<link href="<?php Connector::make_assets_uri( $link ); ?>" rel="stylesheet">
			<?php
		} else {
			?>
				<link href="<?php echo $link; ?>" rel="stylesheet">
			<?php
		}
	}

	public static function echo_wp_head() {
		 self::$wp_head = false;
		wp_head();
	}

	/**
	 * Close the header
	 *
	 * @param string $body_class add classes to the body tag.
	 * @param [type] $attrs add attributes to the body tag.
	 * @return void
	 */
	public static function bottom_of_the_header( $body_class = '', $attrs = array() ) {
		if ( self::$wp_head ) {
			wp_head();
		}
		?>
		</head>

		<body 
		<?php
		echo ' ' . body_class( $body_class ) . ' ';
		if ( is_array( $attrs ) && ! empty( $attrs ) ) {
			foreach ( $attrs as $attr => $value ) {
				$echo_val = $value !== null ? $attr . '="' . esc_attr( $value ) . '" ' : $attr;
				echo ' ' . $echo_val . ' ';
			}
		}
		?>
		>
		<?php
	}
}
