<?php
/**
 * Skeleton Class File
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */
namespace Themalizer\Console\Initialize;

use Themalizer\Console\CommandPart;
use Themalizer\Console\Helper\Formatter;
use Themalizer\Console\Helper\Question;
use Themalizer\Console\Helper\Table;

if ( ! defined( 'THEMALIZER_CLI' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

class Skeleton extends CommandPart {

	use Table;
	use Question;
	use Formatter;

	private $headers = array( 'ID', 'Dir/File', 'Description' );

	private $skeleton = array(
		array( 0, './assets/', 'Directory for the theme static files.' ),
		array( 1, './assets/css/', 'Directory contains the theme CSS files.' ),
		array( 2, './assets/js/', 'Directory contains the theme JS files.' ),
		array( 3, './assets/images/', 'Directory contains the theme static images.' ),
		array( 4, './assets/fonts/', 'Directory contains the theme fonts.' ),
		array( 5, './assets/main.js', 'The main JS file which will be served after all the scripts compressed into one script.' ),
		array( 6, './page-templates/', 'Directory for custom WP templates.' ),
		array( 7, './template-parts/', 'Directory for the WP default template parts.' ),
		array( 8, './home.php', 'This template is used to render the blog posts index.' ),
		array( 9, './_front-page.php', 'This template is used to render your site’s _front page.' ),
		array( 10, './privacy-policy.php', 'This template is used to render your site’s Privacy Policy page.' ),
		array( 11, './singular.php', 'This template is used to backup single & page templates if one of them is not existed.' ),
		array( 12, './single.php', 'This template is used to render a single post.' ), // custom post type
		array( 13, './page.php', 'This template is used to render a static page (page post-type).' ),
		array( 14, './archive.php', 'This template is used to render  archive index pages.' ),
		array( 15, './category.php', 'This template is used to render category archive index pages.' ),
		array( 16, './tag.php', 'This template is used to render tag archive index pages.' ),
		array( 17, './author.php', 'This template is used to render author archive index pages.' ),
		array( 18, './date.php', 'This template is used to render archive index pages by date.' ),
		array( 19, './searchpage.php', 'This template is used to render the search page.' ),
		array( 20, './search.php', 'This template is used to render the search results in the Search Template.' ),
		array( 21, './404.php', 'This template is used to render the 404 template.' ),
		array( 22, './header.php', 'This template is used to render theme\'s header.' ),
		array( 23, './footer.php', 'This template is used to render theme\'s footer.' ),
	);

	private $ids_to_be_removed_from_skeleton = array();

	private $additional_skeleton_values = array();

	private $skeleton_loop = true;

	private $remove_loop = true;

	private $add_loop = true;

	protected function init() {

		$this->info_msg( 'Getting skeleton info..', true );
		$this->get_skeleton_info();

	}

	public function get_skeleton(){
		return $this->skeleton;
	}	

	private function get_skeleton_info() {

		$this->write_out( 'This is the default skeleton:', 'comment' );
		$this->generate_table(
			$this->headers,
			$this->skeleton
		);

		$this->confirm_default_skeleton();

		while ( $this->skeleton_loop ) {
			$this->get_final_skeleton();
			$this->confirm_amendments();
		}
	}

	private function confirm_default_skeleton() {
		if ( $this->confirm( 'Index.php, functions.php and style.css will be added by default. Do you want to proceed with the default skeleton?' ) ) {
			$this->skeleton_loop = false;
		}
	}

	private function confirm_amendments() {
		$this->generate_table(
			$this->headers,
			$this->skeleton
		);
		if ( $this->confirm( 'This is your final skeleton, do you want to do any other amendments on it?' ) ) {
			$this->skeleton_loop = true;
		} else {
			$this->skeleton_loop = false;
		}
	}

	private function get_final_skeleton() {
		do {
		$this->remove_from_skeleton_info();
		} while ( $this->remove_loop );

		do {
			$this->add_to_skeleton_info();
		} while ( $this->add_loop );
	}

	private function remove_from_skeleton_info() {
		$ids_to_be_removed                     = '';
		$this->ids_to_be_removed_from_skeleton = array();
		if ( $this->confirm( 'Do you want to remove from the skeleton?' ) ) {
			do {
				$ids_to_be_removed = $this->ask_question( 'Please list the skeleton parts IDs from the above table seperated by comma (e.g. 1,2,3):', null );
			} while ( $this->check_ids_list( $ids_to_be_removed ) );
			if ( ! empty( $ids_to_be_removed ) ) {
				$removed_rows = array_filter(
					$this->skeleton,
					function( $element ) {
						if ( in_array( $element[0], $this->ids_to_be_removed_from_skeleton ) ) {
							return $element;
						}
					}
				);
				$this->write_out( 'These are the elements which have been removed from the skeleton:', 'comment' );
				$this->generate_table(
					$this->headers,
					$removed_rows
				);
				if ( $this->confirm( 'Are you sure of removing these elements?' ) ) {
					$this->remove_loop = false;
					$this->do_remove();
				} else {
					$this->remove_loop = true;
				}
			}
		} else {
			$this->remove_loop = false;
			return;
		}
	}

	private function do_remove() {
		foreach ( $this->skeleton as $key => $value ) {
			if ( in_array( $value[0], $this->ids_to_be_removed_from_skeleton ) ) {
				unset( $this->skeleton[ $key ] );
			}
		}
	}

	private function add_to_skeleton_info() {
		$this->additional_skeleton_values = array();
		$additional_data                  = array();

		if ( $this->confirm( 'Do you want to add to the skeleton?' ) ) {
			$additional_data = $this->ask_question(
				'Please enter the additional elements that you want to add seperated by comma. Directories must be appended with "/" (e.g. exampleFile1.php, exampleDir1/, exampleDir2/exampleDir3/exampleFile2.php, assets/js/exampleFile3.js): ',
				null,
				true
			);
			if ( ! empty( $additional_data ) ) {
				$starting_id                      = end( $this->skeleton )[0];
				$this->additional_skeleton_values = array_map(
					function( $item ) use ( &$starting_id ) {
						return array( ++$starting_id, './' . $item, 'Additional Element.' );
					},
					$additional_data
				);
				$this->write_out( 'These are the elements which will be added to the skeleton:', 'comment' );
				$this->generate_table(
					$this->headers,
					$this->additional_skeleton_values
				);
				if ( $this->confirm( 'Are you sure of adding these elements?' ) ) {
					$this->add_loop = false;
					$this->do_add();
				} else {
					$this->add_loop = true;
				}
			}
		} else {
			$this->add_loop = false;
			return;
		}

		// die;
	}

	private function do_add() {
		if ( ! empty( $this->additional_skeleton_values ) ) {
			foreach ($this->additional_skeleton_values as $value) {
				array_push($this->skeleton, $value);
			}
		}
	}

	private function check_ids_list( $ids ) {
		if ( $ids === null ) {
			return false;
		}
		$ids = explode( ',', $ids );
		foreach ( $ids as $id ) {
			if ( ! is_numeric( $id ) ) {
				$this->ids_to_be_removed_from_skeleton = array();
				$this->throw_error( 'You have entered non numeric input in one of your entries, please try again!' );
				return true;
			}
			$id = intval( trim( $id ) );

			if ( sizeof( $this->skeleton ) < $id ) {
				$this->ids_to_be_removed_from_skeleton = array();
				$this->throw_error( 'You have entered non existed ID in one of your entries, please try again!' );
				return true;
			}

			array_push( $this->ids_to_be_removed_from_skeleton, $id );
		}
		$this->ids_to_be_removed_from_skeleton = array_unique( $this->ids_to_be_removed_from_skeleton );
		return false;
	}



}
