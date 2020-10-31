<?php
/**
 * Class File - Settings Page Class
 *
 * @package BoshDev
 */

namespace Themalizer\Register;

use BoshDev;
use Helper\Tests;
use Helper\Sanitizers;

/**
 * Create theme setting page with options.
 */
class SettingPage {

	use Tests;
	use Sanitizers;


	public $class_default_args = array(
		'page_title' => 'BoshDev Settings Page',
		'menu_title' => 'BoshDev Settings',
		'capability' => 'manage_options',
		'icon_url'   => '',
		'position'   => null,
	);

	/**
	 * Undocumented variable
	 *
	 * @var array
	 */
	public $settings_page_options = array();

	/**
	 * Undocumented function
	 *
	 * @param array $custom_args Args.
	 */
	public function __construct( $custom_args = array() ) {
		self::isset_test( $GLOBALS['BoshDev'], 'You didn\'t initialize BoshDev library' ); // Check if the auto run file was included.

		$this->args = (object) array_merge( $this->class_default_args, $custom_args ); // merge args.
		$this->process_args();

		if ( is_admin() ) {
			$this->add_actions();
		}
	}

	/**
	 * Process the class properties and fill each property with it's value.
	 *
	 * @return void
	 */
	private function process_args() {
		$this->page_title = $this->args->page_title;
		$this->menu_title = $this->args->menu_title;
		$this->capability = $this->args->capability;
		$this->icon_url   = $this->args->icon_url;
		$this->position   = $this->args->position;

		$this->menu_slug     = str_replace( ' ', '_', strtolower( $this->menu_title ) ) . '_slug';
		$this->options_group = str_replace( ' ', '_', strtolower( $this->menu_title ) ) . '_option_group';

		$this->isset_test( $this->args->sections, 'Please add at least one section' );
		$this->sections = $this->args->sections;
		foreach ( $this->sections as $section => $args ) {
			$this->empty_isset_test( $args['title'], 'Please add the title field for "' . $section . '" section.' );
			$this->isset_test( $args['fields'], 'Please add the section fields.' );
			$this->empty_test( $args['fields'], 'Please fill the field arguments' );

			$section_fields = $args['fields'];
			foreach ( $section_fields as $fieldName => $field_args ) {
				$field_args = (object) $field_args;
				$this->empty_isset_test( $field_args->title, 'Please add the title of ' . $fieldName . ' field.' );
				$this->settings_page_options[ $fieldName ] = BoshDev::get( 'prefix' ) . '_' . $fieldName;
			}
		}

		// unsetting the unnessesery properities
		unset( $this->class_default_args, $this->args );
	}

	public function add_actions() {
		add_action( 'admin_menu', array( $this, 'settings_menu' ) );
		add_action( 'admin_init', array( $this, 'initialize_options' ) );
	}

	public function settings_menu() {

		add_menu_page( $this->page_title, $this->menu_title, $this->capability, $this->menu_slug, array( $this, 'echo_the_page' ), $this->icon_url, $this->position );
	}

	public function echo_the_page() {
		if ( ! current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
		}
		?>
		<div class="wrap p-4 bod_settings_page">
			<h1 class="text-center bod_settings_page_title img-thumbnail p-3 mb-5"><?php echo get_admin_page_title(); ?></h1>

			<?php settings_errors(); ?>
			
			<form method="post" action="options.php" id="bod_settings_form">
				<?php settings_fields( $this->options_group ); ?>
				<?php do_settings_sections( $this->menu_slug ); ?>           
				<?php submit_button(); ?>
			</form>
		</div>
		<?php
	}

	public function initialize_options() {
		foreach ( $this->sections as $section => $args ) {
			add_settings_section(
				$section, // section slug
				$args['title'], // section title
				array( $this, 'add_settings_section_callable' ), // callable function to echo section description
				$this->menu_slug // accociate options page
			);

			$section_fields = $args['fields'];
			foreach ( $section_fields as $fieldName => $field_args ) {

				$field_args              = (object) $field_args;
				$field_args->description = isset( $field_args->description ) ? $field_args->description : '';
				$field_args->switchable  = isset( $field_args->switchable ) ? $field_args->switchable : null;
				$field_args->dependent   = isset( $field_args->dependent ) ? $field_args->dependent : null;

				$field_args->type = isset( $field_args->type ) ? $field_args->type : 'text';
				$field_html       = '';
				if ( is_array( $field_args->type ) ) {
					$field_html       = $field_args->type[1];
					$field_args->type = $field_args->type[0];
				}

				add_settings_field(
					$fieldName, // field slug
					$field_args->title, // field title
					array( $this, 'echo_settings_field_callback' ), // echo inputs
					$this->menu_slug, // accociate options page
					$section, // accociate section
					$args = array(
						'label_for'   => $fieldName,
						'type'        => $field_args->type, // type of the input, text by default.
						'fieldName'   => $fieldName,
						'description' => $field_args->description,
						'switchable'  => $field_args->switchable,
						'dependent'   => $field_args->dependent,
						'all_fields'  => $section_fields,
						'html'        => $field_html,
					)
				);

				register_setting(
					$this->options_group, // options group name
					BoshDev::get( 'prefix' ) . '_' . $fieldName, // the option name created by merging the prefix with the fieldname.
					$args = array(
						'sanitize_callback' => array( $this, 'sanitize_inputs' ),
					)
				);

			} // end fields
		} // end sections
	}

	public function add_settings_section_callable( $args ) {

		echo '<button 
	 		type="button" 
	 		class="btn btn-sm rounded-circle p-0 bod_help_icons" 
	 		data-container="body" 
	 		data-toggle="popover" 
	 		data-placement="right" 
	 		data-trigger="hover" 
	 		data-content="' . $this->sections[ $args['id'] ]['description'] . '">
	 			<span class="dashicons dashicons-editor-help"></span>
 		</button>';

	}

	public function echo_settings_field_callback( $args ) {

		$args         = (object) $args;
		$optionName   = BoshDev::get( 'prefix' ) . '_' . $args->fieldName;
		$args->option = get_option( $optionName );
		$field_val    = '';
		if ( $args->option !== false ) {
			$field_val = $args->option;
		}

		$field_switch   = true;
		$switch_message = '';
		if ( $args->switchable !== null || $args->dependent !== null ) {
			if ( $args->dependent !== null ) { // if the dependent field doesn't have value, switch the field off
				$dependent             = get_option( BoshDev::get( 'prefix' ) . '_' . $args->dependent ); // if dependent field is set, get it's value
				$field_switch          = ( $dependent == '' || $dependent == false ) ? false : $field_switch;
				$switching_field_title = $args->all_fields[ $args->dependent ]['title'];
				$switch_message        = ( $dependent == '' || $dependent == false ) ? "This field has been disabled since the '{$switching_field_title}' field doesn't has value." : '';
			} elseif ( $args->switchable !== null ) {
				$switchable            = get_option( BoshDev::get( 'prefix' ) . '_' . $args->switchable ); // if switching field is set, get it's value
				$field_switch          = ( $switchable == '' || $switchable == false ) ? $field_switch : false;
				$switching_field_title = $args->all_fields[ $args->switchable ]['title'];
				$switch_message        = ( $switchable == '' || $switchable == false ) ? "This field has been disabled since the '{$switching_field_title}' field has value." : '';
			}
		}

		if ( $field_switch ) { // if the field does not have switching field and the switching field does not have value, echo the field. else, echo the note and echo the field as a hidden value in order to save the old value of the field.
			if ( empty( $args->html ) ) {
				echo "<input type='{$args->type}' id='{$args->fieldName}' name='{$optionName}' value='{$field_val}'/>";
				echo "<div class='pt-1 pl-1 text-muted'>{$args->description}</div>";
			} else {
				echo $args->html;
				echo "<input type='{$args->type}' id='{$args->fieldName}' name='{$optionName}' value='{$field_val}'/>";
				echo "<div class='pt-1 pl-1 text-muted'>{$args->description}</div>";
			}
		} else {

			echo "<div class='mb-2 text-muted'>{$switch_message}</div>";
			echo "<input type='hidden' id='{$args->fieldName}' name='{$optionName}' value='{$field_val}'/>";
		}

	}

	public function sanitize_inputs( $input ) {
		if ( gettype( $input ) == 'string' ) {
			$input = esc_attr( $input );
		}
		return $input;
	}

	public function get_option_value( $option ) {
		$optionName = $this->settings_page_options[ $option ];

		return get_option( $optionName );
	}

}


