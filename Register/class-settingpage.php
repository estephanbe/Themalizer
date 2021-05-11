<?php
/**
 * Class File - Settings Page Class
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

use Themalizer\Core\Connector;

/**
 * Create theme setting page with options.
 */
class SettingPage {

	/**
	 * Settings Page Title.
	 *
	 * @var string
	 */
	private $page_title = 'Themalizer Settings Page';

	/**
	 * Settings Page Capability.
	 *
	 * @var string
	 */
	private $capability = 'manage_options';

	/**
	 * Settings Page Menu Title.
	 *
	 * @var string
	 */
	private $menu_title = 'Themalizer Settings';

	/**
	 * Settings Page Menu Slug.
	 *
	 * @var string
	 */
	private $menu_slug;

	/**
	 * Settings Page Menu Position.
	 *
	 * @var string
	 */
	private $position = null;

	/**
	 * Settings Options Group.
	 *
	 * @var string
	 */
	private $options_group;

	/**
	 * Settings Options Group.
	 *
	 * @var string
	 */
	public $setting_page_id;

	/**
	 * Settings Page Sections.
	 *
	 * @var array
	 */
	private $sections = array();

	/**
	 * The location of the menu.
	 *
	 * @var string main, theme.
	 */
	private $menu_location = 'main';

	/**
	 * Theme prefix.
	 * It is good practice to have this propertiy as the theme prefix used many times in the class.
	 *
	 * @var array
	 */
	private $theme_prefix;

	/**
	 * Fillable arguments.
	 *
	 * @var array
	 */
	private $customizable_properties = array(
		'page_title',
		'menu_title',
		'capability',
		'position',
		'sections',
		'menu_location',
	);

	/**
	 * Contructor
	 *
	 * @param array $custom_args initialization args.
	 */
	public function __construct( $custom_args = array() ) {
		$this->process_args( $custom_args );
		$this->add_actions(); // add the necessary actions to register the setting page.
		$this->setting_page_id = $this->page_title;

		if ( isset( $_GET['page'] ) ) {
			if ( $this->menu_slug === $_GET['page'] ) {
				add_action( 'admin_head', array( $this, 'add_setting_page_style' ) );
			}
		}

	}

	/**
	 * Process the class properties and fill each property with it's value.
	 *
	 * @param array $custom_args args to be processed.
	 * @return void
	 */
	private function process_args( $custom_args ) {

		// Fill the properties.
		if ( ! empty( $custom_args ) ) {
			foreach ( $custom_args as $property => $value ) {
				if ( in_array( $property, $this->customizable_properties, true ) ) {
					$this->{$property} = $value;
				}
			}
		}

		$this->theme_prefix  = Connector::$theme_prefix;
		$this->menu_slug     = $this->theme_prefix . '_' . str_replace( ' ', '_', strtolower( $this->menu_title ) ) . '_slug'; // generate the menu_slug.
		$this->options_group = $this->theme_prefix . '_' . str_replace( ' ', '_', strtolower( $this->menu_title ) ) . '_option_group'; // generate the options_group.

		Connector::empty_test( $this->sections, 'Please add at least one section' );

		foreach ( $this->sections as $section => $args ) {
			Connector::empty_isset_test( $args['title'], 'Please add the title field for "' . $section . '" section.' );
			Connector::empty_isset_test( $args['fields'], 'Please add the section fields and fill its arguments.' );

			foreach ( $args['fields'] as $field_name => $field_args ) {
				Connector::empty_isset_test( $field_args['title'], 'Please add the title of ' . $field_name . ' field.' );
			}
		}

	}

	/**
	 * Add the page actions
	 *
	 * @return void
	 */
	private function add_actions() {
		if ( is_admin() ) {
			add_action( 'admin_menu', array( $this, 'settings_menu' ) );
			add_action( 'admin_init', array( $this, 'initialize_options' ) );
		}
	}

	/**
	 * Register the setting page in the appearance menu.
	 *
	 * @return void
	 */
	public function settings_menu() {
		if ( 'theme' === $this->menu_location ) {
			add_theme_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				array( $this, 'echo_the_page' ),
				$this->position
			);
		} else {
			add_menu_page(
				$this->page_title,
				$this->menu_title,
				$this->capability,
				$this->menu_slug,
				array( $this, 'echo_the_page' ),
				'',
				$this->position
			);
		}
	}

	/**
	 * Echo the page in the admin dashboard when selected.
	 *
	 * @return void
	 */
	public function echo_the_page() {
		// Check user capability.
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); // phpcs:ignore
		}
		?>
		<div id="themalizer_settings_page">
			<h1><?php echo Connector::html_sanitization( get_admin_page_title() ); // phpcs:ignore ?></h1>

			<?php settings_errors(); ?>

			<form method="post" action="options.php" id="themalizer_settings_form">
				<?php settings_fields( $this->options_group ); ?>
				<?php do_settings_sections( $this->menu_slug ); ?>
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	/**
	 * Register the page options.
	 *
	 * @return void
	 */
	public function initialize_options() {
		/** Loop through each section */
		foreach ( $this->sections as $section => $args ) {
			add_settings_section(
				$section, // section slug.
				$args['title'], // section title.
				array( $this, 'add_settings_section_description' ), // callable function to echo section description.
				$this->menu_slug // accociate options page.
			);

			/** Register the fields of the section */
			foreach ( $args['fields'] as $field_name => $field_args ) {

				$field_args = (object) $field_args;

				// assign a value of Null for the below three elements if they have no value in order to define them for the below processing.
				$field_args->description     = isset( $field_args->description ) ? $field_args->description : null;
				$field_args->switchable_with = isset( $field_args->switchable_with ) ? $field_args->switchable_with : null;
				$field_args->dependent       = isset( $field_args->dependent ) ? $field_args->dependent : null;
				$field_args->width           = isset( $field_args->width ) ? $field_args->width : 100;

				/**
				 * Field type can be an array where the 0 index is the field type
				 * and the 1 index is the html which will prepend the input.
				 */
				$field_args->type = isset( $field_args->type ) ? $field_args->type : 'text'; // field type is text by default.
				$field_html       = ''; // for adding any html before the input.
				if ( is_array( $field_args->type ) ) { // check if the field type is array, then add it's prepending HTML.
					$field_html       = $field_args->type[1];
					$field_args->type = $field_args->type[0];
				}

				// add the setting filed for the admin page.
				add_settings_field(
					$field_name, // field slug.
					$field_args->title, // field title.
					array( $this, 'echo_settings_field_callback' ), // echo inputs.
					$this->menu_slug, // accociate options page.
					$section, // accociate section.
					array(
						'label_for'       => $field_name, // field label.
						'type'            => $field_args->type, // type of the input, text by default.
						'field_name'      => $field_name, // the name of the field.
						'description'     => $field_args->description, // the description of the field.
						'switchable_with' => $field_args->switchable_with, // disable the current field if the switchable_with field has value.
						'dependent'       => $field_args->dependent, // disable the current field if the dependable field has no value.
						'all_fields'      => $args['fields'], // all the fields.
						'html'            => $field_html, // the html which will prepend the input field.
						'width'           => $field_args->width, // the field width.
					)
				);

				// Save the input to the database.
				register_setting(
					$this->options_group, // options group name.
					$this->theme_prefix . '_' . $field_name, // The option name created by merging the prefx with the field_name.
					array(
						'default'           => null,
						'sanitize_callback' => array( $this, 'sanitize_inputs' ), // sanitization callback.
					)
				);

			} // end fields.
		} // end sections.
	}

	/**
	 * Sanitize the inputs from the fields.
	 *
	 * @param mixed $input the input from the field.
	 * @return mixed
	 */
	public function sanitize_inputs( $input ) {
		$new_input = array();

		if ( null === $input || ! is_array( $input ) ) {
			return $new_input;
		}

		$key = array_keys( $input )[0];

		switch ( $key ) {
			case 'checkbox':
				if ( isset( $input['checkbox'] ) ) {
					$new_input['checkbox'] = absint( $input['checkbox'] );
				}
				break;
			case 'text':
				if ( isset( $input['text'] ) ) {
					$new_input['text'] = Connector::text_field_sanitization( $input['text'] );
				}
				break;
			case 'date':
				if ( isset( $input['date'] ) ) {
					$new_input['date'] = Connector::text_field_sanitization( $input['date'] );
				}
				break;
			case 'number':
				if ( isset( $input['number'] ) ) {
					$new_input['number'] = intval( $input['number'] );
				}
				break;
			default:
				break;
		}

		return $new_input;
	}

	/**
	 * Section description.
	 *
	 * @param array $args the section args.
	 * @return void
	 */
	public function add_settings_section_description( $args ) {
		$description = Connector::html_attr_sanitization( $this->sections[ $args['id'] ]['description'] );

		// phpcs:disable
		// echo "<button 
		// 	type='button' 
		// 	class='btn btn-sm rounded-circle p-0 bod_help_icons' 
		// 	data-container='body' 
		// 	data-toggle='popover' 
		// 	data-placement='right' 
		// 	data-trigger='hover' 
		// 	data-content='$description'>
		// 		<span class='dashicons dashicons-editor-help'></span>
		// </button>";
		echo "<div class='themalizer-settings-page-section-description'>
			$description
			</div>";
		// phpcs:enable

	}

	/**
	 * Echo the setting filed in the setting page.
	 *
	 * @param array $args same as $args element in add_settings_field function in initialize_options method.
	 * @return void
	 */
	public function echo_settings_field_callback( $args ) {

		$args         = (object) $args; // covert the arguments into obj for easier use.
		$option_name  = $this->theme_prefix . '_' . $args->field_name; // generate the option name.
		$option_value = get_option( $option_name ); // retrive the option value.

		// TODO: check it later.
		// $field_retrived_value = get_option( $option_name ); // retrive the option value.
		// $option_value            = ''; // initiate the field value container.
		// if ( false !== $field_retrived_value ) {
		// $option_value = $field_retrived_value;
		// } phpcs:ignore.

		/** Sanitize the outputs */
		$args->type       = Connector::html_attr_sanitization( $args->type );
		$args->field_name = Connector::html_attr_sanitization( $args->field_name );
		$option_name      = Connector::html_attr_sanitization( $option_name );

		if ( $args->dependent || $args->switchable_with ) {
			$switch = $this->process_dependant_and_switchable( $args->dependent, $args->switchable_with );
		} else {
			$switch['value'] = false;
		}

		$this->echo_field(
			array(
				'switch'       => $switch,
				'type'         => $args->type,
				'html'         => $args->html,
				'field_name'   => $args->field_name,
				'option_name'  => $option_name,
				'option_value' => $option_value,
				'description'  => $args->description,
				'width'        => $args->width,
			)
		);

	}

	/**
	 * Generate the switching msg which will be displayed if the field has switch value.
	 *
	 * @param string $dependent_val  dependent field id.
	 * @param string $switchable_val switchable_with field id.
	 * @return array
	 */
	private function process_dependant_and_switchable( $dependent_val, $switchable_val ) {
		$switch_message = '';
		$field_switch   = false;

		/** The field weather dependent or switchable, it can't be both. So, elseif has been used. */
		if ( $dependent_val ) { // if the dependent field doesn't have value, process the switch.
			$retrived_dependent_val = get_option( $this->theme_prefix . '_' . $dependent_val ); // if dependent field is set, get it's value.
			if ( empty( $retrived_dependent_val ) ) {
				$field_switch   = true; // enable the switch if dependent field retrived value is empty.
				$switch_message = "This field has been disabled since the '" . $this->get_switching_field_title( $dependent_val ) . "' field doesn't has value.";  // show the msg if it doesn't have value.
			}
		} elseif ( $switchable_val ) { // if the switchable_with field have value, process the switch.
			$retrived_switchable_val = get_option( $this->theme_prefix . '_' . $switchable_val ); // if switchable field is set, get it's value.
			if ( ! empty( $retrived_switchable_val ) ) {
				$field_switch   = true; // enable the switch if switchable field retrived value is not empty.
				$switch_message = "This field has been disabled since the '" . $this->get_switching_field_title( $switchable_val ) . "' field has value.";  // show the msg if it doesn't have value.
			}
		}

		return array(
			'value'   => $field_switch,
			'message' => $switch_message,
		);
	}

	/**
	 * Get the switching field title.
	 *
	 * @param string $field_id the field ID.
	 * @return string
	 */
	private function get_switching_field_title( $field_id ) {
		$switching_field_title = '';
		foreach ( $this->sections as $section_to_be_processed ) { // search for the field value in the main sections.
			if ( isset( $section_to_be_processed['fields'][ $field_id ] ) ) {
				$switching_field_title = $section_to_be_processed['fields'][ $field_id ]['title']; // get the title if the field to use it with the below msg.
				break;
			}
		}
		return $switching_field_title;
	}

	/**
	 * Echo the input field according to its type.
	 *
	 * @param array $args field arguments.
	 * @return void
	 */
	private function echo_field( $args ) {
		$switch       = $args['switch'];
		$field_type   = $args['type'];
		$html         = $args['html'];
		$field_name   = $args['field_name'];
		$option_name  = $args['option_name'];
		$option_value = $args['option_value'];
		$description  = $args['description'];
		$width        = $args['width'];

		if ( $switch['value'] ) {
			echo "<div class='switch-msgs'>" . $switch['message'] . '</div>'; // phpcs:ignore
		}

		$switch_styling = $switch['value'] ? 'display:none;' : '';
		$input_name     = $option_name . "[$field_type]";

		// phpcs:disable
		echo $html;
		switch ( $field_type ) {
			case 'checkbox':
				$sanitized_option_value = Connector::html_int_sanitization( isset( $option_value[ $field_type ] ) ? $option_value[ $field_type ] : 0 );
				echo "<input 
				class='themalizer-settings-page-input' 
				style='$switch_styling width:'$width%;'
				type='$field_type' 
				id='$field_name' 
				name='$input_name' 
				value='1' 
				" . checked( 1, $sanitized_option_value, false ) . '
				/>';
				echo "<span style='$switch_styling' class='themalizer-settings-page-input-description'>$description</span>";
				break;
			default:
				$sanitized_option_value = Connector::html_attr_sanitization(
					isset( $option_value[ $field_type ] ) ? $option_value[ $field_type ] : 0
				);
				echo "<input 
				class='themalizer-settings-page-input' 
				style='$switch_styling width:$width%;'
				type='$field_type' 
				id='$field_name' 
				name='$input_name' 
				value='$sanitized_option_value'
				/>";
				echo "<div style='$switch_styling' class='themalizer-settings-page-input-description'>$description</div>";
				break;
		}
		// phpcs:enable
	}

	/**
	 * Add the style to the setting page.
	 *
	 * @return void
	 */
	public function add_setting_page_style() {
		echo '<style>#themalizer_settings_page{background-color:#fff;padding:2rem;margin:1rem}#themalizer_settings_page>h1{text-align:center;padding:1rem;margin-bottom:4rem;font-size:300%;font-weight:700}#themalizer_settings_page h2{margin-bottom:.5rem;font-size:150%;font-weight:700}#themalizer_settings_page .form-table{background-color:#fafafa;border:1px solid #dee2e6;width:100%;height:auto;border-collapse:separate;border-spacing:1rem}.switch-msgs{color:#6c757d!important}.themalizer-settings-page-input{display:block;width:100%;height:calc(1.5em + .75rem + 2px);padding:.375rem .75rem;font-size:1rem;font-weight:400;line-height:1.5;color:#495057;background-color:#fff;background-clip:padding-box;border:1px solid #ced4da;border-radius:.25rem;transition:border-color .15s ease-in-out,box-shadow .15s ease-in-out}.themalizer-settings-page-input-description{color:#6c757d!important;padding-left:.5rem;padding-top:.5rem}</style>';
	}

	/**
	 * Get the option value.
	 *
	 * @param string $option the option name/id.
	 * @return mixed
	 */
	public function get_option_value( $option ) {
		$option_name = $this->theme_prefix . '_' . $option;

		return get_option( $option_name );
	}

}


