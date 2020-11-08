<?php
namespace Themalizer\Luxury;

use Helper\tests;

// this class is for loading more posts in AJAX request

class LoadMorePosts {

	use tests;

	function __construct( $init, $numOfPosts ) {
		$this->isInit_test( $init, 'Make sure the "init" argument is instance of INIT class' );
		$this->init       = $init; // assign init class to get the directories
		$this->numOfPosts = $numOfPosts; // how many post per page
		$this->addActions(); // link the functions with the proper actions, basically, fire the class.
	}

	public function addActions() {
		add_action( 'wp_enqueue_scripts', array( $this, 'load_more_scripts' ) ); // localize script, add the required variables.
		add_action( 'wp_ajax_nopriv_more_post_ajax', array( $this, 'more_post_ajax' ) );
		add_action( 'wp_ajax_more_post_ajax', array( $this, 'more_post_ajax' ) );
	}

	public function load_more_scripts() {
		wp_localize_script(
			$this->init->scriptName,
			'ajax_posts',
			array(
				'ajaxurl' => admin_url( 'admin-ajax.php' ),
				'noposts' => __( 'No older posts found', $this->init->textDomain ),
			)
		);
	}

	public function more_post_ajax() {
		if ( isset( $_POST['pageNumber'] ) ) {
			$page = $_POST['pageNumber']; // check if the page number is assigned.
			unset( $_POST['pageNumber'] );
		} else {
			$page = 0;
		}

		if ( isset( $_POST['action'] ) ) {
			unset( $_POST['action'] );
		}

		if ( sizeof( $_POST ) > 0 ) {

			if ( isset( $_POST['s'] ) ) {
				$args                   = $_POST;
				$args['paged']          = $page;
				$args['posts_per_page'] = isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : $this->numOfPosts;
			} else {
				$args                     = $_POST;
				$args['suppress_filters'] = true;
				$args['post_type']        = isset( $args['post_type'] ) ? $args['post_type'] : 'post';
				$args['paged']            = $page;
				$args['posts_per_page']   = isset( $args['posts_per_page'] ) ? $args['posts_per_page'] : $this->numOfPosts;
			}
		} else {

			  $args = array(
				  'suppress_filters' => true,
				  'post_type'        => 'post',
				  'post_status'      => 'publish',
				  'posts_per_page'   => $this->numOfPosts,
				  'paged'            => $page,
			  );

		}

		header( 'Content-Type: text/html' ); // to be checked later

		$loop = new \WP_Query( $args );

		$posts = array(); // to be used to add the posts to it

		if ( $loop->have_posts() ) :
			while ( $loop->have_posts() ) :
				  $loop->the_post();

				  $hasPostThumbnail = has_post_thumbnail(); // check if has post thumbnail
				  $hasExcerpt       = has_excerpt(); // check if has excerpt

				if ( has_category( 'uncategorized' ) ) { // check if has cats
					$cats = array();
				} else {
					$cats = get_the_category();
				}

				$permaLink = get_the_permalink();
				$title     = get_the_title();

				// get the thumbnail url, if it doesn't have thumbnail, get random image from the random image directory
				$imageUrl = $hasPostThumbnail ? get_the_post_thumbnail_url() : $this->init->assetsDirUri . 'images/random/' . rand( 1, 12 ) . '.jpg';
				// if for any reason the getPostThumbnailUrl returned false, get random image again.
				$imageUrl = $imageUrl == false ? $this->init->assetsDirUri . 'images/random/' . rand( 1, 12 ) . '.jpg' : $imageUrl;

				// if it doesn't have excerpt, generate placeholder
				$excerpt = $hasExcerpt ? get_the_excerpt() : 'يرجى الدخول على محتويات المقالة لمعرفة عن ماذا تتحدث';

				$dateDay   = get_the_date( 'd' );
				$dateMonth = get_the_date( 'm' );
				$dateYear  = get_the_date( 'Y' );

				// create the post array
				$post = array(
					'hasPostThumbnail' => $hasPostThumbnail,
					'hasExcerpt'       => $hasExcerpt,
					'cats'             => $cats,
					'permaLink'        => $permaLink,
					'title'            => $title,
					'imageUrl'         => $imageUrl,
					'excerpt'          => $excerpt,
					'dateDay'          => $dateDay,
					'dateMonth'        => $dateMonth,
					'dateYear'         => $dateYear,
				);

				// add term link for each cat if there was any
				if ( ! empty( $post['cats'] ) ) {
					foreach ( $post['cats'] as $index => $cat ) {
						$post['cats'][ $index ]->term_permalink = get_term_link( $cat->term_id );
					}
				}

				// push the post into the main posts array which will be sent
				array_push( $posts, $post );

			endwhile;
	  endif;

		$posts = json_encode( $posts );
		die( $posts );
	}
}


