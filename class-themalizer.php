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

if (!defined('ABSPATH')) {
	exit('You are not allowed to get here, TINKY WINKY!!'); // Exit if accessed directly.
}

use Themalizer\Core\Connector;

/**
 * Including all the classes in the framework
 */

use Themalizer\Core\Constants;
use Themalizer\Core\Init as Initialization;
use Themalizer\Core\Security;
use Themalizer\Register\PostType;
use Themalizer\Register\SettingPage;
use Themalizer\Register\Sidebar;
use Themalizer\Register\Taxonomy;
use Themalizer\Register\ThemeHeader;
use Themalizer\Luxury\Customizer;
use Themalizer\Luxury\Sharing;
use Themalizer\Luxury\ImageHandler;
use Themalizer\Register\RestRoute;

/**
 * Provides direct access to all methods in the framework through static calls. 
 * Any data is being saved in $GLOBALS['BoshDev\Themalizer']
 */
class Themalizer
{

	/**
	 * Call the shared static properties from other classes as a static methods.
	 *
	 * @param string $name
	 * @param array $args
	 * @return void
	 */

	public static function __callStatic($method, $args)
	{

		switch ($method) {
			case 'panel_id':
			case 'text_domain':
			case 'prefix':
			case 'nav_menus':
				return Connector::${'theme_' . $method};
				break;
			case 'development':
				Connector::$development = $args[0];
				break;
			default:
				throw new \Exception('Unavailable static method!');
				break;
		}
	}

	/** =================================== INITIALIZATIONS ===================================== */

	/**
	 * Register a new Init class and initialize the theme along with its constants and security practices.
	 *
	 * @param array $args the initialization arguments.
	 * @return void
	 */
	public static function init($args = array())
	{
		(new Constants());
		(new Security());
		Connector::container()->init = new Initialization($args); // phpcs:ignore
	}

	/**
	 * Create settings page and save it in $GLOBALS.
	 *
	 * @param array $args the settings arguments.
	 * @return void
	 */
	public static function setting($args)
	{
		Connector::empty_test($args, 'Please fill out the settings arguments array.');

		Connector::container()->settings = new SettingPage($args);
	}

	/**
	 * Create custom post type and save it in $GLOBALS.
	 *
	 * @param string $singular The singular name of the post.
	 * @param string $plural The plural name of the post.
	 * @param string $description the description of the post.
	 * @param array $args the rest of the post arguments.
	 * @return object
	 */
	public static function custom_post_type($singular, $plural, $description = '', $args = array())
	{
		if (!isset(Connector::container()->custom_post_types)) {
			Connector::container()->custom_post_types = array();
		}

		Connector::container()->custom_post_types[$singular] = new PostType($singular, $plural, $description, $args);
		return Connector::container()->custom_post_types[$singular];
	}

	/**
	 * Create custom taxonomy and save the resulted object in $GLOBALS
	 *
	 * @param string $singular The singulare name of the taxonomy.
	 * @param string $plural The plural name of the taxonomy.
	 * @param string|array $posts_scope the posts associated with the taxonomy.
	 * @param array $args the taxonomy arguments.
	 * @return void
	 */
	public static function custom_taxonomy($singular, $plural, $posts_scope = '', $args = array())
	{
		if (!isset(Connector::container()->custom_taxonomies)) {
			Connector::container()->custom_taxonomies = array();
		}

		Connector::container()->custom_taxonomies[$singular] = new Taxonomy($singular, $plural, $posts_scope, $args);
	}

	/**
	 * Registering sidebar
	 *
	 * @param array $args the arguments of register_sidebar() WP function.
	 * @return void
	 */
	public static function sidebar($args = array())
	{
		Connector::isset_test($args['name'], 'please add the name of your sidebar');

		if (!isset(Connector::container()->sidebars)) {
			Connector::container()->sidebars = array();
		}

		Connector::container()->sidebars[$args['name']] = new Sidebar($args);
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
	 * @return void
	 */
	public static function customizer($customizer_id, $args)
	{
		Connector::empty_test($customizer_id, 'Please add the customizer id.');

		if (!isset(Connector::container()->customizers)) {
			Connector::container()->customizers = array();
		}

		Connector::container()->customizers[$customizer_id] = new Customizer($args);
	}

	/**
	 * Create sharing buttons.
	 *
	 * @param array $linking_platforms the sharing arguments.
	 * @return array The sharing array.
	 */
	public static function sharing($linking_platforms)
	{
		Connector::empty_test($linkingPlatforms, 'Please fill out the sharing arguments array.');

		if (!isset(Connector::container()->sharing)) {
			Connector::container()->sharing = array();
		}

		// TODO: define Setting->id property
		$new_sharing = new Sharing($linkingPlatforms);

		Connector::container()->sharing[$new_sharing->id] = $new_sharing;
	}

	// public static function nav_walker( $args = array() ) {
	// 	(new NavWalker());
	// }

	public static function register_rest_route(string $endpoint_name, array $class_arguments)
	{
		return new RestRoute($endpoint_name, $class_arguments);
	}


	/** =================================== Getters ===================================== */


	public static function get_customizer($customizer_id, $all = false)
	{
		if ($all) {
			return Connector::container()->customizers;
		}
		return Connector::container()->customizers[$customizer_id];
	}


	public static function get_sharing($sharing_id, $all = false)
	{
		if ($all) {
			return Connector::container()->sharing;
		}
		return Connector::container()->sharing[$sharing_id];
	}

	/**
	 * Get setting page from the container
	 *
	 * @param string $option_id the option name.
	 * @return string
	 */
	public static function get_setting($option_id)
	{
		$setting = Connector::container()->settings->get_option_value($option_id);
		return is_array($setting) ? reset($setting) : false;
	}

	public static function post_type_slug($singular)
	{
		if (!isset(Connector::container()->custom_post_types[$singular])) {
			throw new \Exception('custom post type is not existed');
		}
		return Connector::container()->custom_post_types[$singular]->get_slug();
	}

	public static function get_custom_meta(WP_Post $post_object, string $meta_id, $single = true)
	{
		$post_type = $post_object->post_type;
		if (!isset(Connector::container()->custom_post_types[$post_type])) {
			throw new \Exception('custom post type is not existed');
		}
		return Connector::container()->custom_post_types[$post_type]->get_post_meta($post_object->ID, $meta_id, $single);
	}

	public static function taxonomy_slug($singular)
	{
		if (!isset(Connector::container()->custom_taxonomies[$singular])) {
			throw new \Exception('custom taxonomy is not existed');
		}
		return Connector::container()->custom_taxonomies[$singular]->get_slug();
	}

	/** =================================== HTML MANIPULATION ===================================== */

	/**
	 * Used to echo simple head.
	 *
	 * @return void
	 */
	public static function simple_header()
	{
		ThemeHeader::simple_header();
	}

	/**
	 * Used to echo the top part of the HTML head.
	 *
	 * @param string $html_classes HTML tag classes
	 * @param array $html_attrs HTML tag attributes
	 * @param string $title_seperator title seperator.
	 * @return void
	 */
	public static function start_header($html_classes = '', $html_attrs = array(), $title_seperator = '')
	{
		ThemeHeader::top_of_the_header($html_classes, $html_attrs, $title_seperator);
	}

	/**
	 * used to add CSS links to the head
	 *
	 * @param string $link the css file link
	 * @param boolean $url if the file is exernal.
	 * @return void
	 */
	public static function header_css_link($link, $url = false)
	{
		Connector::empty_test($link, 'add the stylesheet link.');
		ThemeHeader::echo_generated_header_css_link($link, $url);
	}

	/**
	 * Used to echo the WordPress wp_head and prioratize it.
	 * This function is usefull if any HTML to be added after the wp_head function.
	 *
	 * @return void
	 */
	public static function wp_head()
	{
		ThemeHeader::echo_wp_head();
	}

	/**
	 * This function closes the head and start the body tag.
	 *
	 * @param string $body_class body classes.
	 * @param array $attrs body attributes.
	 * @return void
	 */
	public static function close_header($body_class = '', $attrs = array())
	{
		ThemeHeader::bottom_of_the_header($body_class, $attrs);
	}

	/**
	 * This function echos the wp_footer and closes the body and HTML tags.
	 *
	 * @return void
	 */
	public static function footer()
	{
		\wp_footer();
		echo "\r\n</body>\r\n</html>";
	}

	/**
	 * Echo the sidebar
	 *
	 * @param string $sidebar_name the registered sidebar name.
	 * @param array  $jquery jquery manipulations.
	 * @return void
	 */
	public static function echo_sidebar($sidebar_name, $jquery = array())
	{
		if (!isset(Connector::container()->sidebars[$sidebar_name])) {
			throw new \Exception('sidebar is not existed');
		}
		Connector::container()->sidebars[$sidebar_name]->echo($jquery);
	}

	/**
	 * Echo MailChimp form.
	 *
	 * @param array $args
	 * @return void
	 */
	public static function mailchimp_form($args)
	{
		$args            = (object) $args;
		$form_class      = $args->class ? ' class="' . \esc_attr($args->class) . '" ' : '';
		$input_order     = $args->input_order ? $args->input_order : 1;
		$input_attrs     = '';
		$success_message = $args->success_message ? $args->success_message : 'You are subscribed!';
		$failure_message = $args->failure_message ? $args->failure_message : 'Something went wrong, please try again later!';

		if ($args->input && is_array($args->input)) {
			foreach ($args->input as $attr => $attr_value) {
				$input_attrs .= ' ' . $attr . '="' . $attr_value . '" ';
			}
		}

?>
		<form id="themalizer-mailchimp-form" <?php echo $form_class; ?>>
			<?php
			if (1 === $input_order) {
				echo $args->before_input ? $args->before_input : '';
				echo '<input type="email" name="email" ' . $input_attrs . ' required>';
				echo $args->after_input ? $args->after_input : '';
			}

			if ($args->submit) {
				echo $args->submit;
			} else {
				echo '<button type="submit"></button>';
			}

			if (2 === $input_order) {
				echo $args->before_input ? $args->before_input : '';
				echo '<input type="email" name="email" ' . $input_attrs . ' required>';
				echo $args->after_input ? $args->after_input : '';
			}
			?>
			<div id="themalizer-mailchimp-success-message-modal" class="modal">
				<!-- Modal content -->
				<div class="modal-content">
					<span class="close">&times;</span>
					<p><?php echo \esc_html($success_message); ?></p>
				</div>
			</div>
			<div id="themalizer-mailchimp-failure-message-modal" class="modal">
				<!-- Modal content -->
				<div class="modal-content">
					<span class="close">&times;</span>
					<p><?php echo \esc_html($failure_message); ?></p>
				</div>
			</div>
		</form>
<?php
	}



	/** =================================== HELPERS ===================================== */

	/**
	 * Get all the registered menus with their locations.
	 *
	 * @return array
	 */
	public static function get_menus_locations()
	{
		$nav_menus = array_merge(array('primary' => 'Header Menu'), Connector::$theme_nav_menus);
		$locations = array();
		foreach ($nav_menus as $location => $desc) {
			array_push($locations, $location);
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
	public static function add_image_size($slug, $width, $height, $crop = false)
	{
		new ImageHandler($slug, $width, $height, $crop);
	}

	/**
	 * Change the image size of an existed image.
	 *
	 * @param string $url
	 * @param string $size_slug
	 * @return void
	 */
	public static function change_image_size($url, $size_slug)
	{
		return ImageHandler::change_image_size($url, $size_slug);
	}

	/**
	 * Gets the logo uri ************ to be amended to get the logo object
	 *
	 * @param boolean $echo
	 * @return void
	 */
	public static function logo_uri($echo = false)
	{
		$logo_uri = ImageHandler::get_logo()[0];
		if ($echo)
			echo $logo_uri;
		return $logo_uri;
	}

	/**
	 * Generate full URI to the given path for assets directory.
	 *
	 * @param string  $path the path to be appended to the assets URI.
	 * @param boolean $echo switch to echo the path or return it as it is.
	 * @return string   return the path if the switch is False.
	 */
	public static function make_assets_uri($path = '', $echo = true)
	{
		return Connector::make_assets_uri($path, $echo);
	}

	/**
	 * Generate full DIR URI to the given path.
	 *
	 * @param string  $path the path to be appended to the assets URI.
	 * @param boolean $echo switch to echo the path or return it as it is.
	 * @return string   return the path if the switch is False.
	 */
	public static function make_dir_uri($path = '', $echo = true)
	{
		Connector::make_dir_uri($path, $echo);
	}

	public static function get_endpoint(string $endpoint_name, bool $echo = true)
	{
		$endpoint = RestRoute::get_route_url($endpoint_name);
		if (!$echo)
			return $endpoint;
		
		echo $endpoint;
	}
}
