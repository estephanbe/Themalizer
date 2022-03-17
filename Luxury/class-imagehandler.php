<?php
/**
 * Class File - ImageHandler Class
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace Themalizer\Luxury;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

use Themalizer\Core\Connector;

/**
 * Handle images.
 */
class ImageHandler {

	public $slug                = '';
	public $width               = '';
	public $height              = '';
	public $crop                = false;
	public $human_friendly_name = '';

	public function __construct( $slug, $width, $height, $crop = false ) {
		Connector::empty_test( $slug, 'add the image slug.' );
		Connector::empty_test( $width, 'add the image slug.' );
		Connector::empty_test( $height, 'add the image slug.' );

		$this->slug                = $slug;
		$this->width               = $width;
		$this->height              = $height;
		$this->crop                = $crop;
		$this->human_friendly_name = ucfirst( str_replace( '_', ' ', $slug ) );

		\add_action(
			'after_setup_theme',
			function() {
				\add_image_size( $this->slug, $this->width, $this->height, $this->crop );
			}
		);

		\add_filter(
			'image_size_names_choose',
			function ( $sizes ) {
				return array_merge(
					$sizes,
					array(
						$this->slug => \__( $this->human_friendly_name ),
					)
				);
			}
		);
	}

	public static function change_image_size( $url, $size_slug ) {
		Connector::empty_test( $url, 'add the image url.' );
		Connector::empty_test( $size_slug, 'add the size slug.' );
		$attachment_id = $url;
		if ( gettype( $attachment_id ) !== 'integer' ) {
			$attachment_id = \attachment_url_to_postid( $url );
		}

		return \esc_url( \wp_get_attachment_image_src( $attachment_id, $size_slug )[0] );
	}

	public static function get_logo($attrs = array(), $main_size = 'full'){
		$logo = get_theme_mod( 'custom_logo' );
		if(!empty($logo)){
			$logo_res = array(
				'src' => wp_get_attachment_image_src( $logo , $main_size )[0]
			);
			if( in_array('srcset', $attrs) ){
				$logo_res['srcset'] = wp_get_attachment_image_srcset($logo, $main_size);
			}
		} else {
			$logo_res = array();
		}
		return $logo_res;
	}


}


