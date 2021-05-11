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

use Themalizer\Core\Connector;

/**
 * Manage the mailchimp.
 */
class Mailchimp {

	/**
	 * API key.
	 *
	 * @var string
	 */
	public $api_key;

	/**
	 * List ID.
	 *
	 * @var string
	 */
	public $list_id;


	/**
	 * Constructor
	 *
	 * @return void
	 */
	function __construct() {
		// Create MailChimp setting page.
		if ( \is_admin() ) {
			\add_action( 'admin_menu', array( $this, 'create_settings_page' ) );
			\add_action( 'admin_menu', array( $this, 'initialize_options' ) );
			if ( isset( $_GET['page'] ) && THEMALIZER_MAILCHIMP_MENU_SLUG === $_GET['page'] ) { // phpcs:ignore
				\add_action( 'admin_head', array( $this, 'add_setting_page_style' ) ); // phpcs:ignore
			}
		}

		// Create custom endpoint to handle the MailChimp submitted emails.
		add_action(
			'rest_api_init',
			function () {
				register_rest_route(
					THEMALIZER_REST_API_NAMESPACE,
					THEMALIZER_REST_API_MAILCHIMP_ENDPOINT,
					array(
						'methods'  => 'POST',
						'callback' => array( $this, 'add_subscriber' ),
					)
				);
			}
		);

		// Handle the MailChimp submitted emails.

		$this->api_key = \get_option( THEMALIZER_MAILCHIMP_API_KEY_OPTION_NAME );
		$this->list_id = \get_option( THEMALIZER_MAILCHIMP_LIST_ID_OPTION_NAME );
	}

	/**
	 * Add subscriber
	 *
	 * @param WP_REST_Request $request request object.
	 * @return void
	 */
	public function add_subscriber( \WP_REST_Request $request ) {

		if ( ! $request['email'] && ! \is_email( $request['email'] ) ) {
			return new \WP_Error( 'invalid_data_submission', 'Invalid Data Submission', array( 'status' => 422 ) );
		}

		$api_key = $this->api_key['text'];
		$list_id = $this->list_id['text'];
		$email   = $request['email'];
		$status  = 'subscribed'; // unsubscribed, cleaned, pending.

		$args     = array(
			'method'  => 'PUT',
			'headers' => array(
				'Authorization' => 'Basic ' . base64_encode( 'user:' . $api_key ),
			),
			'body'    => \wp_json_encode(
				array(
					'email_address' => $email,
					'status'        => $status,
				)
			),
		);
		$api_url  = 'https://' . substr( $api_key, strpos( $api_key, '-' ) + 1 ) . '.api.mailchimp.com/3.0/lists/' . $list_id . '/members/' . md5( strtolower( $email ) );
		$response = \wp_remote_post( $api_url, $args );
		$body     = json_decode( $response['body'] );

		if ( $response['response']['code'] == 200 && $body->status == $status ) {
			return array(
				'code'    => 'successful_subscription',
				'message' => 'Subscriber was added successfully',
				'status'  => 200,
			);
		} else {
			return new \WP_Error( 'invalid_mailchimp_response', 'Invalid MailChimp Response', array( 'status' => 422 ) );
		}
	}



	// ======================== SETTINGS PAGE ==============================

	/**
	 * Register the setting page in the admin menu.
	 *
	 * @return void
	 */
	public function create_settings_page() {
		\add_options_page(
			'MailChimp Settings Page',
			'MailChimp Settings',
			'manage_options',
			THEMALIZER_MAILCHIMP_MENU_SLUG,
			array( $this, 'echo_mailchimp_settings' ),
			null
		);
	}

	/**
	 * Echo MailChimp settings page.
	 *
	 * @return void
	 */
	public static function echo_mailchimp_settings() {
		// Check user capability.
		if ( ! \current_user_can( 'manage_options' ) ) {
			wp_die( __( 'You do not have sufficient permissions to access this page.' ) ); // phpcs:ignore
		}
		?>
		<div id="themalizer_settings_page">
			<h1><?php echo Connector::html_sanitization( \get_admin_page_title() ); // phpcs:ignore ?></h1>

			<?php \settings_errors(); ?>

			<form method="post" action="options.php" id="themalizer_settings_form">
				<?php \settings_fields( 'themalizer_mailchimp_options_group' ); ?>
				<?php \do_settings_sections( THEMALIZER_MAILCHIMP_MENU_SLUG ); ?>
				<?php \submit_button(); ?>
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

		\add_settings_section(
			'themalizer_mailchimp_section', // section slug.
			'MailChimp API Settings', // section title.
			array( $this, 'add_settings_section_description' ), // callable function to echo section description.
			THEMALIZER_MAILCHIMP_MENU_SLUG, // accociate options page.
		);

		$fields = array(
			'mail_chimp_api_key' => array(
				'title'       => 'API Key',
				'description' => 'Add the API key of your MailChimp list.',
			),
			'mail_chimp_list_id' => array(
				'title'       => 'List ID',
				'description' => 'Add the List ID of your MailChimp list.',
			),
		);

		/** Register the fields of the section */
		foreach ( $fields as $field_name => $field_args ) {

			$field_args        = (object) $field_args;
			$field_args->width = 100;
			$field_args->type  = 'text'; // field type is text by default.

			// add the setting filed for the admin page.
			\add_settings_field(
				$field_name, // field slug.
				$field_args->title, // field title.
				array( $this, 'echo_settings_field_callback' ), // echo inputs.
				THEMALIZER_MAILCHIMP_MENU_SLUG, // accociate options page.
				'themalizer_mailchimp_section', // accociate section.
				array(
					'label_for'   => $field_name, // field label.
					'type'        => $field_args->type, // type of the input, text by default.
					'field_name'  => $field_name, // the name of the field.
					'description' => $field_args->description, // the description of the field.
					'all_fields'  => $fields, // all the fields.
					'width'       => $field_args->width, // the field width.
				)
			);

			// Save the input to the database.
			register_setting(
				'themalizer_mailchimp_options_group', // options group name.
				'themalizer_plugin_' . $field_name, // The option name.
				array(
					'default'           => null,
					'sanitize_callback' => array( $this, 'sanitize_inputs' ), // sanitization callback.
				)
			);

		} // end fields.
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
			case 'text':
				if ( isset( $input['text'] ) ) {
					$new_input['text'] = Connector::text_field_sanitization( $input['text'] );
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
	 * @return void
	 */
	public function add_settings_section_description() {
		echo "<div class='themalizer-settings-page-section-description'>
				This section for adding the MailChimp API settings in order to enable the subscriptions.
			</div>";
	}

	/**
	 * Echo the setting filed in the setting page.
	 *
	 * @param array $args same as $args element in add_settings_field function in initialize_options method.
	 * @return void
	 */
	public function echo_settings_field_callback( $args ) {

		$args         = (object) $args; // covert the arguments into obj for easier use.
		$option_name  = 'themalizer_plugin_' . $args->field_name; // generate the option name.
		$option_value = \get_option( $option_name ); // retrive the option value.

		$args->type       = Connector::html_attr_sanitization( $args->type );
		$args->field_name = Connector::html_attr_sanitization( $args->field_name );
		$option_name      = Connector::html_attr_sanitization( $option_name );

		$this->echo_field(
			array(
				'type'         => $args->type,
				'field_name'   => $args->field_name,
				'option_name'  => $option_name,
				'option_value' => $option_value,
				'description'  => $args->description,
				'width'        => $args->width,
			)
		);

	}

	/**
	 * Echo the input field according to its type.
	 *
	 * @param array $args field arguments.
	 * @return void
	 */
	private function echo_field( $args ) {
		$field_type   = $args['type'];
		$field_name   = $args['field_name'];
		$option_name  = $args['option_name'];
		$option_value = $args['option_value'];
		$description  = $args['description'];
		$width        = $args['width'];

		$input_name = $option_name . "[$field_type]";

		$sanitized_option_value = Connector::html_attr_sanitization(
			isset( $option_value[ $field_type ] ) ? $option_value[ $field_type ] : 0
		);
		echo "<input 
		class='themalizer-settings-page-input' 
		style='width: $width%;'
		type='$field_type' 
		id='$field_name' 
		name='$input_name' 
		value='$sanitized_option_value'
		/>";
	}

	/**
	 * Add the style to the setting page.
	 *
	 * @return void
	 */
	public function add_setting_page_style() {
		echo '<style>#themalizer_settings_page{background-color:#fff;padding:2rem;margin:1rem}#themalizer_settings_page>h1{text-align:center;padding:1rem;margin-bottom:4rem;font-size:300%;font-weight:700}#themalizer_settings_page h2{margin-bottom:.5rem;font-size:150%;font-weight:700}#themalizer_settings_page .form-table{background-color:#fafafa;border:1px solid #dee2e6;width:100%;height:auto;border-collapse:separate;border-spacing:1rem}.switch-msgs{color:#6c757d!important}.themalizer-settings-page-input{display:block;width:100%;height:calc(1.5em + .75rem + 2px);padding:.375rem .75rem;font-size:1rem;font-weight:400;line-height:1.5;color:#495057;background-color:#fff;background-clip:padding-box;border:1px solid #ced4da;border-radius:.25rem;transition:border-color .15s ease-in-out,box-shadow .15s ease-in-out}.themalizer-settings-page-input-description{color:#6c757d!important;padding-left:.5rem;padding-top:.5rem}</style>';
	}



}


