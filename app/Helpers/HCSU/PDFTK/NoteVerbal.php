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
			case 'blanket':
				$end_header = " UNON's annual Value Added Tax (VAT) and Excise Duty exemption application for {$this->data->vatObj->supplier->SERVICE_TYPE} services provided by {$this->data->vatObj->supplier->NAME} for the year {$this->data->year}.";
				$connector = " refer to";
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
		$padding = 15;
		switch($this->process){
			case "vat":
				$body = "Details are as follows:\r";
				$body .= str_pad("Serial No:", $padding) . "{$this->data->case_no}\r";
				$body .= str_pad("Name:", $padding) . "{$this->data->client->name}\r";
				if($this->data->client->type == "staff" || $this->data->client->type == "dependent"){
					$body .= str_pad("Organization:", $padding) . "{$this->data->client->organization}\r";
				}
				$body .= str_pad("Invoice No:" , $padding) . "{$this->data->vat->pfNo}\r";
				$body .= str_pad("VAT Amount:" , $padding) . "KSH. {$this->data->vat->vatAmount}\r";
			break;

			case "blanket":
				$body = "Details are as follows:\r";
				$body .= str_pad("Serial No:", $padding) . "{$this->data->case_no}\r";
				if($this->data->client->type == "staff" || $this->data->client->type == "dependent"){
					$body .= str_pad("Name:", $padding) . "{$this->data->client->name}\r";
				}
				$body .= str_pad("Organization:", $padding) . "{$this->data->client->organization}\r";
				$body .= str_pad("Supplier:", $padding) . "{$this->data->vatObj->supplier->NAME}\r";
				$body .= str_pad("VAT Amount:", $padding) . "{$this->data->vatObj->VAT_AMOUNT}\r";
				$body .= str_pad("Invoice No:", $padding) . "{$this->data->vatObj->INVOICE_NO}\r";
				$body .= str_pad("Invoice Date:", $padding) . "{$this->data->vatObj->INVOICE_DATE}\r";
				$body .= str_pad("Duration:", $padding) . "{$this->data->vatObj->DURATION_FROM} to {$this->data->vatObj->DURATION_TO}\r";
				$body .= "\r";
				$body .= "UNON is therefore seeking the esteemed Ministry's approval of the annual VAT exemption.";
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
