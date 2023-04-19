<?php

/**
 * Class File - Init
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace  Themalizer\Core;

use Themalizer\Luxury\MailChimp as MailChimp;
use Themalizer\Register\RestRoute;
use WP_REST_Request;
use WP_REST_Response;

if (!defined('ABSPATH')) {
	exit('You are not allowed to get here, TINKY WINKY!!'); // Exit if accessed directly.
}

/**
 * Initialize all the necessary actions and processes for any WordPress theme according to the provided arguments.
 */
class Init
{

	/**
	 * The theme object.
	 * WordPress value.
	 *
	 * @var object
	 */
	private $theme;

	/**
	 * The theme stylesheet directory.
	 * WordPress value.
	 *
	 * @var object
	 */
	private $dir;

	/**
	 * The theme stylesheet directory URI.
	 * WordPress value.
	 *
	 * @var object
	 */
	private $dir_uri;

	/**
	 * Represents the prefix of the theme which used to build a uniqe sets of methods and references.
	 * Customizable.
	 *
	 * @var string 'bod'
	 */
	private $prefix = 'bod';

	/**
	 * The assets directory name which contains the _front-end directories and files.
	 * Customizable.
	 *
	 * @var string 'assets'
	 */
	private $assets_dir_name = 'assets';

	/**
	 * The assets directory.
	 *
	 * @var string
	 */
	private $assets_directory = '';

	/**
	 * The assets directory URI.
	 *
	 * @var string
	 */
	private $assets_dir_uri = '';

	/**
	 * The theme version.
	 * WordPress value.
	 *
	 * @var string
	 */
	private $version = '';

	/**
	 * The theme text_domain.
	 * WordPress value.
	 *
	 * @var string
	 */
	private $text_domain = '';

	/**
	 * The singular and plurar names of the default "Post" label.
	 * Customizable.
	 *
	 * @var array
	 */
	private $change_post_label_name = array();

	/**
	 * The theme's main stylesheet name, the id of the stylesheet.
	 *
	 * @var string
	 */
	private $stylesheet_name = '';

	/**
	 * The theme's admin customization stylesheet name, the id of the stylesheet.
	 *
	 * @var string
	 */
	private $admin_stylesheet_name = '';

	/**
	 * The theme's main js name, the id of the script file.
	 *
	 * @var string
	 */
	private $script_name = '';

	/**
	 * The theme's admin customization script name, the id of the script file.
	 *
	 * @var string
	 */
	private $admin_script_name = '';

	/**
	 * The theme's js file name, the file name without the extenstion.
	 * It exists if the user want to save the file in another name.
	 * Customizable.
	 *
	 * @var string 'main'
	 */
	private $script_file_name = 'main';

	/**
	 * The theme's js file name container, after processing.
	 *
	 * @var string
	 */
	private $main_script_file_name = '';

	/**
	 * The theme's admin customization js file name, it can't be amended.
	 * It holds the same name as the script file name prepended with _admin.
	 *
	 * @var string
	 */
	private $admin_script_file_name = '';

	/**
	 * The theme's scripts directory URI.
	 * If left empty, the script directory will be set to the default directory name; js.
	 * Customizable.
	 *
	 * @var string
	 */
	private $script_dir = '';

	/**
	 * The theme's script file source.
	 *
	 * @var string
	 */
	private $js_src = '';

	/**
	 * The theme's admin stylsheet file source.
	 *
	 * @var string
	 */
	private $admin_css_src = '';

	/**
	 * The theme's admin script file source.
	 *
	 * @var string
	 */
	private $admin_js_src = '';

	/**
	 * Holds the value of the posts per page which was set by the user.
	 * WordPress value.
	 *
	 * @var integer 0
	 */
	private $posts_per_page = 0;

	/**
	 * Array of posts formats which will be enables in the theme.
	 * Customizable.
	 *
	 * @var array
	 */
	private $post_formats = array();

	/**
	 * Theme extra menus, goes by "location" => "description".
	 * Customizable.
	 * Default is: "primary" => "Header Menu"
	 *
	 * @var array
	 */
	private $nav_menus = array();

	/**
	 * Switch to enable the administration view customized scripts and stylesheets.
	 * Customizable.
	 *
	 * @var boolean false
	 */
	private $support_admin_script = false;

	/**
	 * Switch for the post thumbnail theme support.
	 * Customizable.
	 *
	 * @var boolean true
	 */
	private $post_thumbnails = true;

	/**
	 * Switch for the custom_logo theme support.
	 * Customizable.
	 *
	 * @var boolean true
	 */
	private $custom_logo = true;

	/**
	 * Switch for the automatic_feed_links theme support.
	 * Customizable.
	 *
	 * @var boolean true
	 */
	private $automatic_feed_links = true;

	/**
	 * Switch for the html5 theme support.
	 * Customizable.
	 *
	 * @var boolean true
	 */
	private $html5 = true;

	/**
	 * Switch for the title_tag theme support.
	 * Customizable.
	 *
	 * @var boolean true
	 */
	private $title_tag = true;

	/**
	 * Switch for the customize_selective_refresh_widgets theme support.
	 * Customizable.
	 *
	 * @var boolean true
	 */
	private $customize_selective_refresh_widgets = true;

	/**
	 * The theme customizer panel info.
	 *
	 * @var array
	 */
	public $customizer_panel = array();

	/**
	 * The theme customizer panel generated id.
	 *
	 * @var string
	 */
	public $panel_id = '';

	/**
	 * Enable MailChimp Support.
	 *
	 * @var boolean
	 */
	private $mailchimp = false;

	/**
	 * array of js vars to be enqueued.
	 *
	 * @var array
	 */
	public static $js_vars = array();


	/**
	 * Set of cusomizable properties.
	 *
	 * @var array
	 */
	private $customizable_properties = array(
		'prefix',
		'assets_dir_name',
		'script_dir',
		'script_file_name',
		'post_formats',
		'post_thumbnails',
		'custom_logo',
		'automatic_feed_links',
		'html5',
		'title_tag',
		'customize_selective_refresh_widgets',
		'nav_menus',
		'change_post_label_name',
		'support_admin_script',
		'customizer_panel',
		'mailchimp'
	);

	/**
	 * Process properties and initialize the actions
	 *
	 * @param array $custom_args if there is any amendments.
	 */
	public function __construct($custom_args = array())
	{
		$this->process_args($custom_args);

		define('THEMALIZER_THEME_PREFIX', $this->prefix);
		define('THEMALIZER_STYLE_NAME', $this->stylesheet_name);
		define('THEMALIZER_SCRIPT_NAME', $this->script_name);
		define('THEMALIZER_THEME_VERSION', $this->version);
		define('THEMALIZER_ENQ_PRIORITY', 100);

		$this->make_panel();
		$this->add_initial_actions();

		if ($this->mailchimp) {
			$this->mailchimp_support();
		}

		$this->initiate_helpers_endpoints();

		$this->setup_connector_props();

		if (class_exists('WooCommerce')) {
			add_action(
				'after_setup_theme',
				function () {
					add_theme_support('woocommerce');
				}
			);
		}
	}

	/**
	 * Get the value of protected property
	 *
	 * @param String $property the needed property.
	 * @return mixed The property value
	 */
	public function get_property($property)
	{
		return $this->{$property};
	}

	/**
	 * Process the class properties and fill each property with it's value.
	 *
	 * @param array $custom_args array of customizable properties values.
	 * @return void
	 */
	private function process_args($custom_args)
	{

		if (!empty($custom_args)) {
			foreach ($custom_args as $property => $value) {
				if (in_array($property, $this->customizable_properties, true)) {
					$this->{$property} = $value;
				}
			}
		}

		$this->theme                  = wp_get_theme(); // add the theme object.
		$this->dir                    = get_template_directory() . '/'; // get the theme directory.
		$this->dir_uri                = get_template_directory_uri() . '/'; // get the theme directory URI.
		$this->posts_per_page         = get_option('posts_per_page'); // get the theme posts_per_page_option.
		$this->version                = $this->theme->get('Version'); // get the theme version.
		$this->text_domain            = $this->theme->get('TextDomain'); // get the theme textDomain.
		$this->assets_directory       = $this->dir . $this->assets_dir_name . '/'; // compose the assets directory.
		$this->assets_dir_uri         = $this->dir_uri . $this->assets_dir_name . '/'; // compose the assets directory URI.
		$this->stylesheet_name        = $this->prefix . '_style'; // compose the stylesheet name.
		$this->admin_stylesheet_name  = $this->prefix . '_admin_style'; // compose the admin stylesheet name.
		$this->script_name            = $this->prefix . '_script'; // compose the script name.
		$this->admin_script_name      = $this->prefix . '_admin_script'; // compose the admint script name.
		$this->main_script_file_name  = $this->script_file_name . '.js'; // compose the scipt file name.
		$this->admin_script_file_name = $this->script_file_name . '_admin.js'; // compose the admin script file name.
		$this->script_dir             = empty($this->script_dir) ? $this->assets_dir_uri : $this->dir_uri . $this->script_dir; // compose the script dir URI.
		$this->js_src                 = $this->script_dir . $this->main_script_file_name; // compose the js file URI source.
		$this->admin_css_src          = $this->assets_dir_uri . 'css/admin.css'; // compose the admin stylesheet source.
		$this->admin_js_src           = $this->script_dir . $this->admin_script_file_name; // compose the admin script source.

		Connector::$text_domain = $this->text_domain;
		Connector::$prefix = $this->prefix;
	}

	/**
	 * Add the necessary actions for the theme
	 *
	 * @return void
	 */
	private function add_initial_actions()
	{
		add_action('after_setup_theme', array($this, 'theme_supports'));
		add_action('after_setup_theme', array($this, 'theme_nav_menus'));
		add_action('init', array($this, 'change_post_object_name'));
		add_action('wp_enqueue_scripts', array($this, 'add_basic_theme_scripts'), THEMALIZER_ENQ_PRIORITY);
		add_action('admin_enqueue_scripts', array($this, 'add_admin_theme_scripts'), THEMALIZER_ENQ_PRIORITY);
	}

	/**
	 * A method to generat the theme customizer panel if $customizer_panel was not empty.
	 *
	 * @return void
	 */
	private function make_panel()
	{
		if (!empty($this->customizer_panel)) {
			Connector::empty_test($this->customizer_panel, 'Make sure args is not empty');
			Connector::empty_isset_test($this->customizer_panel['title'], 'Make sure panel title is added to the args and is not empty');
			Connector::empty_isset_test($this->customizer_panel['description'], 'Make sure panel description is added to the args and is not empty');

			$this->panel_id = $this->prefix . '_customizer_panel_' . str_replace(' ', '_', strtolower($this->customizer_panel['title']));
			Connector::$panel_id = $this->panel_id;
			add_action(
				'customize_register',
				function ($wp_customize) {
					$wp_customize->add_panel(
						$this->panel_id,
						$this->customizer_panel
					);
				}
			);
		}
	}

	/**
	 * Add the necessary theme supports to be used with add_initial_actions().
	 *
	 * @return void
	 */
	public function theme_supports()
	{
		!empty($this->post_formats) ? add_theme_support('post-formats', $this->post_formats) : '';
		$this->post_thumbnails ? add_theme_support('post-thumbnails') : '';
		$this->automatic_feed_links ? add_theme_support('automatic-feed-links') : '';
		$this->html5 ? add_theme_support('html5', array('comment-list', 'comment-form', 'search-form', 'gallery', 'caption')) : '';
		$this->title_tag ? add_theme_support('title-tag') : '';
		$this->customize_selective_refresh_widgets ? add_theme_support('customize-selective-refresh-widgets') : '';
		$this->custom_logo ? add_theme_support('custom-logo') : '';
	}

	/**
	 * Register the theme menues.
	 *
	 * @return void
	 */
	public function theme_nav_menus()
	{
		$nav_menus = array_merge(array('primary' => 'Header Menu'), $this->nav_menus);
		Connector::$nav_menus = $nav_menus;
		foreach ($nav_menus as $location => $description) {
			register_nav_menu($location, __($description, $this->text_domain)); // phpcs:ignore
		}
	}

	/**
	 * Enqueue the syle and script.
	 *
	 * @return void
	 */
	public function add_basic_theme_scripts()
	{
		wp_enqueue_style($this->stylesheet_name, get_stylesheet_uri(), array(), $this->version);
		wp_enqueue_script(THEMALIZER_SCRIPT_NAME, $this->js_src, array('jquery'), $this->version, true);
		wp_localize_script(
			THEMALIZER_SCRIPT_NAME,
			"ThemalizerJsVars",
			self::$js_vars
		);
	}

	/**
	 * Add the admin theme scripts if the support_admin_script switch was enabled.
	 *
	 * @param [type] $hook_suffix @ticket To be checked later.
	 * @return void
	 */
	public function add_admin_theme_scripts($hook_suffix)
	{
		if ($this->support_admin_script) {
			wp_enqueue_media();
			wp_enqueue_style($this->admin_stylesheet_name, $this->admin_css_src, array(), $this->version);
			wp_enqueue_script($this->admin_script_name, $this->admin_js_src, array(), $this->version, true);
		}
	}

	/**
	 * Change the "Post" lable
	 *
	 * @return void
	 */
	public function change_post_object_name()
	{
		if (!empty($this->change_post_label_name) && is_array($this->change_post_label_name)) {

			Connector::isset_test($this->change_post_label_name[0], 'Add the Post Lable Single Name');
			Connector::isset_test($this->change_post_label_name[1], 'Add the Post Lable Plurar Name');
			$single = $this->change_post_label_name[0];
			$plurar = $this->change_post_label_name[1];

			$get_post_type = get_post_type_object('post');
			$labels        = $get_post_type->labels;

			$labels->name               = "$plurar";
			$labels->singular_name      = "$plurar";
			$labels->add_new            = "Add $single";
			$labels->add_new_item       = "Add $single";
			$labels->edit_item          = "Edit $single";
			$labels->new_item           = "$single";
			$labels->view_item          = "View $plurar";
			$labels->search_items       = "Search $plurar";
			$labels->not_found          = "No $plurar found";
			$labels->not_found_in_trash = "No $plurar found in Trash";
			$labels->all_items          = "All $plurar";
			$labels->menu_name          = "$plurar";
			$labels->name_admin_bar     = "$plurar";
		}
	}

	public function mailchimp_support()
	{
		(new MailChimp());

		add_action(
			'wp_enqueue_scripts',
			function () {
				$url    = Connector::mailchimp_action_url(false);
				$script = <<<EOD
				jQuery(document).ready(($) => {
					window.addEventListener('DOMContentLoaded', function() {
						$(document).ready(function () {
							var successModal = document.getElementById("themalizer-mailchimp-success-message-modal");
							var failureModal = document.getElementById("themalizer-mailchimp-failure-message-modal");
							// Get the <span> element that closes the modal
							var closeBtnSuccess = document.querySelectorAll('#themalizer-mailchimp-success-message-modal .close')[0];
							var closeBtnFailure = document.querySelectorAll('#themalizer-mailchimp-failure-message-modal .close')[0];
							closeBtnSuccess.onclick = function() {
								successModal.style.display = "none";
							}
							closeBtnFailure.onclick = function() {
								failureModal.style.display = "none";
							}
							// When the user clicks anywhere outside of the modal, close it
							window.onclick = function(event) {
								if ( event.target == successModal ) {
									successModal.style.display = "none";
								} else if ( event.target == failureModal ) {
									failureModal.style.display = "none";
								}
							}
							$('#themalizer-mailchimp-form').submit(function(e){
								e.preventDefault();
								var email = $('#themalizer-mailchimp-form input[name=email]').val();
								$.ajax({
									type: "POST",
									url: "$url",
									data: JSON.stringify({"email": email}),
									dataType: 'json',
									contentType: 'application/json',
									success: function (response) {
										if ( 200 === response.status ) {
											successModal.style.display = "block";
											$('#themalizer-mailchimp-form input[name=email]').val('');
										}
									},
									error: function (request, status, error) {
										failureModal.style.display = "block";
										$('#themalizer-mailchimp-form input[name=email]').val('');
									}
								});
							});
						});
					});
				});
				EOD;
				\wp_add_inline_script(THEMALIZER_SCRIPT_NAME, $script);
				\wp_add_inline_style(THEMALIZER_STYLE_NAME, '#themalizer-mailchimp-form .modal{display:none;position:fixed;z-index:1;left:0;top:0;width:100%;height:100%;overflow:auto;background-color:#000;background-color:rgba(0,0,0,.4)}#themalizer-mailchimp-form .modal-content{background-color:#fefefe;margin:15% auto;padding:20px;border:1px solid #888;width:20%}#themalizer-mailchimp-form .modal-content p{text-align:center}#themalizer-mailchimp-form .close{color:#aaa;float:right;font-size:28px;font-weight:700}#themalizer-mailchimp-form .close:focus,#themalizer-mailchimp-form .close:hover{color:#000;text-decoration:none;cursor:pointer}');
			}
		);
	}

	private function initiate_helpers_endpoints()
	{
		Connector::container()->custom_rest_routes['check_existed_url'] = new RestRoute(
			'check_existed_url',
			array(
				'route' => 'check_existed_url', // add pregmatch for url to pass to the checker
				'permission_callback' => '__return_true',
				'callback' => function (\WP_REST_Request $request) {
					$url_to_be_tested = $request->get_param('url');
					if (empty($url_to_be_tested))
						return new \WP_Error('missing_url_param', 'Please addd "url" query parameter to the route!', array('success' => false));

					if (strpos($url_to_be_tested, "http://") !== 0 && strpos($url_to_be_tested, "https://") !== 0) {
						$url_to_be_tested = 'http://' . $url_to_be_tested;
					}

					return new \WP_REST_Response(
						array(
							'valid_url' => Connector::existed_url_test($url_to_be_tested),
							'url' => $url_to_be_tested,
							'success' => true
						)
					);
				}
			)
		);
	}

	private function setup_connector_props()
	{
		Connector::$text_domain = $this->text_domain;
		Connector::$prefix = $this->prefix;
		Connector::$nav_menus = $this->nav_menus;
		Connector::$panel_id = $this->panel_id;
		Connector::$script_name = $this->script_name;
	}
}
