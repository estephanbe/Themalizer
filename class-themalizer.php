<?php
/**
 * Themalizer Class
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

use Themalizer\Core\Engine as Engine;

/**
 * Provides direct access to all methods in the framework through static calls
 */
class Themalizer extends Engine {



	/** ================ INITIALIZATIONS ================ */


	/**
	 * Register a new Init class in $GLOBALS and overrides the previous
	 *
	 * @param array $args the initialization params.
	 * @return void Initialization object.
	 */
	public static function init( $args = array() ) {
		self::initialize_theme( $args );
	}

	/**
	 * Create Settings Page.
	 *
	 * @param array $args the settings arguments.
	 * @return void
	 */
	public static function setting( $args ) {
		self::initialize_setting_page( $args );
	}

	public static function custom_post_type( $singular, $plural, $description = '', $args = array() ) {
		self::initialize_custom_post_type( $singular, $plural, $description, $args );
	}

	public static function custom_taxonomy( $singular, $plural, $posts_scope = '', $args = array() ) {
		self::initialize_custom_taxonomy( $singular, $plural, $posts_scope, $args );
	}

	/**
	 * Registering Sidebar
	 *
	 * @param array $args the arguments of register_sidebar() WP function.
	 * @return void
	 */
	public static function sidebar( $args = array() ) {
		self::initialize_sidebar( $args );
	}

	public static function nav_walker( $args = array() ) {
		return self::initialize_nav_walker();
	}

	public static function simple_header() {
		self::echo_simple_header();
	}

	public static function start_header( $html_classes = '', $title_seperator = '' ) {
		self::echo_start_header( $html_classes, $title_seperator );
	}

	public static function header_css_link( $link, $url = false ) {
		self::empty_test( $link, 'add the stylesheet link.' );
		self::generate_header_css_link( $link, $url );
	}

	public static function wp_head() {
		self::prioratize_wp_head();
	}

	public static function close_header( $body_class = '' ) {
		self::echo_close_header( $body_class );
	}

	public static function footer() {
		\wp_footer();
		echo "\r\n</body>\r\n</html>";
	}

	/**
	 * Get setting page from the container
	 *
	 * @param string $option_id the option name.
	 * @return string
	 */
	public static function get_setting( $option_id ) {
		$setting = self::get_container()->settings->get_option_value( $option_id );
		return is_array( $setting ) ? reset( $setting ) : false;
	}

	public static function post_type_slug( $singular ) {
		if ( ! isset( self::get_container()->custom_post_types[ $singular ] ) ) {
			throw new \Exception( 'custom post type is not existed' );
		}
		return self::get_container()->custom_post_types[ $singular ]->get_slug();
	}

	public static function taxonomy_slug( $singular ) {
		if ( ! isset( self::get_container()->custom_taxonomies[ $singular ] ) ) {
			throw new \Exception( 'custom taxonomy is not existed' );
		}
		return self::get_container()->custom_taxonomies[ $singular ]->get_slug();
	}

	/**
	 * Echo the sidebar
	 *
	 * @param string $sidebar_name the registered sidebar name.
	 * @param array  $jquery jquery manipulations.
	 * @return void
	 */
	public static function echo_sidebar( $sidebar_name, $jquery = array() ) {
		if ( ! isset( self::get_container()->sidebars[ $sidebar_name ] ) ) {
			throw new \Exception( 'sidebar is not existed' );
		}
		self::get_container()->sidebars[ $sidebar_name ]->echo( $jquery );
	}

	/**
	 * Create Customizer Object which is the customized section in the theme's panel.
	 *
	 * - $args['title'] string The customizer title in the panel.
	 * - $args['description'] string The customizer description in the panel.
	 * - $args['args'] array The customizer.
	 * - $args['settings'] array Settings array which defines the controls and settings.
	 * - - $args['settings'][$setting_slug] array Defines the arguments to initiate the single setting inside the section.
	 * - - $setting_slug: The setting slug which will be used to call the registered setting:
	 * - - - $args['settings'][$setting_slug]['selector'] The selection which will be used to initiate the setting and link it with the partial view.
	 * - - - $args['settings'][$setting_slug]['control'] The control arguments, take the following arguments plus the default control arguments.
	 * - - - - $args['settings'][$setting_slug]['control']['label'] The control label.
	 * - - - - $args['settings'][$setting_slug]['control']['type'] The control type.
	 *
	 * @param string $customizer_id the customizer name which will be used to call the customizer.
	 * @param array  $args (see above).
	 * @return object
	 */
	public static function customizer( $customizer_id, $args ) {
		return self::initialize_customizer( $customizer_id, $args );
	}

	public static function get_customizer( $customizer_id, $all = false ) {
		if ( $all ) {
			return self::get_container()->customizers;
		}
		return $GLOBALS['BoshDev\Themalizer']->customizers[ $customizer_id ];
	}

	/**
	 * Create sharing buttons.
	 *
	 * @param array $linking_platforms the sharing arguments.
	 * @return array The sharing array.
	 */
	public static function sharing( $linking_platforms ) {
		return self::initialize_sharing( $linking_platforms );
	}

	public static function get_sharing( $sharing_id, $all = false ) {
		if ( $all ) {
			return self::get_container()->sharing;
		}
		return self::get_container()->sharing[ $sharing_id ];
	}

	/** ================ HELPERS ================ */

	/**
	 * Get all the registered menus with their locations.
	 *
	 * @return array
	 */
	public static function get_menus_locations() {
		$nav_menus = array_merge( array( 'primary' => 'Header Menu' ), $GLOBALS['BoshDev\Themalizer']->init->get( 'nav_menus' ) );
		$locations = array();
		foreach ( $nav_menus as $location => $desc ) {
			array_push( $locations, $location );
		}
		return $locations;
	}

	/**
	 * Register image size.
	 *
	 * @param string  $slug the image size name.
	 * @param int     $width the image width.
	 * @param int     $hight the image hieght.
	 * @param boolean $crop if the image should be croped or not.
	 * @return void
	 */
	public static function add_image_size( $slug, $width, $height, $crop = false ) {
		self::generate_new_image_size( $slug, $width, $height, $crop );
	}

	public static function change_image_size( $url, $size_slug ) {
		return self::customized_image_size( $url, $size_slug );
	}

	public static function mailchimp_form( $args ) {
		$args            = (object) $args;
		$form_class      = $args->class ? ' class="' . \esc_attr( $args->class ) . '" ' : '';
		$input_order     = $args->input_order ? $args->input_order : 1;
		$input_attrs     = '';
		$success_message = $args->success_message ? $args->success_message : 'You are subscribed!';
		$failure_message = $args->failure_message ? $args->failure_message : 'Something went wrong, please try again later!';

		if ( $args->input && is_array( $args->input ) ) {
			foreach ( $args->input as $attr => $attr_value ) {
				$input_attrs .= ' ' . $attr . '="' . $attr_value . '" ';
			}
		}

		?>
		<form id="themalizer-mailchimp-form" <?php echo $form_class; ?>>
			<?php
			if ( 1 === $input_order ) {
				echo $args->before_input ? $args->before_input : '';
					echo '<input type="email" name="email" ' . $input_attrs . ' required>';
				echo $args->after_input ? $args->after_input : '';
			}

			if ( $args->submit ) {
				echo $args->submit;
			} else {
				echo '<button type="submit"></button>';
			}

			if ( 2 === $input_order ) {
				echo $args->before_input ? $args->before_input : '';
					echo '<input type="email" name="email" ' . $input_attrs . ' required>';
				echo $args->after_input ? $args->after_input : '';
			}
			?>
			<div id="themalizer-mailchimp-success-message-modal" class="modal">
				<!-- Modal content -->
				<div class="modal-content">
					<span class="close">&times;</span>
					<p><?php echo \esc_html( $success_message ); ?></p>
				</div>
			</div>
			<div id="themalizer-mailchimp-failure-message-modal" class="modal">
				<!-- Modal content -->
				<div class="modal-content">
					<span class="close">&times;</span>
					<p><?php echo \esc_html( $failure_message ); ?></p>
				</div>
			</div>
		</form>
		<?php
	}
}
