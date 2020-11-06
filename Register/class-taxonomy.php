<?php
/**
 * Class File - Register taxonomy Class
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

use Themalizer\Core\Engine;

/**
 * Register new taxonomy.
 */
class Taxonomy extends Engine {

	/**
	 * The taxonomy singular label.
	 *
	 * @var string
	 */
	private $singular;

	/**
	 * The taxonomy plural label.
	 *
	 * @var string
	 */
	private $plural;

	/**
	 * The taxonomy slug.
	 *
	 * @var string
	 */
	private $slug;

	/**
	 * The taxonomy posts scope.
	 *
	 * @var array
	 */
	public $posts_scope = array();

	/**
	 * The taxonomy arguments.
	 *
	 * @var array
	 */
	public $args = array(
		'labels'            => array(),
		'public'            => true,
		'show_ui'           => true,
		'show_in_menu'      => true,
		'show_in_nav_menus' => false,
		'show_in_rest'      => true,
		'show_admin_column' => true,
		'hierarchical'      => true,
		'query_var'         => true,
	);

	/**
	 * Constructor.
	 *
	 * @param string $singular The taxonomy singular label.
	 * @param string $plural The taxonomy plural label.
	 * @param mixed  $posts_scope The taxonomy posts_scope.
	 * @param array  $args The rest of the args.
	 * @return void
	 */
	public function __construct( $singular, $plural, $posts_scope, $args = array() ) {
		$this->process_args( $singular, $plural, $posts_scope, $args );
		$this->generate_labels();

		add_action(
			'init',
			function() {
				register_taxonomy( $this->slug, $this->posts_scope, $this->args );
			}
		);
	}

	/**
	 * Process the class provided arguments.
	 *
	 * @param string $singular The taxonomy singular label.
	 * @param string $plural The taxonomy plural label.
	 * @param mixed  $posts_scope The taxonomy posts_scope.
	 * @param array  $args The rest of the args.
	 * @return void
	 */
	private function process_args( $singular, $plural, $posts_scope, $args ) {
		self::empty_test( $singular, 'Please add the singular name of the taxonomy' );
		self::empty_test( $plural, 'Please add the plural name of the taxonomy' );
		self::empty_test( $posts_scope, 'Empty taxonomy posts_scope' );
		$this->singular = $singular;
		$this->plural   = $plural;

		if ( ! is_array( $posts_scope ) ) {
			$posts_scope = array( $posts_scope );
		}
		$this->posts_scope = $posts_scope;

		// override the value one by one if it's args.
		if ( ! empty( $args ) ) {
			foreach ( $args as $arg_key => $arg_value ) {
				$this->args[ $arg_key ] = $arg_value;
			}
		}

		$this->slug = self::get( 'prefix' ) . '_' . str_replace( ' ', '_', strtolower( $this->singular ) ) . '_tax'; // create taxonomy slug.
	}

	/**
	 * Process the taxonomy labels and add them to the args.
	 *
	 * @return void
	 */
	private function generate_labels() {
		$singular    = $this->singular;
		$plural      = $this->plural;
		$text_domain = self::get( 'text_domain' );
    // phpcs:disable
		$this->args['labels'] = array(
			'name'                  => _x( $plural, $plural, $text_domain ),
			'singular_name'         => _x( $singular, $singular, $text_domain ),
			'search_items'          => __( 'Search ' . $plural, $text_domain ),
			'all_items'             => __( 'All ' . $plural, $text_domain ),
			'parent_item'           => __( 'Parent ' . $singular, $text_domain ),
			'parent_item_colon'     => __( 'Parent ' . $singular, $text_domain ),
			'edit_item'             => __( 'Edit ' . $singular, $text_domain ),
			'update_item'           => __( 'Update ' . $singular, $text_domain ),
			'add_new_item'          => __( 'Add New ' . $singular, $text_domain ),
			'new_item_name'         => __( 'New ' . $singular . ' Name', $text_domain ),
			'add_or_remove_items'   => __( 'Add or remove ' . $plural, $text_domain ),
			'choose_from_most_used' => __( 'Choose from most used ' . $plural, $text_domain ),
			'menu_name'             => __( $plural, $text_domain ),
    );
    // phpcs:enable
	}

	/**
	 * Get the taxonomy slug publicly.
	 *
	 * @return string
	 */
	public function get_slug() {
		return $this->slug;
	}

}
