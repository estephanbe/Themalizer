<?php
/**
 * Engine Class
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace BoshDev\Themalizer\Core;

if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}



/**
 * Including all the classes in the framework
 */
use BoshDev\Themalizer\Core\Init as Initialization;
use BoshDev\Themalizer\Core\Security as Security;
use BoshDev\Themalizer\Register\PostType as PostType;
use BoshDev\Themalizer\Register\SettingPage as Setting;
use BoshDev\Themalizer\Register\Sidebar as Sidebar;
use BoshDev\Themalizer\Register\Taxonomy as Taxonomy;
use BoshDev\Themalizer\Luxury\Customizer as Customizer;
use BoshDev\Themalizer\Luxury\Facebook as Facebook;
use BoshDev\Themalizer\Luxury\LoadMorePosts as LoadMorePosts;
use BoshDev\Themalizer\Luxury\MailChimp as MailChimp;
use BoshDev\Themalizer\Luxury\Sharing as Sharing;

/** Traits */
use BoshDev\Themalizer\Helper\Tests;
use BoshDev\Themalizer\Helper\Sanitizers;

/**
 * Provides general methods and calls used internally in all the classes in the framework.
 */
class Engine {

	// Including all the traits in the framework.
	use Tests;
	use Sanitizers;


}
