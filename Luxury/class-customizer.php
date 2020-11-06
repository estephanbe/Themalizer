<?php
/**
 * Class File - Customizer
 *
 * @package BoshDev
 */

namespace Themalizer\Luxury;

class Customizer extends \Themalizer\Core\Engine {

	public $sectionSettingsSelectors = array();
	public $sectionSettingsIds       = array();
	public $finalSectionSettings     = array();

	function __construct( $args = array() ) {

		$this->processArgs( $args );
		$this->processSettings();
		add_action( 'customize_register', array( $this, 'finalAddAction' ) );

	}

	function finalAddAction( $wp_customize ) {
		$this->addSection( $wp_customize );
		$this->addSettingsAndControls( $wp_customize );
	}

	function addSection( $wp_customize ) {
		$sectionArgsDefault = array(
			'description_hidden' => true,
		);

		$sectionArgsDefault = array_merge( $sectionArgsDefault, $this->sectionOtherArgs );

		$sectionMainArgs = array(
			'title'       => __( $this->sectionTitle, $this->textDomain ),
			'description' => $this->sectionDescription,
		);

		$wp_customize->add_section( $this->sectionID, array_merge( $sectionMainArgs, $sectionArgsDefault ) );
	}

	function addSettingsAndControls( $wp_customize ) {
		foreach ( $this->finalSectionSettings as $settingID => $settingArgs ) {

			$settingControl = $settingArgs['control'];
			unset( $settingArgs['control'] );

			$settingSelector = isset( $settingArgs['selector'] ) ? $settingArgs['selector'] : '';
			unset( $settingArgs['selector'] );

			$wp_customize->add_setting( $settingID, $settingArgs );

			$settingControl['label'] = __( $settingControl['label'], $this->textDomain );
			$preDefinedControlArgs   = array(
				'section'  => $this->sectionID,
				'settings' => $settingID,
			);

			$finalControlSettings = array_merge( $settingControl, $preDefinedControlArgs );

			if ( $finalControlSettings['type'] == 'image' ) {

				$wp_customize->add_control(
					new \WP_Customize_Image_Control(
						$wp_customize,
						$settingID,
						array(
							'label'    => $settingControl['label'],
							'section'  => $this->sectionID,
							'settings' => $settingID,
							'priority' => ( isset( $finalControlSettings['priority'] ) && ! empty( $finalControlSettings['priority'] ) ) ? $finalControlSettings['priority'] : 10,
						   // 'context'    => 'your_setting_context'
						)
					)
				);
			} elseif ( $finalControlSettings['type'] == 'color' ) {

				$wp_customize->add_control(
					new \WP_Customize_Color_Control(
						$wp_customize,
						$settingID,
						array(
							'label'    => $settingControl['label'],
							'section'  => $this->sectionID,
							'settings' => $settingID,
							'priority' => ( isset( $finalControlSettings['priority'] ) && ! empty( $finalControlSettings['priority'] ) ) ? $finalControlSettings['priority'] : 10,
						   // 'context'    => 'your_setting_context'
						)
					)
				);

			} else {
				$wp_customize->add_control( $settingID, $finalControlSettings );
			}

			if ( isset( $wp_customize->selective_refresh ) && ! empty( $settingSelector ) ) {
				$wp_customize->selective_refresh->add_partial(
					$settingID . '-partial',
					array(
						'selector'        => $settingSelector,
						'settings'        => array( $settingID ),
						'render_callback' => function() {
							return get_option( $settingID );
						},
					)
				);
			}
		}
	}

	function processSettings() {
		// this function mainly for the front end id attribute value
		foreach ( $this->sectionSettings as $settingSlugWithoutPrefix => $settingArgs ) {
			$settingArgsArr = $settingArgs;

			$settingArgs = (object) $settingArgs; // args object for processing purpose

			$settingID = $this->themePrefix . '_' . $settingSlugWithoutPrefix;

			if ( isset( $settingArgs->selector ) ) {
				$settingSelector = $settingArgs->selector;
			} else {
				$settingSelector = 'x';
			}

			$this->sectionSettingsIds[ $settingSlugWithoutPrefix ]       = $settingID; // this array creation is for echoing eche setting id attribute value
			$this->sectionSettingsSelectors[ $settingSlugWithoutPrefix ] = substr( $settingSelector, 1 ); // this array creation is for echoing eche setting id attribute value

			$settingFinalArgs = array(
				'type'              => isset( $settingArgs->type ) ? $settingArgs->type : 'option',
				'transport'         => isset( $settingArgs->transport ) ? $settingArgs->transport : 'postMessage',
				'sanitize_callback' => array( $this, self::get_sanitizing_method( $settingArgs->control['type'] ) ),
			);

			$this->finalSectionSettings[ $settingID ] = array_merge( $settingFinalArgs, $settingArgsArr ); // this array creation is for settings registration purpose
		}
	}

	function processArgs( $args ) {

		$this->args = (object) $args;
		$this->testTheArgs();

		$this->textDomain  = self::get( 'text_domain' );
		$this->themePrefix = self::get( 'prefix' );

		$this->sectionTitle       = $this->args->section_title;
		$this->sectionDescription = $this->args->section_description;
		$this->sectionSettings    = $this->args->section_settings;
		$this->sectionOtherArgs   = isset( $this->args->section_args ) ? $this->args->section_args : array();
		$this->sectionID          = $this->themePrefix . '_' . strtolower( str_replace( ' ', '_', $this->sectionTitle ) );

	}

	function testTheArgs() {
		$this->empty_test( $this->args, 'Add the arguments of the section' ); // test if the section is not empty
		$this->empty_isset_test( $this->args->section_title, 'Add the section name' ); // test if the section name is set
		$this->empty_isset_test( $this->args->section_description, 'Add the section description' ); // test if the section description is set
		$this->empty_isset_test( $this->args->section_settings, 'Add the section settings' ); // test if the settings are set

		foreach ( $this->args->section_settings as $setting_slug => $args ) { // test the settings args
			$msg1 = 'Add "' . $setting_slug . '"" setting args';
			self::empty_test( $args, $msg1 ); // test if the settings args array is not empty
			$msg2 = 'Add the control of the "' . $setting_slug . '" setting';
			self::empty_isset_test( $args['control'], $msg2 ); // test if the control section is set inside the settings args
			$msg3 = 'Add the label of the "' . $setting_slug . '" control';
			self::empty_isset_test( $args['control']['label'], $msg3 ); // test if the contol args array is not empty
			$msg4 = 'Add the type of the "' . $setting_slug . '" control';
			self::empty_isset_test( $args['control']['type'], $msg4 ); // test if each control args array has type argument
		}
	}

	function theSelector_ID( $setting_id ) {
		$html   = ' id="';
		$id_val = self::bod_html_attr_sanitization( $this->sectionSettingsSelectors[ $setting_id ] );
		$html  .= $id_val;
		$html  .= '" ';
		echo $html;
	}

	function theSelector_CLASS( $setting_id ) {
		$html   = ' class="';
		$id_val = self::bod_html_attr_sanitization( $this->sectionSettingsSelectors[ $setting_id ] );
		$html  .= $id_val;
		$html  .= '" ';
		echo $html;
	}

	function theSelector_CLASS_value( $setting_id ) {
		echo self::bod_html_attr_sanitization( $this->sectionSettingsSelectors[ $setting_id ] );
	}

	function theSettingIsHtml( $setting_id ) {
		$theSetting  = get_option( $this->sectionSettingsIds[ $setting_id ] );
		$san_setting = self::bod_html_sanitization( $theSetting );
		echo $san_setting;
	}

	function theSettingIsUrl( $setting_id ) {
		$theSetting  = get_option( $this->sectionSettingsIds[ $setting_id ] );
		$san_setting = self::bod_html_url_sanitization( $theSetting );
		echo $san_setting;
	}

	function theSettingIsStyle( $setting_id ) {
		$theSetting  = get_option( $this->sectionSettingsIds[ $setting_id ] );
		$san_setting = self::bod_html_attr_sanitization( $theSetting );
		echo $san_setting;
	}


}


