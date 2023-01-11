<?php

/**
 * Initialize Class File
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

namespace Themalizer\Console\Initialize;

use Themalizer\Console\Core;
use Themalizer\Console\Helper\Formatter;
use Themalizer\Console\Helper\Question;
use Themalizer\Console\Initialize\Skeleton;
use Themalizer\Console\Initialize\InitialData;


if (!defined('THEMALIZER_CLI')) {
	exit('You are not allowed to get here, TINKY WINKY!!'); // Exit if accessed directly.
}

class Initialize extends Core
{

	use Question;
	use Formatter;

	// the name of the command
	protected static $defaultName = 'init';

	public $theme_name = 'Themalizer';

	public $theme_author = 'Bisharah Estephan';

	public $theme_author_company = '';

	public $theme_author_uri = 'https://boshdev.com';

	public $description = 'This theme is created for...';

	public $tags = array();

	public $text_domain = 'themalizer-test';

	public $theme_prefix = 'tml';

	public $laravel_mix_confirmation = true;

	public $theme_default_css_preprocessor = '';

	public $default_css_preprocessors = array(
		'SASS',
		'SCSS',
		'LESS',
	);

	public $settings_page = false;

	public $sidebar = false;

	public $nav_walker = false;

	public $skeleton = array();

	public $files_names = array(

		'home.php'           => 'Home Page Theme Template',
		'front-page.php'     => 'Front Page Theme Template',
		'privacy-policy.php' => 'Privacy Policy Theme Template',
		'singular.php'       => 'Singular Theme Template',
		'single.php'         => 'Single Theme Template',
		'page.php'           => 'Page Theme Template',
		'archive.php'        => 'Archive Theme Template',
		'category.php'       => 'Category Theme Template',
		'tag.php'            => 'Tag Theme Template',
		'author.php'         => 'Author Theme Template',
		'date.php'           => 'Date Theme Template',
		'searchpage.php'     => 'Searchpage Theme Template',
		'search.php'         => 'Search Theme Template',
		'404.php'            => '404 Theme Template',
		'header.php'         => 'Header Theme Template',
		'footer.php'         => 'Footer Theme Template',
		'index.php'          => 'Main Index Theme File',
		'functions.php'      => 'Functions Theme File',
	);

	protected function init()
	{
	}

	protected function in_configure()
	{
		$this
			->setDescription('Configure the WP theme')
			->setHelp('This command initiate the basic skeleton of a WordPress theme');
	}

	protected function in_interact()
	{
		$this->get_theme_basic_info();
		$this->skeleton = (new Skeleton($this))->get_skeleton();
		if (empty($this->skeleton)) {
			$this->write_out('You have created an empty skeleton, the program will exit now.', 'error');
			exit();
		}
	}

	protected function in_excute()
	{
		$this->prepare_skeleton();
		$this->create_skeleton();
		$this->setup_laravel_mix();
		$this->setup_nav_walker();
		(new InitialData($this));
	}

	private function get_theme_basic_info()
	{
		$this->theme_name                     = $this->ask_question('What is your theme name?');
		$this->theme_prefix                   = $this->ask_question('What is your theme prefix, this will be used in your them to prevent any conflicts with other plugins and functionalities?');
		$this->theme_author                   = $this->ask_question('What is the name of the theme author?');
		$this->theme_author_company           = $this->ask_question('What is the name of company/team that will be working on the theme? (Default = Null)', null);
		$this->theme_author_uri               = $this->ask_question('What is the author URI?');
		$this->theme_description              = $this->ask_question('What is the description of the theme?');
		$this->theme_text_domain              = $this->ask_question('What is the text domain of the theme?');
		$this->theme_tags                     = $this->ask_question(
			'Please write the tags of the theme seperated by comma (e.g. tag1, tag2, tag3):',
			null,
			true
		);
		$this->theme_laravel_mix_confirmation = $this->confirm('Whould you like to use Laravel-mix for preprocessing your JS and CSS files?');

		if ($this->theme_laravel_mix_confirmation) {
			$this->theme_default_css_preprocessor = $this->multiple_choice('Please choose which CSS preprocessor you want to use', $this->default_css_preprocessors);
		} else {
			$this->theme_default_css_preprocessor = null;
		}
		$this->settings_page = $this->confirm('Whould you like to have settings page for your theme?');
		$this->sidebar       = $this->confirm('Whould you like to have sidebar in your theme?');
		$this->nav_walker       = $this->confirm('Whould you like to have NavWalker for your theme?');
	}

	private function prepare_skeleton()
	{
		$skeleton = array();
		foreach ($this->skeleton as $value) {
			$skeleton[] = array(
				'type'  => substr($value[1], -1) === '/' ? 'dir' : 'file',
				'value' => substr($value[1], 2),
			);
		}
		$this->skeleton = $skeleton;
	}

	private function create_skeleton()
	{
		// To add the custom theme functions and other custom files
		if (!is_dir('../includes'))
			mkdir('../includes', 0755, true);

		foreach ($this->skeleton as $element) {
			if (!is_dir('../' . $element['value']) && $element['type'] === 'dir') {
				mkdir('../' . $element['value'], 0755, true);
			} elseif (strpos($element['value'], '/') && $element['type'] === 'file') {
				$processed_element = explode('/', $element['value']);
				$file              = end($processed_element);
				array_pop($processed_element);
				$dir = implode('/', $processed_element);
				if (!is_dir('../' . $dir)) {
					mkdir('../' . $dir, 0755, true);
				}
				$this->create_file($element['value']);
			} elseif ($element['type'] === 'file') {
				$this->create_file($element['value']);
			}
		}
		$this->create_file('index.php');
		$this->create_file('functions.php');
		$this->create_file('style.css');

		// Default Themalizer files
		$this->create_file('includes/custom_theme_functions.php');
	}

	private function create_file($file_name)
	{
		$file = '../' . $file_name;
		if (substr($file_name, -4) === '.php') {
			file_put_contents($file, $this->starting_of_file($file_name));
		} else {
			file_put_contents($file, '');
		}
	}

	private function starting_of_file($file_name)
	{
		$year      = date('Y');
		$file_name = array_key_exists($file_name, $this->files_names) ? $this->files_names[$file_name] : $file_name;
		return <<<EOD
		<?php
		/**
		 * $file_name
		 *
		 * @package $this->theme_name
		 * @copyright $year - $this->theme_author_company
		 * @author $this->theme_author
		 * @link $this->theme_author_uri
		 * @license https://opensource.org/licenses/GPL-2.0 GPL-2.0 License
		 */
		
		if ( ! defined( 'ABSPATH' ) ) {
			exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
		}
		
		EOD;
	}

	private function setup_laravel_mix()
	{
		if (!$this->laravel_mix_confirmation) {
			return;
		}

		$preprocessor = strtolower($this->theme_default_css_preprocessor);

		if (!is_dir('../assets/' . $preprocessor)) {
			mkdir('../assets/' . $preprocessor, 0755, true);
			file_put_contents('../assets/' . $preprocessor . '/style.' . $preprocessor, '');
		}

		// Create custom JS file to add the JS theme's functions.
		if (!is_dir('../assets/js'))
			mkdir('../assets/js', 0755, true);

		file_put_contents(
			'../assets/js/custom.js',
			<<<EOD
			$(document).ready(function () {
				// your code goes here..
			});
			EOD
		);

		$webpack = <<<EOD
		/*
		|--------------------------------------------------------------------------
		| Mix Asset Management
		|--------------------------------------------------------------------------
		|
		| Mix provides a clean, fluent API for defining some Webpack build steps
		| for your Laravel application. By default, we are compiling the Sass
		| file for the application as well as bundling up all the JS files.
		|
		*/
		
		const mix = require('laravel-mix');
		
		mix
			.scripts([
				// Your scripts files path.
				'assets/js/custom.js',
			], 'assets/main.js')
			.$preprocessor('assets/$preprocessor/style.$preprocessor', '')
			.options({
				processCssUrls: false
			});
		EOD;

		file_put_contents('../webpack.mix.js', $webpack);

		file_put_contents(
			'../package.json',
			<<<EOD
			{
				"private": true,
				"scripts": {
					"dev": "npm run development",
					"development": "cross-env NODE_ENV=development node_modules/webpack/bin/webpack.js --progress --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js",
					"watch": "npm run development -- --watch",
					"watch-poll": "npm run watch -- --watch-poll",
					"hot": "cross-env NODE_ENV=development node_modules/webpack-dev-server/bin/webpack-dev-server.js --inline --hot --disable-host-check --config=node_modules/laravel-mix/setup/webpack.config.js",
					"prod": "npm run production",
					"production": "cross-env NODE_ENV=production node_modules/webpack/bin/webpack.js --no-progress --hide-modules --config=node_modules/laravel-mix/setup/webpack.config.js"
				},
				"devDependencies": {
					"axios": "^0.19",
					"cross-env": "^7.0",
					"laravel-mix": "^5.0.1",
					"lodash": "^4.17.19",
					"resolve-url-loader": "^3.1.0",
					"sass": "^1.15.2",
					"sass-loader": "^8.0.0",
					"vue-template-compiler": "^2.6.12"
				},
				"dependencies": {
					"@fortawesome/fontawesome-free": "^5.14.0",
					"bootstrap": "^4.5.2",
					"elegant-icons": "0.0.1",
					"jquery": "^3.5.1",
					"modernizr": "^3.11.3",
					"popper.js": "^1.16.1"
				}
			}
			EOD
		);
	}

	private function setup_nav_walker()
	{
		if (!$this->nav_walker) {
			return;
		}

		file_put_contents('../includes/nav_walker.php', $this->starting_of_file('nav_walker.php'));
	}
}
