<?php

/**
 * Class File - Register sidebar Class
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
 * Register sidebar class.
 */
class Sidebar
{

	/**
	 * Sidebar name.
	 *
	 * @var string
	 */
	private $name = '';

	/**
	 * Sidebar uniqe id.
	 *
	 * @var string
	 */
	private $id = '';

	/**
	 * Sidebar description.
	 *
	 * @var string
	 */
	private $description = 'Sidebar does not have description.';

	/**
	 * Sidebar class.
	 *
	 * @var string
	 */
	private $class = '';

	/**
	 * Sidebar before widget html.
	 *
	 * @var string
	 */
	private $before_widget = '<div>';

	/**
	 * Sidebar after widget html.
	 *
	 * @var string
	 */
	private $after_widget = '</div>';

	/**
	 * Before title html.
	 *
	 * @var string
	 */
	private $before_title = '<h2>';

	/**
	 * After title html.
	 *
	 * @var string
	 */
	private $after_title = '</h2>';

	/**
	 * Jquery manipulation container.
	 *
	 * @var array
	 */
	private $jquery = array();

	/**
	 * The constructor
	 *
	 * @param array $args the sidebar arguments.
	 * @return void
	 */
	public function __construct($args = array())
	{
		$this->process_args($args);

		add_action(
			'widgets_init',
			function () {
				register_sidebar(
					array(
						'name'          => $this->name,
						'id'            => $this->id,
						'description'   => $this->description,
						'class'         => $this->class,
						'before_widget' => $this->before_widget,
						'after_widget'  => $this->after_widget,
						'before_title'  => $this->before_title,
						'after_title'   => $this->after_title,
					)
				);
			}
		);
	}

	/**
	 * Process sidebar arguments.
	 *
	 * @param array $custom_args Sidebar arguments.
	 * @return void
	 */
	private function process_args($custom_args)
	{
		if (!empty($custom_args)) {
			foreach ($custom_args as $property => $value) {
				$this->{$property} = $value;
			}
		}
		$this->id = Connector::$prefix . '_' . strtolower(str_replace(' ', '_', $this->name)) . '_sidebar';
	}

	/**
	 * Echo the sidebar.
	 *
	 * @param array $jquery jQuery amendments to the sidebar.
	 * @return void
	 */
	public function echo($jquery = array())
	{

		if (is_active_sidebar($this->id)) {

			dynamic_sidebar($this->id); // echo the sidebar.

			if (!empty($jquery)) {
				$this->jquery = $jquery;
				add_action('wp_print_footer_scripts', array($this, 'dom_script')); // add the jquery scripts.
			}
		}
	}

	/**
	 * Jquery manipulation to the sidebar.
	 *
	 * @return void
	 */
	public function dom_script()
	{
		echo '<script>jQuery(document).ready(function($){';
		foreach ($this->jquery as $selector => $action) { // lood through jQuery set.
			if (!is_array($action)) { // if action is str, then the method has no value like .show().
				echo "$('$selector').$action();"; // phpcs:ignore
			} else { // the action is a method which has value.
				foreach ($action as $method => $method_value) { // loop through each method to apply it on the selector.
					if (!is_array($method_value)) { // if the method value is str, the method has one value;.
						echo "$('$selector').$method('$method_value');"; // phpcs:ignore
					} else { // the method value is an array with a key as the first method value and a value as the second method value.
						foreach ($method_value as $method_args) {
							$args = '';
							foreach ($method_args as $method_single_arg) {
								$args .= "'$method_single_arg',";
							}
							echo "$('$selector').$method($args);"; // phpcs:ignore
						}
					}
				}
			}
		}
		echo '});</script>';
	}

	/**
	 * Get the sidebar id publicly
	 *
	 * @return string
	 */
	public function get_id()
	{
		return $this->id;
	}
}
