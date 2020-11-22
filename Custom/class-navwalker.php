<?php
/**
 * NavWalker Class
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace Themalizer\Custom;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}


/**
 * Customizer class for nav menus which called in wp_nav_menu.
 */
class NavWalker extends \Walker_Nav_Menu {

	function start_el( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
		$li_class = '';
		if ( $args->walker->has_children ) {
			$li_class = 'sub ';
		}

		$output .= "<li class='$li_class" . implode( ' ', $item->classes ) . "'>";

		if ( $item->url && '#' !== $item->url ) {
			$output .= '<a href="' . $item->url . '">';
		} else {
			$output .= '<span>';
		}

		$output .= $item->title;

		if ( $item->url && '#' !== $item->url ) {
			$output .= '</a>';
		} else {
			$output .= '</span>';
		}

	}

	// function end_el( &$output, $depth = 0, $args = array() ) {
	// $indent  = str_repeat( "\t", $depth );
	// $output .= "\n$indent</li>\n";
	// }


	function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent         = str_repeat( "\t", $depth );
		$sub_menu_inner = $depth > 0 ? 'sub-menu-inner' : '';
		$output        .= "\n$indent<div class='sub-menu-wrap $sub_menu_inner'>\n";
		$output        .= "\n$indent<ul>\n";
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
		$output .= "$indent</div>\n";
	}

}


