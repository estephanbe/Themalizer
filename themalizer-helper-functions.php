<?php
/**
 * Helper functions
 *
 * @package Themalizer
 * @copyright 2019-2020 BoshDev - Bisharah Estephan
 * @author Bisharah Estephan (BoshDev Founder)
 * @link https://boshdev.com/ BoshDev
 * @license http://www.opensource.org/licenses/bsd-license.php BSD License
 */

// phpcs:disable
if ( ! defined( 'ABSPATH' ) ) {
	exit( 'You are not allowed to get here, TINKY WINKY!!' ); // Exit if accessed directly.
}

if ( ! function_exists( 'set_post_views' ) ) {

	/**
	 * Set post views meta for each post and it sould be used in single.php in order to enable this functionality.
	 *
	 * The query params to collect the posts is:
	 * 'meta_key' => 'post_views_count',
	 * 'orderby' => 'meta_value_num',
	 *
	 * @param integer $post_id the post id.
	 * @return void
	 */
	function set_post_views( $post_id ) {
		$count_key = 'post_views_count';
		$count     = get_post_meta( $post_id, $count_key, true );
		if ( $count == '' ) {
			$count = 0;
			delete_post_meta( $post_id, $count_key );
			add_post_meta( $post_id, $count_key, '0' );
		} else {
			$count++;
			update_post_meta( $post_id, $count_key, $count );
		}
	}
}

if ( ! function_exists( 'the_breadcrumb' ) ) {
	function the_breadcrumb() {
		// $delimiter   = '&raquo;'; // delimiter between crumbs
		$delimiter   = ''; // delimiter between crumbs
		$home        = 'الرئيسية'; // text for the 'Home' link
		$show_current = 1; // 1 - show current post/page title in breadcrumbs, 0 - don't show
		$before      = '<li>'; // tag before the current crumb
		$after       = '</li>'; // tag after the current crumb

		global $post;
		$home_link = get_bloginfo( 'url' );
		echo '<li><a href="' . $home_link . '">' . $home . '</a></li> ' . $delimiter . ' ';
		if ( is_category() ) {
			$this_cat = get_category( get_query_var( 'cat' ), false );
			if ( $this_cat->parent != 0 ) {
				echo get_category_parents( $this_cat->parent, true, ' ' . $delimiter . ' ' );
			}
			echo $before . 'الأرشيف بحسب المواضيع "' . single_cat_title( '', false ) . '"' . $after;
		} elseif ( is_search() ) {
			echo $before . 'نتائج البحث لِ "' . get_search_query() . '"' . $after;
		} elseif ( is_day() ) {
			echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li> ' . $delimiter . ' ';
			echo '<li><a href="' . get_month_link( get_the_time( 'Y' ), get_the_time( 'm' ) ) . '">' . get_the_time( 'F' ) . '</a></li> ' . $delimiter . ' ';
			echo $before . get_the_time( 'd' ) . $after;
		} elseif ( is_month() ) {
			echo '<li><a href="' . get_year_link( get_the_time( 'Y' ) ) . '">' . get_the_time( 'Y' ) . '</a></li> ' . $delimiter . ' ';
			echo $before . get_the_time( 'F' ) . $after;
		} elseif ( is_year() ) {
			echo $before . get_the_time( 'Y' ) . $after;
		} elseif ( is_single() && ! is_attachment() ) {
			if ( get_post_type() != 'post' ) {
				$post_type = get_post_type_object( get_post_type() );
				$slug      = $post_type->rewrite;
				echo '<li><a href="' . $home_link . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a></li>';
				if ( $show_current == 1 ) {
					echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
				}
			} else {
				$cat  = get_the_category();
				$cat  = $cat[0];
				$cats = get_category_parents( $cat, true, ' ' . $delimiter . ' ' );
				if ( $show_current == 0 ) {
					$cats = preg_replace( "#^(.+)\s$delimiter\s$#", '$1', $cats );
				}
				echo '<li>';
				echo $cat->name !== 'Uncategorized' ? $cats : '';
				echo '</li>';
				if ( $show_current == 1 ) {
					echo $before . get_the_title() . $after;
				}
			}
		} elseif ( ! is_single() && ! is_page() && get_post_type() != 'post' && ! is_404() ) {
			$post_type = get_post_type_object( get_post_type() );
			echo $before . $post_type->labels->singular_name . $after;
		} elseif ( is_attachment() ) {
			$parent = get_post( $post->post_parent );
			$cat    = get_the_category( $parent->ID );
			$cat    = $cat[0];
			echo get_category_parents( $cat, true, ' ' . $delimiter . ' ' );
			echo '<li><a href="' . get_permalink( $parent ) . '">' . $parent->post_title . '</a></li>';
			if ( $show_current == 1 ) {
				echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
			}
		} elseif ( is_page() && ! $post->post_parent ) {
			if ( $show_current == 1 ) {
				echo $before . get_the_title() . $after;
			}
		} elseif ( is_page() && $post->post_parent ) {
			$parent_id   = $post->post_parent;
			$breadcrumbs = array();
			while ( $parent_id ) {
				$page          = get_page( $parent_id );
				$breadcrumbs[] = '<li><a href="' . get_permalink( $page->ID ) . '">' . get_the_title( $page->ID ) . '</a></li>';
				$parent_id     = $page->post_parent;
			}
			$breadcrumbs = array_reverse( $breadcrumbs );
			for ( $i = 0; $i < count( $breadcrumbs ); $i++ ) {
				echo $breadcrumbs[ $i ];
				if ( $i != count( $breadcrumbs ) - 1 ) {
					echo ' ' . $delimiter . ' ';
				}
			}
			if ( $show_current == 1 ) {
				echo ' ' . $delimiter . ' ' . $before . get_the_title() . $after;
			}
		} elseif ( is_tag() ) {
			echo $before . 'المقالات بحسب الكلمة المفتاحية  "' . single_tag_title( '', false ) . '"' . $after;
		} elseif ( is_author() ) {
			global $author;
			$userdata = get_userdata( $author );
			echo $before . 'المقالات التي نُشرت بواسطة ' . $userdata->display_name . $after;
		} elseif ( is_404() ) {
			echo $before . 'Error 404' . $after;
		}
		if ( get_query_var( 'paged' ) ) {
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
				echo ' (';
			}
			echo __( 'Page' ) . ' ' . get_query_var( 'paged' );
			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() ) {
				echo ')';
			}
		}
	} // end the_breadcrumb()
}
