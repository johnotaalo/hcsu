<?php

namespace App\Helpers\HCSU\PDFTK\Templates;

class VISAExtensionForm{

	protected $other_names;
	protected $nationality;
	protected $date_of_issue;
	protected $place_of_issue;
	protected $address_in_kenya;
	protected $passport_no;
	protected $surname;
	protected $port_of_entry;
	protected $extending_reason;
	protected $date_of_entry;
	protected $extending_period;

	function __construct($data){
		foreach ($data as $k => $d) {
			$this->$k = $d;
		}
	}

	function data(){
		return [
			"other_names"		=>	$this->other_names,
			"nationality"		=>	$this->nationality,
			"date_of_issue"		=>	$this->date_of_issue,
			"place_of_issue"	=>	$this->place_of_issue,
			"address_in_kenya"	=>	$this->address_in_kenya,
			"passport_no"		=>	$this->passport_no,
			"surname"			=>	$this->surname,
			"port_of_entry"		=>	$this->port_of_entry,
			"extending_reason"	=>	$this->extending_reason,
			"date_of_entry"		=>	$this->date_of_entry,
			"extending_period"	=>	$this->extending_period,
		];
	}

	public function __get($property){
		if (property_exists($this, $property)) {
			return $this->property;
		}
	}

	public function __set($property, $value){
		if (property_exists($this, $property)) {
			$this->$property = $value;
		}

		return $this;
	}
}