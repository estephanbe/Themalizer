<?php
/**
 * Class File - Customizer Class
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

use Themalizer\Core\Engine;

/**
 * Manage the theme customizations.
 */
class Customizer extends Engine {

	/**
	 * Setting arguments which will be overriden with the defult arguments.
	 *
	 * @var array
	 */
	private $extra_args = array();

	/**
	 * The theme text-domain.
	 *
	 * @var string
	 */
	private $text_domain = '';

	/**
	 * The theme prefix.
	 *
	 * @var string
	 */
	private $theme_prefix = '';

	/**
	 * The section title.
	 *
	 * @var string
	 */
	private $title = '';

	/**
	 * The section description.
	 *
	 * @var string
	 */
	private $description = '';

	/**
	 * The section settings (options).
	 *
	 * @var array
	 */
	private $settings = array();

	/**
	 * An array of the settings selectors. It used with "selector" method in order to retrive the selector which will be added to an ID or CLASS in HTML.
	 *
	 * @var array
	 */
	public $section_settings_selectors = array();

	/**
	 * An array with the provided IDs as keys and the processes IDs as a value. It used to retrive the processed IDs for get_option calls.
	 *
	 * @var array
	 */
	public $settings_processed_ids = array();

	/**
	 * The section settings (options) array after adding the default arguments.
	 *
	 * @var array
	 */
	public $processed_settings = array();

	/**
	 * The constructor.
	 *
	 * @param array $args The class arguments.
	 */
	public function __construct( $args = array() ) {
		$this->process_args( $args );
		add_action(
			'customize_register',
			function ( $wp_customize ) {
				$wp_customize->add_section(
					$this->section_id,
					array_merge(
						array(
							'description_hidden' => true,
							'panel'              => self::get( 'panel_id' ),
							'title'              => __( $this->title, $this->text_domain ), // phpcs:ignore WordPress
							'description'        => __( $this->description, $this->text_domain ), // phpcs:ignore WordPress
						),
						$this->extra_args
					)
				);
				$this->add_settings( $wp_customize );
				$this->add_controls( $wp_customize );
			}
		);
	}

	/**
	 * Processing arguments method.
	 *
	 * @param array $args the class arguments.
	 * @return void
	 */
	private function process_args( $args ) {
		$this->test_the_args( $args ); // test the arguments.

		$this->text_domain  = self::get( 'text_domain' );
		$this->theme_prefix = self::get( 'prefix' );
		$this->title        = $args['title'];
		$this->description  = $args['description'];
		$this->extra_args   = isset( $args['args'] ) ? $args['args'] : array(); // check if there is extra arguments to overide the default settings arguments.
		$this->settings     = $args['settings'];
		$this->section_id   = $this->theme_prefix . '_' . strtolower( str_replace( ' ', '_', $this->title ) ) . '_customizer'; // generate the section id.

		/**
		 * Prepare the settings for the registration.
		 */
		foreach ( $this->settings as $setting_slug => $setting_args ) {
			$this->section_settings_selectors[ $setting_slug ] = isset( $setting_args['selector'] ) ? substr( $setting_args['selector'], 1 ) : ''; // add the selector.
			$setting_id                                        = $this->theme_prefix . '_' . $setting_slug . '_customizer_setting'; // generate the setting id.
			$this->settings_processed_ids[ $setting_slug ]     = $setting_id; // add the setting id.
			$setting_args['control']['type']                   = isset( $setting_args['control']['type'] ) ? $setting_args['control']['type'] : 'text';
			// Merge the settings with the default settings in order to override any provided settings arguments.
			$this->processed_settings[ $setting_id ] = array_merge(
				array(
					'type'              => 'option',
					'transport'         => 'postMessage',
					'sanitize_callback' => array( $this, self::get_sanitizing_method( $setting_args['control']['type'] ) ),
				),
				$setting_args
			);
		}
	}

	/**
	 * Test the arguments.
	 *
	 * @param array $args the class arguments.
	 * @return void
	 */
	private function test_the_args( $args ) {
		$this->args = (object) $args;
		self::empty_test( self::get( 'panel_id' ), 'Add the panel ID in the initialization class.' ); // test if the panel id was initiated.
		self::empty_test( $this->args, 'Add the arguments of the section' ); // test if the section is not empty.
		self::empty_isset_test( $this->args->title, 'Add the section name' ); // test if the section name is set.
		self::empty_isset_test( $this->args->description, 'Add the section description' ); // test if the section description is set.
		self::empty_isset_test( $this->args->settings, 'Add the section settings' ); // test if the settings are set.
		foreach ( $this->args->settings as $setting_slug => $args ) { // test the settings args.
			self::empty_test( $args, 'Add "' . $setting_slug . '"" setting args' ); // test if the settings args array is not empty.
			self::empty_isset_test( $args['control'], 'Add the control of the "' . $setting_slug . '" setting' ); // test if the control section is set inside the settings args.
			self::empty_isset_test( $args['control']['label'], 'Add the label of the "' . $setting_slug . '" control' ); // test if the contol args array is not empty.
		}
		if ( isset( $this->args->args['title'] ) ) { // to make sure that it is only taking the main input.
			unset( $this->args->args['title'] );
		}
		if ( isset( $this->args->args['description'] ) ) { // to make sure that it is only taking the main input.
			unset( $this->args->args['description'] );
		}
		unset( $this->args );
	}

	/**
	 * Add the settings.
	 *
	 * @param object $wp_customize wp customize retrived class, WordPress default.
	 * @return void
	 */
	public function add_settings( $wp_customize ) {
		foreach ( $this->processed_settings as $setting_id => $setting_args ) {
			unset( $setting_args['control'], $setting_args['selector'] );
			// register the setting without the control and selector.
			$wp_customize->add_setting( $setting_id, $setting_args );
		}
	}

	/**
	 * Add the controls.
	 *
	 * @param object $wp_customize wp customize retrived class, WordPress default.
	 * @return void
	 */
	public function add_controls( $wp_customize ) {
		foreach ( $this->processed_settings as $setting_id => $setting_args ) {

			$setting_control             = $setting_args['control']; // set the control variable.
			$priority                    = isset( $setting_control['priority'] ) ? $setting_control['priority'] : 10; // set the priority variable.
			$setting_control['section']  = $this->section_id; // override or add the section id.
			$setting_control['settings'] = $setting_id; // override or add the settings id.
			$setting_control['label']    = __( $setting_control['label'], $this->text_domain ); // phpcs:ignore

			// Add control according to the field type.
			switch ( $setting_control['type'] ) {
				case 'image':
					$wp_customize->add_control(
						new \WP_Customize_Image_Control(
							$wp_customize,
							$setting_id,
							array(
								'label'    => $setting_control['label'],
								'section'  => $this->section_id,
								'settings' => $setting_id,
								'priority' => $priority,
							)
						)
					);
					break;
				case 'color':
					$wp_customize->add_control(
						new \WP_Customize_Color_Control(
							$wp_customize,
							$setting_id,
							array(
								'label'    => $setting_control['label'],
								'section'  => $this->section_id,
								'settings' => $setting_id,
								'priority' => $priority,
							)
						)
					);
					break;
				default:
					$wp_customize->add_control( $setting_id, $setting_control );
					break;
			}

			// Add the selective refresh partial.
			$setting_selector = isset( $setting_args['selector'] ) ? $setting_args['selector'] : '';
			if ( isset( $wp_customize->selective_refresh ) && ! empty( $setting_selector ) ) {
				$wp_customize->selective_refresh->add_partial(
					$setting_id . '-partial',
					array(
						'selector' => $setting_selector,
						'settings' => array( $setting_id ),

					)
				);
			}
		}
	}

	/**
	 * Echo the selector.
	 *
	 * @param string $setting_id the setting id.
	 * @param string $selector the selector attribute.
	 * @return void
	 */
	public function selector( $setting_id, $selector = 'd' ) {
		self::empty_test( $setting_id, 'Please add the setting id.' );
		$html = '';
		switch ( $selector ) {
			case 'd':
				$html  = ' id="';
				$html .= self::html_attr_sanitization( $this->section_settings_selectors[ $setting_id ] );
				$html .= '" ';
				break;
			case 'c':
				$html  = ' class="';
				$html .= self::html_attr_sanitization( $this->section_settings_selectors[ $setting_id ] );
				$html .= '" ';
				break;
			case 'v':
				$html = self::html_attr_sanitization( $this->section_settings_selectors[ $setting_id ] );
				break;
		}
		echo $html; // phpcs:ignore
	}

	/**
	 * Echo the customizer setting value.
	 *
	 * @param string  $setting_id the setting id.
	 * @param boolean $echo to echo the setting of not.
	 * @return string if $echo is false.
	 */
	public function setting( $setting_id, $echo = true ) {
		self::empty_test( $setting_id, 'Please add the setting id.' );
		$setting = get_option( $this->settings_processed_ids[ $setting_id ] );
		$type    = isset( $this->settings[ $setting_id ]['control']['type'] ) ? $this->settings[ $setting_id ]['control']['type'] : 'text';
		switch ( $type ) {
			case 'url':
			case 'image':
				$setting = self::html_url_sanitization( $setting );
				break;
			case 'color':
				$setting = self::html_attr_sanitization( $setting );
				break;
			default:
				$setting = self::html_sanitization( $setting );
				break;
		}

		if ( $echo ) {
			echo $setting; // phpcs:ignore
		} else {
			return $setting;
		}
	}

	// There is issue with non text sanitization controls types like checkbox.
}


