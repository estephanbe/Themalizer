<?php

/**
 * Class File - Metabox Creator Class
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace Themalizer\Luxury;

if (!defined('ABSPATH')) {
	exit('You are not allowed to get here, TINKY WINKY!!'); // Exit if accessed directly.
}

use Themalizer\Core\Connector;

/**
 * Handle images.
 */
class MetaBox
{
	private $box_id;
	private $box_title;
	private $description;
	private $callback_view_type;
	private $post_types = array('post');
	public $meta_key;

	public function __construct(string $box_title,  string $box_type, string $description, array $post_types)
	{
		Connector::empty_test($box_title, 'add the box id');
		Connector::empty_test($box_type, 'add the box type.');
		Connector::empty_test($post_types, 'add the post types array.');

		$this->box_id = Connector::$prefix . "_" . \str_replace(' ', '_', \strtolower($box_title));
		$this->box_title = $box_title;
		$this->post_types = $post_types;
		$this->description = $description;
		$this->meta_key = "_{$this->box_id}_meta_key";

		try {
			switch ($box_type) {
				case 'text':
					$this->callback_view_type = 'text_field';
					break;
				case 'textarea':
					$this->callback_view_type = 'textarea_field';
					break;
				case 'checkbox':
					$this->callback_view_type = 'checkbox_field';
					break;
				default:
					throw new \Exception('This metabox type is not supported yet.');
					break;
			}
		} catch (\Exception $error) {
			echo "<table>$error->xdebug_message</table>";
			die;
		}

		add_action('add_meta_boxes', [$this, 'add_meta_box']);
		add_action('save_post', [$this, 'save_meta_value']);
	}

	function add_meta_box()
	{
		foreach ($this->post_types as $post) {
			add_meta_box(
				$this->box_id,                 // Unique ID
				$this->box_title,      // Box title
				[$this, $this->callback_view_type],  // Content callback, must be of type callable
				$post                            // Post type
			);
		}
	}

	function save_meta_value($post_id)
	{
		// check if the current meta is checkbox type. 
		// if so, check if the key exists in the $_POST. if not, then update the value with null. 
		if (array_key_exists($this->box_id, $_POST)) {
			update_post_meta(
				$post_id,
				$this->meta_key,
				$_POST[$this->box_id]
			);
		} elseif ($this->callback_view_type == 'checkbox_field') {
			update_post_meta(
				$post_id,
				"_{$this->box_id}_meta_key",
				null
			);
		}
	}

	function text_field($post)
	{
		$value = get_post_meta($post->ID, "_{$this->box_id}_meta_key", true);
		echo <<<EOD
		<label for="$this->box_id">$this->description</label>
		<input type="text" name="$this->box_id" value="$value" id="$this->box_id" class="postbox" style="margin-left:1rem;border:1px solid gray;">
		EOD;
	}
	function textarea_field($post)
	{
		$value = get_post_meta($post->ID, "_{$this->box_id}_meta_key", true);
		echo <<<EOD
		<label for="$this->box_id" style="display:flex; margin-bottom: 1rem;">$this->description</label>
		<textarea name="$this->box_id" id="$this->box_id" class="postbox" style="margin-left:1rem;border:1px solid gray;" rows="10">$value</textarea>
		EOD;
	}

	function checkbox_field($post)
	{
		$value = get_post_meta($post->ID, "_{$this->box_id}_meta_key", true);
		$checked = \checked($value, "on", false);
		echo <<<EOD
		<label for="$this->box_id">$this->description</label>
		<input type="checkbox" name="$this->box_id" $checked id="$this->box_id" class="postbox" style="margin-left:1rem;border:1px solid gray;">
		EOD;
	}
}
