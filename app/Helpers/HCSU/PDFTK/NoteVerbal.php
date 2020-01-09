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
				$end_header="a Value Added Tax (VAT) Exemption claim.";
				$connector = "submit an application for";
				break;
			case 'blanket':
				$end_header = "UNON's annual Value Added Tax (VAT) and Excise Duty exemption application for {$this->data->vatObj->supplier->SERVICE_TYPE} services provided by {$this->data->vatObj->supplier->NAME} for the year {$this->data->year}.";
				$connector = "refer to";
				break;

			case 'pin':
				$end_header = "assistance in obtaining a Personal Identity Number (PIN) from Kenya Revenue Authority for the under mentioned {$this->data->client->contract_type} of {$this->data->client->organization}.";
				$connector = "request for";
				break;
			case 'diplomatic-id':
			case 'diplomatic-id-renewal':
				$end_header = (isset($this->data->client->relationship)) ? "the under mentioned {$this->data->client->relationship} of {$this->data->client->principal}" : "{$this->data->client->name}";
				$end_header .= ", an internationally recruited staff member of {$this->data->client->organization}";
				if($this->data->type == "new")
					$connector = "apply for a diplomatic identity card for";
				else
					$connector = "apply for renewal of Diplomatic ID card for";
				break;
			case 'work-permit-new-case':
				$connector = "submit an application for";
				$end_header = "";
				if ($this->data->type == "new-case") {
					$end_header .= "issuance";
				}

				$end_header .= " of Exemption from Kenya Work Permit";

				if($this->data->client->dependents){
					$end_header .= " and Dependants Pass";
				}

				$end_header .= " for the under mentioned {$this->data->client->contract_type} of {$this->data->client->organization}";

				if($this->data->client->relationships){
					$relationships = [];
					foreach ($this->data->client->relationships as $relationship) {
						if ($relationship != "Spouse") {
							$relationship = "dependants";
						}else{
							$relationship = strtolower($relationship);
						}

						array_push($relationships, $relationship);
					}

					$uniqueRelationships = collect($relationships)->unique()->toArray();
					$relationshipString = implode(" and ", $uniqueRelationships);

					$end_header .= " and his {$relationshipString}";
				}

				$end_header .= ".";
				
				break;
		}

		$this->header = "Our Ref: {$this->data->ref}/$this->initials\n\nThe United Nations Office at Nairobi (UNON) presents its compliments to the Ministry of Foreign Affairs & International Trade of the Republic of Kenya and has the honour to {$connector} {$end_header}\n\n";

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
				$body .= str_pad("Account No:", $padding) . "{$this->data->vatObj->ACCOUNT_NO}\r";
				$body .= str_pad("VAT Amount:", $padding) . "KSH. " . number_format($this->data->vatObj->VAT_AMOUNT, 2) . "\r";
				$body .= str_pad("Invoice No:", $padding) . "{$this->data->vatObj->INVOICE_NO}\r";
				$body .= str_pad("Invoice Date:", $padding) . "{$this->data->vatObj->INVOICE_DATE}\r";
				$body .= str_pad("Duration:", $padding) . "{$this->data->vatObj->DURATION_FROM} to {$this->data->vatObj->DURATION_TO}\r";
				$body .= "\r";
				$body .= "UNON is therefore seeking the esteemed Ministry's approval of the annual VAT exemption.";
			break;

			case "pin":
				$body = "Details are as follows:\r";
				$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
				$body .= str_pad("Nationality: ", $padding) . "{$this->data->client->nationality}\r";
				$body .= str_pad("Passport No.: ", $padding) . "{$this->data->client->passport}\r";
				$body .= "\r";
				$body .= "Mohamed Mubarak, UN ID Card No. 711305, Patrick Mwololo, UN ID Card No. 945740, Evans MWANZI, UN ID Card No. 292241 and Lilliya LIECH, UN ID Card No. 549559 are authorized to handle the PIN application of the above-mentioned {$this->data->client->contract_type} of {$this->data->client->organization}.";
			break;

			case "diplomatic-id":
			case "diplomatic-id-renewal":
				$body = "Details are as follows:\r";
				if ($this->data->client->type == "staff") {
					$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
					$body .= str_pad("Nationality: ", $padding) . "{$this->data->client->nationality}\r";
					$body .= str_pad("Title: ", $padding) . "{$this->data->client->designation}/{$this->data->client->grade}\r";
				}else{
					$body .= ucwords($this->data->client->relationship) . "\r";
					$body .= str_pad("Name", 40) . str_pad("Nationality", 25) . "Title\r";
					$body .= str_pad($this->data->client->name, 40) . str_pad($this->data->client->nationality, 25) . ucwords($this->data->client->relationship) . "\r";
				}
				$body .= "\r";

				if($this->data->dipData->NV_COMMENTS){
					$period = (substr($this->data->dipData->NV_COMMENTS, -1) == ".") ? "" : ".";
					$body .= "{$this->data->dipData->NV_COMMENTS}{$period}\r\r";
				}

				if($this->data->type == "new")
					$body .= "The Ministry's assistance in issuance of a Diplomatic Identity Card will be highly appreciated.";
				else
					$body .= "The Ministry's assistance in renewal of the Diplomatic Identity Card would be highly appreciated.";
			break;

			case 'work-permit-new-case':
				$body = "Details are as follows:\r";

				$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
				$body .= str_pad("Passport No.: ", $padding) . "{$this->data->client->passport}\r";
				$body .= str_pad("Nationality: ", $padding) . "{$this->data->client->nationality}\r";
				if($this->data->type == "new-case"){
					$body .= str_pad("R. No: ", $padding) . "New Case\r";
				}
				$body .= str_pad("Validity: ", $padding) . "{$this->data->client->passport_validity}\r";

				if($this->data->client->dependents){
					$body .= "\rSpouse and Dependants\r";
					$body .= str_pad("Name", 30) . str_pad("Passport No", 15) . str_pad("Nationality", 20) . "Validity\r";
					foreach ($this->data->client->dependents as $dependant) {
						// dd($dependant->latest_passport);
						$body .= str_pad(ucwords(strtolower($dependant->OTHER_NAMES)) . " " . strtoupper($dependant->LAST_NAME), 30) . str_pad("", 15) . str_pad($dependant->COUNTRY, 20);
					}
				}
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
