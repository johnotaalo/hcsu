<?php

namespace App\Helpers\HCSU\PDFTK;

class InternshipPass{
	private $filename;

	public function __construct(){

	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no){
		$data = \App\Helpers\HCSU\Data\InternshipPassData::get($case_no);

		$date = date('F d, Y');

		$title = "The United Nations Office at Nairobi (UNON), Host Country Services Unit, extends its compliments to the Director of Immigration Services in Nairobi and would like to confirm that the above-named individual has been accepted to participate in the United Nations Internship Programme.\n";
		$body = "\nHis details are as follows:\n";
		$body .= "Name:\t\t\t\t\t\t\t\t\t\t\t\t{$data->client->name}\n";
		$body .= "Nationality:\t\t\t\t\t{$data->client->nationality}\n";
		$body .= "Passport No:\t\t\t\t\t{$data->client->passport->passport_no}\n";
		$body .= "Course:\t\t\t\t\t\t\t\t\t\t{$data->client->contract->ACRONYM}/Internship Programme\n";
		$body .= "Effective Date:\t\t{$data->client->contract->START_DATE}\n";
		$body .= "End Date:\t\t\t\t\t\t\t\t{$data->client->contract->END_DATE}\n";

		$footer = "\nAny assistance that could be accorded to {$data->client->name} to enable him to obtain the Kenya Internship Pass would be highly appreciated.\n\n\n";
		$signoff = "                               Yours sincerely,";

		$content = $title . $body . $footer . $signoff;

		$tabData = [
			'ref'				=>	$data->ref,
			'today'				=>	$date,
			'top_reference'		=>	"RE: APPLICATION FOR KENYA INTERNSHIP PASS - {$data->client->name}",
			'content'			=>	$content
		];

		return $tabData;
	}
}