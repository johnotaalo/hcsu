<?php

if (! function_exists('get_highest_excel_column')) {
	function get_highest_excel_column($number){
		$alphabets = range('A', 'Z');
		if($number <= 27 && $number > 0){
			return $alphabets[$number - 1];
		}
	}
}