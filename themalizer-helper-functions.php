<?php
/**
 * Helper functions
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

if ( ! function_exists( 'set_post_views' ) ) {

	/**
	 * Set post views meta for each post and it sould be used in single.php in order to enable this functionality.
	 *
	 * The query params to collect the posts is:
	 * 'meta_key' => 'post_views_count',
	 * 'orderby' => 'meta_value_num',
	 * 
	 * @param integer $post_id the post id.
	 * @return void
	 */
	function set_post_views( $post_id ) {
		$count_key = 'post_views_count';
		$count     = get_post_meta( $post_id, $count_key, true );
		if ( $count == '' ) {
			$count = 0;
			delete_post_meta( $post_id, $count_key );
			add_post_meta( $post_id, $count_key, '0' );
		} else {
			$count++;
			update_post_meta( $post_id, $count_key, $count );
		}
	}
}

