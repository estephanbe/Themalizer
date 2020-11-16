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

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

use Themalizer\Core\Engine;

/**
 * Register the theme header.
 */
class ThemeHeader extends Engine {

	public static $wp_head = true;

	public static function top_of_the_header( $html_classes = '', $title_seperator = '>' ) { ?>
		<!DOCTYPE html>
		<html class="<?php echo $html_classes; ?>" <?php language_attributes(); ?>>

		<head>
			<meta charset="<?php bloginfo( 'charset' ); ?>">
			<meta http-equiv="X-UA-Compatible" content="IE=edge">
			<meta name="description" content="Themalizer Theme">
			<meta name="viewport" content="width=device-width, initial-scale=1">

			<title><?php wp_title( $title_seperator, $display = true ); ?></title>

		<?php
	}

	public static function echo_generated_header_css_link( $link, $url = false ) {
		if ( ! $url ) {
			?>
			<link href="<?php self::make_assets_uri( $link ); ?>" rel="stylesheet">
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

	public static function bottom_of_the_header( $body_class = '' ) {
		if ( self::$wp_head ) {
			wp_head();
		}
		?>
		</head>
			<body <?php echo body_class( $body_class ); ?>>
		<?php
	}

}

