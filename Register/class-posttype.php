<?php

/**
 * Class File - Register Custom Post Type Class
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace Themalizer\Register;

if (!defined('ABSPATH')) {
	exit('You are not allowed to get here, TINKY WINKY!!'); // Exit if accessed directly.
}

use Themalizer\Core\Connector;

/**
 * Post Type class for registering custom post type.
 */
class PostType
{

	/**
	 * The post type singular label.
	 *
	 * @var string
	 */
	private $singular;

	/**
	 * The post type plural label.
	 *
	 * @var string
	 */
	private $plural;

	/**
	 * The post type slug.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * The theme's text-domain.
	 *
	 * @var string
	 */
	private $text_domain;

	/**
	 * The post type arguments
	 *
	 * @var array
	 */
	private $args = array(
		'public'              => true,
		'hierarchical'        => false,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'show_in_rest'        => true,
		'menu_position'       => 5,
		'menu_icon'           => null,
		'capability_type'     => 'post',
		'supports'            => array(
			'title',
			'editor',
			'author',
			'thumbnail',
			'excerpt',
			'custom-fields',
			'trackbacks',
			'comments',
			'revisions',
			'page-attributes',
			'post-formats',
		),
		'taxonomies'          => array('post_tag'),
		'has_archive'         => true,
		'rewrite'             => true,
		'query_var'           => true,
		'can_export'          => true,
	);

	/**
	 * Constructor.
	 *
	 * @param string $singular The post singular label.
	 * @param string $plural The post plural label.
	 * @param string $description The post description.
	 * @param array  $args The rest of the args.
	 * @return void
	 */
	public function __construct($singular, $plural, $description = '', $args = array())
	{
		if (!isset(Connector::container()->custom_post_types)) {
			Connector::container()->custom_post_types = array();
		}

		$this->process_args($singular, $plural, $description, $args);
		$this->add_lables_to_args();

		add_action(
			'init',
			function () {
				register_post_type($this->slug, $this->args);
			}
		);

		Connector::container()->custom_post_types[$this->slug] = $this;
	}

	/**
	 * Process the class provided arguments.
	 *
	 * @param string $singular The post singular label.
	 * @param string $plural The post plural label.
	 * @param string $description The post description.
	 * @param array  $args the post type arguments.
	 * @return void
	 */
	private function process_args($singular, $plural, $description, $args)
	{
		Connector::empty_test($singular, 'Please add the singular name of the post');
		Connector::empty_test($plural, 'Please add the plural name of the post');
		$this->singular     = $singular;
		$this->plural       = $plural;
		$default_taxonomies = $this->args['taxonomies']; // save the default taxonomies to add them later on again so it want be overided.

		// override the value one by one if it's args.
		if (!empty($args)) {
			foreach ($args as $arg_key => $arg_value) {
				$this->args[$arg_key] = $arg_value;
			}
		}

		$this->args['taxonomies'] = array_merge($this->args['taxonomies'], $default_taxonomies); // add the default taxonomies to the provided taxonomies.

		$this->args['description'] = empty($description) ? 'The description is not available.' : $description;

		$this->slug = Connector::$theme_prefix . '_' . str_replace(' ', '_', strtolower($this->singular)); // create post slug.

		$this->text_domain = Connector::$theme_text_domain;
	}

	/**
	 * Process the post type labels and add them to the args.
	 *
	 * @return void
	 */
	private function add_lables_to_args()
	{
		$singular    = $this->singular;
		$plural      = $this->plural;
		$text_domain = $this->text_domain;
		// phpcs:disable
		$this->args['labels'] = array(
			'name'               => __("$plural", "$text_domain"),
			'singular_name'      => __("$singular", "$text_domain"),
			'add_new'            => _x("Add New $singular", "$text_domain", "$text_domain"),
			'add_new_item'       => __("Add New $singular", "$text_domain"),
			'edit_item'          => __("Edit $singular", "$text_domain"),
			'new_item'           => __("New $singular", "$text_domain"),
			'view_item'          => __("View $singular", "$text_domain"),
			'search_items'       => __("Search $plural", "$text_domain"),
			'not_found'          => __("No $plural found", "$text_domain"),
			'not_found_in_trash' => __("No $plural found in Trash", "$text_domain"),
			'all_items'          => __("All $plural", "$text_domain"),
			'archives'           => __("$singular Archives", "$text_domain"),
			'parent_item_colon'  => __("Parent $singular:", "$text_domain"),
			'menu_name'          => __("$plural", "$text_domain"),
		);
		// phpcs:enable
	}

	public function get_post_meta($post_id, $meta_id, $single = true)
	{
		$meta_id = $this->slug . '_meta_' . $meta_id;
		return \get_post_meta($post_id, $meta_id, $single);
	}

	/**
	 * Get the post type slug publicly.
	 *
	 * @return string
	 */
	public function get_slug()
	{
		return $this->slug;
	}
}
