<?php

namespace App\Helpers\HCSU\PDFTK;

class NoteVerbal {
	protected $process;
	public $header, $footer, $body;
	protected $data, $initials;

	public function __construct($process, $data, $initials){
		$this->process = $process;
		$this->data = $data;
		$this->initials = $initials;
	}

	public function getHeader(){
		$connector = "";
		$end_header = "";
		$body = "";

		switch($this->process){
			case 'vat':
				$end_header=" a Value Added Tax (VAT) Exemption claim.";
				$connector = " submit an application for";
				break;
		}

		$this->header = "Our Ref: {$this->data->ref}/$this->initials\n\nThe United Nations Office at Nairobi (UNON) presents its compliments to the Ministry of Foreign Affairs of the Republic of Kenya and has the honour to {$connector} {$end_header}\n\n";

		return $this;
	}

	public function getFooter(){
		$this->footer = "The United Nations Office at Nairobi (UNON) avails itself of this opportunity to renew to the Ministry of Foreign Affairs and International Trade of the Republic of Kenya the assurances of its highest consideration.\r\r\r\r\r\r\r\r                            {$this->data->date}";

		return $this;
	}

	public function getBody(){
		$body = "";
		switch($this->process){
			case "vat":
				$body = "Details are as follows:\r";
				$body .= str_pad("Serial No:", 15) . "{$this->data->case_no}\r";
				$body .= str_pad("Name:", 15) . "{$this->data->client->name}\r";
				if($this->data->client->type == "staff" || $this->data->client->type == "dependent"){
					$body .= str_pad("Organization:", 15) . "{$this->data->client->organization}\r";
				}
				$body .= str_pad("Invoice No:" , 15) . "{$this->data->vat->pfNo}\r";
				$body .= str_pad("VAT Amount:" , 15) . "KSH. {$this->data->vat->vatAmount}\r";
			break;
		}

		$this->body = $body;
	}

	public function getContent(){
		$this->getHeader();
		$this->getFooter();
		$this->getBody();

		return  $this->header . $this->body . "\n\n" . $this->footer;
	}
}
