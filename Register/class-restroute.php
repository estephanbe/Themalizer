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

use Exception;
use Themalizer\Core\Connector;
use WP_REST_Controller;
use WP_REST_Server;

/**
 * Post Type class for registering custom post type.
 */
class RestRoute extends WP_REST_Controller
{

	public string $route;
	public string $methods;
	public $callback;
	public array $args = array();
	public string $endpoint;

	/**
	 * Constructor.

	 * @return void
	 */
	public function __construct(string $endpoint_name, array $class_arguments)
	{
		$this->process_args($endpoint_name, $class_arguments);
		self::register_rest_route($this->route, $this->callback, $this->methods, $this->args);

		if (!isset(Connector::container()->custom_rest_routes)) {
			Connector::container()->custom_rest_routes = array();
		}

		Connector::container()->custom_rest_routes[$endpoint_name] = $this->route;
	}

	private function process_args(string $endpoint_name, array $class_arguments)
	{
		Connector::empty_isset_test($class_arguments['route'], 'Please add the route!');
		Connector::empty_isset_test($class_arguments['callback'], 'Please add the endpoint!');

		$this->endpoint = $endpoint_name;
		$this->route = $class_arguments['route'];
		$this->callback = $class_arguments['callback'];
		$this->methods = isset($class_arguments['methods']) ? $class_arguments['methods'] : 'read';
		$this->args = isset($class_arguments['args']) ? $class_arguments['args'] : array();
	}

	public static function register_rest_route(string $route, callable $callback, string $methods = 'read', array $args = array())
	{
		// ISSUE: callback can't be tested as it is closure and I can't tell how many args the closure has.
		// if (! $callback() instanceof \WP_REST_Response && ! $callback() instanceof \WP_Error){
		// 	throw new Exception('You must return an instance of WP_REST_Response or WP_Error');
		// }

		//The Following registers an api route with multiple parameters. 
		add_action('rest_api_init', function () use ($route, $methods, $callback, $args) {
			register_rest_route(THEMALIZER_REST_API_NAMESPACE, $route, array(
				'methods' => self::get_method($methods),
				'permission_callback' => '__return_true',
				'callback' => $callback,
				'args' => $args
			));
		});

		return get_rest_url() . THEMALIZER_REST_API_NAMESPACE . $route;
	}

	private static function get_method(string $methods)
	{
		switch ($methods) {
			case 'read':
				return WP_REST_Server::READABLE;
				break;
			case 'create':
				return WP_REST_Server::CREATABLE;
				break;
			case 'edit':
				return WP_REST_Server::EDITABLE;
				break;
			case 'delete':
				return WP_REST_Server::DELETABLE;
				break;
			case 'all':
				return WP_REST_Server::ALLMETHODS;
				break;
			default:
				return WP_REST_Server::READABLE;
				break;
		}
	}

	public static function get_route_object(string $endpoint_name)
	{
		if (!isset(Connector::container()->custom_rest_routes)) {
			throw new Exception('There is no endpoint was registered yet');
		} elseif (!isset(Connector::container()->custom_rest_routes[$endpoint_name])) {
			throw new Exception('No route was registered for "' . $endpoint_name . '" endpoint yet!');
		}

		$endpoint = Connector::container()->custom_rest_routes[$endpoint_name];
		return $endpoint;
	}

	public static function get_route_url(string $endpoint_name)
	{
		if (!isset(Connector::container()->custom_rest_routes)) {
			throw new Exception('There is no endpoint was registered yet');
		} elseif (!isset(Connector::container()->custom_rest_routes[$endpoint_name])) {
			throw new Exception('No route was registered for "' . $endpoint_name . '" endpoint yet!');
		}
		var_dump(Connector::container()->custom_rest_routes);
		die;
		$endpoint = Connector::container()->custom_rest_routes[$endpoint_name]->route;
		return get_rest_url() . THEMALIZER_REST_API_NAMESPACE . $endpoint;
	}
}
