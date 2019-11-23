<?php

if (! function_exists('get_highest_excel_column')) {
	function get_highest_excel_column($number){
		$alphabets = range('A', 'Z');
		if($number <= 27 && $number > 0){
			return $alphabets[$number - 1];
		}
	}
}

if (! function_exists('identify_hcsu_client') ) {
	function identify_hcsu_client($host_country_id){
		$firstIDChar = substr($host_country_id, 0, 1);

		if ( $firstIDChar == 1 ) {
			return 'staff';
		} else if ( $firstIDChar == 2 ) {
			return 'dependent';
		} else if ( $firstIDChar == 3 ) {
			return 'agency';
		}
	}
}