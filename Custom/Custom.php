<?php
namespace BoshDev\Custom;

/**
 * Custom Functions Class
 */
class Custom {


	function bod_rnta( $str ) {

		$str = html_entity_decode( $str );

		$western_arabic_nums = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
		$eastern_arabic_nums = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );

		$western_arabic_months = array( 'January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December' );
		$eastern_arabic_months = array(
			'يناير',
			'فبراير',
			'مارس',
			'أبريل',
			'مايو',
			'يونيو',
			'يوليو',
			'أغسطس',
			'سبتمبر',
			'أكتوبر',
			'نوفمبر',
			'ديسمبر',
		);

		// use preg_replace instead of str_replace
		$str = str_replace( $western_arabic_months, $eastern_arabic_months, $str );
		$str = str_replace( $western_arabic_nums, $eastern_arabic_nums, $str );

		// fix replacing numbers in tags
		$str = preg_replace_callback(
			'/\<.*?\>/',
			function( $match ) {
				$match = preg_replace_callback(
					'/[\x{0660}-\x{0669}]/u',
					function( $num ) {
						$western_arabic_nums = array( '0', '1', '2', '3', '4', '5', '6', '7', '8', '9' );
						$eastern_arabic_nums = array( '٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩' );

						$indexStg = array_search( $num[0], $eastern_arabic_nums );

						return $western_arabic_nums[ $indexStg ];
					},
					$match
				);
				return $match[0];
			},
			$str
		);

		return $str;
	}


	


}

