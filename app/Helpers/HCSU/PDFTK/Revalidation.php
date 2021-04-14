<?php

namespace App\Helpers\HCSU\PDFTK;

class Revalidation{
	private $filename;

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document){
		$revalidationData = \App\Models\Revalidation::where('CASE_NO', $case_no)->first();

		dd($revalidationData);
	}
}