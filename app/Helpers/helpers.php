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

if (! function_exists('format_other_names') ) {
	function format_other_names($other_names){
		$formatted_name = "";
		if(strpos($other_names, "-") !== false){
			$frags = explode("-", $other_names);

			foreach ($frags as $key => $frag) {
				$frags[$key] = ucwords(strtolower($frag));
			}

			$formatted_name = implode("-", $frags);
		}
		elseif (strpos($other_names, ".") !== false) {
			$frags = explode(".", $other_names);

			foreach ($frags as $key => $frag) {
				$frags[$key] = ucwords(strtolower($frag));
			}

			$formatted_name = implode(".", $frags);
		}
		else{
			$formatted_name = ucwords(strtolower($other_names));
		}

		return $formatted_name;
	}
}