<?php
/**
 * Initial Data Class File
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
namespace Themalizer\Console\Initialize;

use Themalizer\Console\CommandPart;

if ( ! defined( 'THEMALIZER_CLI' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

class InitialData extends CommandPart {

	private $skeleton_for_header_footer = array();

	protected function init() {

		$this->skeleton_for_header_footer = array_map(
			function( $item ) {
				if ( 'file' === $item['type'] ) {
					return $item['value'];
				}
			},
			$this->command->skeleton
		);

		$this->skeleton_for_header_footer = array_filter(
			$this->skeleton_for_header_footer,
			function( $item ) {
				$processed_element = explode( '/', $item );
				$file              = end( $processed_element );
				return ! empty( $item ) && '.php' === substr( $item, -4 ) && 'header.php' !== $file && 'footer.php' !== $file && 'search.php' !== $file;
			}
		);

		$this->style_data();
		$this->functions_data();
		$this->header_data();
		$this->footer_data();
		foreach ( $this->skeleton_for_header_footer as $filename ) {
			$this->header_footer_data( $filename );
		}

	}

	private function style_data() {
		$theme_name           = $this->command->theme_name;
		$theme_author         = $this->command->theme_author;
		$theme_author_company = empty( $this->command->theme_author_company ) ? '' : ' - ' . $this->command->theme_author_company;
		$theme_author_uri     = $this->command->theme_author_uri;
		$description          = $this->command->description;
		$tags                 = empty( $this->command->tags ) ? '' : implode( ',', $this->command->tags );
		$text_domain          = $this->command->text_domain;

		$style_data = <<<EOD
		/*
		Theme Name: $theme_name
		Theme URI: https://wordpress.org/themes/twentynineteen/
		Author: $theme_author $theme_author_company
		Author URI: $theme_author_uri
		Description: $description
		Requires PHP: 7.3
		Version: 1.0
		License: GNU General Public License v2 or later
		License URI: http://www.gnu.org/licenses/gpl-2.0.html
		Text Domain: $text_domain
		Tags: $tags
		
		This theme, like WordPress, is licensed under the GPL.
		Use it to make something cool, have fun, and share what you've learned with others.
		*/
		EOD;

		file_put_contents( '../style.css', $style_data );
	}

	private function functions_data() {
		$theme_name     = $this->command->theme_name;
		$theme_prefix   = $this->command->theme_prefix;
		$settings       = ! $this->command->settings_page ? '' : <<<EOD
		Themalizer::setting(
			array(
				'page_title' => 'Your Settings Page Title',
				'menu_title' => 'Theme Settings',
				'sections'   => array(
					'section_slug' => array( // The ID (slug) which will be used to identify the section.
						'title'       => 'Section Title', // The section title which will appear in the settings page.
						'description' => 'Section description', // Description for the section.
						'fields'      => array( // Here goes the section fields and options.
							'option_slug' => array( // field slug.
								'title'       => 'Field title',
								'description' => 'Field description',
							),
						),
					),
				),
			)
		);
		EOD;

		$sidebar        = ! $this->command->sidebar ? '' : <<<EOD
		Themalizer::sidebar(
			array(
				'name'          => 'Sidebar Name',
				'description'   => 'Sidebar description',
				'before_widget' => '<div class="example-class">',
				'before_title'  => '<h5 class="example-class">',
				'after_title'   => '</h5>',
		
			)
		);
		EOD;

		$customizer     = <<<EOD
		Themalizer::customizer(
			'theme_general', // customizer ID (slug) which will be used later to retrive the options.
			array(
				'title'       => 'Section Title',
				'description' => 'Section description..',
				'settings'    => array( // Here goes the fields of the section.
					'field_slug'          => array( // field slug.
						'selector' => '#example_field_selector', // field selector for access the field outputs.
						'control'  => array( // control data
							'label' => 'The label of the control field.',
							'type'  => 'text', // The field type as per Themalizer documentations
						),
					),
				),
			)
		);
		EOD;

		$functions_data = <<<EOD
		require_once 'Themalizer/autoload.php';
		
		Themalizer::\$development = true; // Please remove this in staging/production invironment. 
		
		Themalizer::init(
			array(
				'prefix'           => '$theme_prefix',
				'customizer_panel' => array(
					'title'       => '$theme_name',
					'description' => 'Theme customization panel.',
				),
			)
		);
		
		$settings
		
		$sidebar
		
		$customizer
		EOD;

		file_put_contents( '../functions.php', $functions_data, FILE_APPEND );
	}

	private function header_data() {

		$header_data = <<<EOD
		Themalizer::start_header();
		Themalizer::wp_head();
		?>
		
		<style>
		/* Dynamic Style */
		</style>
		
		<?php
		Themalizer::close_header();
		?>
		
		
		<?php
		wp_nav_menu(
			array(
				'theme_location'  => Themalizer::get_menus_locations()[0],
				'menu_id'         => 'menu-id',
				'menu_class'      => 'menu-class',
				'container'       => 'nav',
				'container_id'    => 'container-id',
				'container_class' => 'container-class',
				// 'walker'          => Themalizer::nav_walker(),
			)
		);
		?>
		EOD;

		if ( file_exists( '../header.php' ) ) {
			file_put_contents( '../header.php', $header_data, FILE_APPEND );
		}

	}

	private function footer_data() {

		$footer_data = <<<EOD
		?>
		
		
		
		
		
		<?php
		Themalizer::footer();
		EOD;

		if ( file_exists( '../footer.php' ) ) {
			file_put_contents( '../footer.php', $footer_data, FILE_APPEND );
		}

	}

	private function header_footer_data( $file_name ) {

		$header_footer_data = <<<EOD
		get_header();
		?>
		
		
		
		
		<?php
		get_footer();
		EOD;
		
		if ( file_exists( '../' . $file_name ) ) {
			file_put_contents( '../' . $file_name, $header_footer_data, FILE_APPEND );
		}
	}

}
