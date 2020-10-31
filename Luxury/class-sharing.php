<?php
namespace Themalizer\Luxury;

use Helper\Tests;

class Sharing {

	use Tests;

	public $sharingAPI = array(
		'facebook' => 'https://facebook.com/sharer.php?display=page&u=',
		'twitter'  => 'https://twitter.com/intent/tweet?url=',
		'mail'     => 'mailto:?subject=placeholder&&body=',
	);

	public $socialMediaPlatforms = array(
		'facebook'  => 'https://facebook.com/',
		'instagram' => 'https://www.instagram.com/',
		'twitter'   => 'https://twitter.com/',
		'youtube'   => 'https://www.youtube.com/user/',
	);

	public $mailSubject = '';

	function __construct( $linkingPlatforms, $sharingPlatforms = array() ) {
		$this->socialMediaAccountsTest( $linkingPlatforms );
		$this->linkingPlatforms = $linkingPlatforms;
		$this->sharingPlatforms = $sharingPlatforms;
	}

	function socialMediaAccountsTest( $linkingPlatforms = array() ) {
		if ( ! empty( $linkingPlatforms ) ) {
			self::empty_isset_test( $linkingPlatforms['themeSettings'], 'Please make sure you add the themeSettings object' );
			self::is_instanceof_test( $linkingPlatforms['themeSettings'], '\BoshDev\Register\SettingPage' );
			self::empty_isset_test( $linkingPlatforms['platforms'], 'Please make sure you add the platforms' );
		}
	}

	function get_linkingPlatforms_ids( $platforms ) {
		$platforms_options_values = array();

		foreach ( $platforms as $platform ) {
			// self::dump_this($this->linkingPlatforms);die;
			$option                                = get_option(
				$this
				->linkingPlatforms['themeSettings']
				->settingsPageOptions[ $this
					->linkingPlatforms['platforms'][ $platform ][0] ]
			);
			$platforms_options_values[ $platform ] = $option;
		}

		return $platforms_options_values;
	}

	function echo_linkingPlatforms( $platforms = array() ) {
		if ( empty( $platforms ) ) {
			$platforms = array_keys( $this->linkingPlatforms['platforms'] );
		}

		$platforms_options_values = $this->get_linkingPlatforms_ids( $platforms );

		$this->final_html = ''; // build default html holder

		foreach ( $this->linkingPlatforms['platforms'] as $platform => $args ) { // loop through eche platform

			if ( ! in_array( $platform, $platforms ) || empty( $platforms_options_values[ $platform ] ) ) {
				continue;
			}

			$linkingPlatformID = $args[0];
			self::empty_test( $args[1], 'Please make sure you add the attributes of the linkingPlatforms' );
			foreach ( $args[1] as $element => $attributes ) { // loop through each structure to open the tags
				$this->final_html .= '<'; // open element tag
				$this->final_html .= $element; // add the element
				$this->final_html .= $element == 'a' ? $this->add_href_linkingPlatforms( $platforms_options_values[ $platform ], $platform ) : ''; // add the href attr if element if ele is anchor

				if ( ! is_array( $attributes ) ) { // check if the attributes value array or string
					if ( $attributes !== '' ) {
						$this->final_html .= ' class="'; // if string, then it's classes and should be echoed into class attr
						$this->final_html .= $attributes;
						$this->final_html .= '"';
					}
				} else { // if array, loop through the array as the attr key is the element key and the attr value is the element value.
					foreach ( $attributes as $attr => $val ) {
						$this->final_html .= ' '; // add space after the element to add the attributes
						$this->final_html .= $attr;
						$this->final_html .= '="';
						$this->final_html .= $val;
						$this->final_html .= '" ';
					}
				}
				$this->final_html .= '>'; // close the element tag
			}

			// loop in reverse order to close the tags
			foreach ( array_reverse( $args[1] )  as $element => $attributes ) {
				$this->final_html .= '</';
				$this->final_html .= $element;
				$this->final_html .= '>';
			}
		}

		echo $this->final_html;

	}

	public function echo( $link = '' ) {
		self::empty_test( $link, 'Make sure the link has value' );
		$this->sharingAPI['mail'] = str_replace( 'placeholder', $this->mailSubject, $this->sharingAPI['mail'] ); // replace the mail link placeholder with the mailSubject value

		$this->final_html = ''; // build default html holder

		foreach ( $this->sharingPlatforms as $platform => $structure ) { // loop through eche platform
			foreach ( $structure as $element => $attributes ) { // loop through each structure to open the tags
				$this->final_html .= '<'; // open element tag
				$this->final_html .= $element; // add the element
				$this->final_html .= $element == 'a' ? $this->add_href_sharingPlatforms( $link, $platform ) : ''; // add the href attr if element if ele is anchor

				if ( ! is_array( $attributes ) ) { // check if the attributes value array or string
					if ( $attributes !== '' ) {
						$this->final_html .= ' class="'; // if string, then it's classes and should be echoed into class attr
						$this->final_html .= $attributes;
						$this->final_html .= '"';
					}
				} else { // if array, loop through the array as the attr key is the element key and the attr value is the element value.
					foreach ( $attributes as $attr => $val ) {
						$this->final_html .= ' '; // add space after the element to add the attributes
						$this->final_html .= $attr;
						$this->final_html .= '="';
						$this->final_html .= $val;
						$this->final_html .= '" ';
					}
				}
				$this->final_html .= '>'; // close the element tag
			}

			// loop in reverse order to close the tags
			foreach ( array_reverse( $structure )  as $element => $attributes ) {
				$this->final_html .= '</';
				$this->final_html .= $element;
				$this->final_html .= '>';
			}
		}

		echo $this->final_html;
	}



	public function override_sharingPlatforms( $args ) {
		self::empty_test( $args, 'add the overriding arguments' );
		$this->sharingPlatforms = $args;
	}

	public function override_linkingPlatforms( $args ) {
		self::empty_test( $args, 'add the overriding arguments' );
		foreach ( $args as $platform => $structure ) {
			$this->linkingPlatforms['platforms'][ $platform ][1] = $structure;
		}

	}

	public function addPublicClass( $args = array() ) {
		self::empty_test( $args, 'add the additiona classes' );
		foreach ( $this->sharingPlatforms as $platform => $structure ) {
			foreach ( $args as $element => $value ) {
				if ( ! is_array( $this->sharingPlatforms[ $platform ][ $element ] ) ) {
					$this->sharingPlatforms[ $platform ][ $element ] .= ' ';
					$this->sharingPlatforms[ $platform ][ $element ] .= $value;
				} else {
					$this->sharingPlatforms[ $platform ][ $element ]['class'] .= ' ';
					$this->sharingPlatforms[ $platform ][ $element ]['class'] .= $value;
				}
			}
		}
	}

	private function add_href_sharingPlatforms( $link, $platform ) {
		$href  = '';
		$href .= ' target="_blank" href="';
		$href .= $this->sharingAPI[ $platform ];

		if ( $link == 'this' ) {
			$href .= ( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' ) . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
		} else {
			$href .= $link;
		}

		$href .= '" ';
		return $href;
	}

	private function add_href_linkingPlatforms( $id, $platform ) {
		$href  = '';
		$href .= ' target="_blank" href="';
		$href .= $this->socialMediaPlatforms[ $platform ];
		$href .= $id;
		$href .= '" ';
		return $href;
	}

}
