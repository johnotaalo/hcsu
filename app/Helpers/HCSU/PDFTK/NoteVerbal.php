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
		$your_ref = "";

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
			case 'work-permit-endorsement':
			case 'work-permit-renewal':
				if ($this->data->client->type == "staff") {
					$connector = "submit an application for";
					$end_header = "";
					if ($this->data->type == "new-case") {
						$end_header .= "issuance";
					}else if ($this->data->type == "endorsement"){
						if($this->data->endorsementType == "new_case" || $this->data->endorsementType == "dependant_pass")
							$end_header .= "endorsement";
					} else if ($this->data->type == "renewal"){
						$end_header .= "the ";
						if($this->data->caseData->TYPE == "renewal"){
							$end_header .= "renewal";
						}else{
							$end_header .= "transfer";
						}
					}

					if($this->process == "work-permit-endorsement" || $this->process == "work-permit-renewal"){
						$your_ref = "Your Ref: {$this->data->client->RNO}\n";
					}

					if ($this->data->type == "endorsement" && $this->data->endorsementType == "dependant_pass"){
						$end_header .= " of Exemption from Dependants Pass";
					}else{
						$end_header .= " of Exemption from Kenya Work Permit";
					}

					

					if(count($this->data->client->dependents) > 0){
						if($this->data->type == "endorsement" && $this->data->endorsementType == "dependant_pass"){
							
						}else{
							$end_header .= " and Dependants Pass";
						}
						
					}

					$relationshipString = "";

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
					}

					if($this->data->type == "endorsement" && $this->data->endorsementType == "dependant_pass"){
						$end_header .= " for the under mentioned {$relationshipString} of {$this->data->client->name}, a {$this->data->client->contract_type} of {$this->data->client->organization}";
					}else{
						$end_header .= " for the under mentioned {$this->data->client->contract_type} of {$this->data->client->organization}";
					}

					if($this->data->client->relationships){
						if($this->data->type == "endorsement" && $this->data->endorsementType == "dependant_pass"){

						}else{
							$end_header .= " and his {$relationshipString}";
						}
					}

					if ($this->data->type == "renewal" && $this->data->caseData->TYPE == "transfer"){
						$end_header .= " from the old passports to the new ones";
					}

					$end_header .= ".";
				}else{
					$connector = "apply for";
					$end_header = "";
					if ($this->data->type == "new-case") {
						$end_header .= "issuance";
					}else if ($this->data->type == "endorsement"){
						$end_header .= "endorsement";
					}

					$end_header .= " of Exemption from Kenya Work Permit for the under mentioned domestic staff of {$this->data->client->principal->principal_name}, a {$this->data->client->contract_type} of {$this->data->client->organization}";
				}
				break;
			case 'domestic-worker-justification':
				$connector = "apply for";
				$end_header = "";
				if ($this->data->type == "new-case") {
					$end_header .= "issuance";
				}

				$end_header .= " of Exemption from Kenya Work Permit for the under mentioned domestic staff of {$this->data->client->principal->principal_name}, {$this->data->client->contract->DESIGNATION} with {$this->data->client->organization} in Kenya.";
				break;

			case 'pro1a':
			case 'pro1b':
			case 'pro1c':
				$processTitle = strtoupper($this->process);
				$connector = "forward the following {$processTitle} for";
				if ($this->data->client->type == "staff") {
					$end_header .= " the under mentioned {$this->data->client->contract_type} of {$this->data->client->organization}";
				}else if($this->data->client->type == "agency"){
					$end_header .= " {$this->data->client->fullname}";
				}else if($this->data->client->type == "dependent"){
					$end_header .= " the under mentioned {$this->data->client->relationship} of {$this->data->client->principal->fullname}, a {$this->data->client->contract_type}  of {$this->data->client->organization}";
				}

				$end_header .= ", for approval.";
				break;
			case 'form_a':
				$connector = "submit the attached Form A for";
				if ($this->data->client->type == "staff") {
					$end_header .= "{$this->data->client->fullname}, a {$this->data->client->contract_type} of {$this->data->client->organization}";
				}else if($this->data->client->type == "agency"){
					$end_header .= " {$this->data->client->fullname}";
				}else if($this->data->client->type == "dependent"){
					$end_header .= "{$this->data->client->fullname}, {$this->data->client->relationship} of {$this->data->client->principal->fullname}, a {$this->data->client->contract_type}  of {$this->data->client->organization}";
				}

				$end_header .= ", for approval.";
				break;
			case 'logbook':
				if ($this->data->client->type == "staff") {
					$end_header .= "{$this->data->client->name}, a {$this->data->client->contract_type} of {$this->data->client->organization}";
				}else if($this->data->client->type == "agency"){
					$end_header .= " {$this->data->client->name}";
				}else if($this->data->client->type == "dependent"){
					$end_header .= "{$this->data->client->name}, {$this->data->client->relationship} of {$this->data->client->principal->fullname}, a {$this->data->client->contract_type}  of {$this->data->client->organization}";
				}
				break;
		}

		if($this->process == "logbook"){
			$this->header = "Your Ref:";
			$this->header .= "\rOur Ref:{$this->data->ref}/$this->initials\n\n";
			$this->header .= "                            VEHICLE REGISTRATION\n";
			$this->header .= "Please find enclosed approved registration Form A for {$end_header}, for the registration of his vehicle whose details are given below:\n\n";
		}else{
			$this->header = "{$your_ref}Our Ref: {$this->data->ref}/$this->initials\n\nThe United Nations Office at Nairobi (UNON) presents its compliments to the Ministry of Foreign Affairs of the Republic of Kenya and has the honour to {$connector} {$end_header}\n\n";
		}
		

		return $this;
	}

	public function getFooter(){
		if($this->process == "logbook"){
			$this->footer = "Your assistance to facilitate the registration of the vehicle will be highly appreciated\r\r\r\r\r\r\r\r                            {$this->data->date}";
		}else{
			$this->footer = "The United Nations Office at Nairobi (UNON) avails itself of this opportunity to renew to the Ministry of Foreign Affairs of the Republic of Kenya the assurances of its highest consideration.\r\r\r\r\r\r\r\r                            {$this->data->date}";
		}

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
				if($this->data->client->type == "staff" || $this->data->client->type == "dependent"){
					$body .= str_pad("PIN:", $padding) . "{$this->data->client->pin}\r";
				}
				$body .= "\r";
				$body .= "UNON is therefore seeking the esteemed Ministry's approval of the annual VAT exemption.";
			break;

			case "pin":
				$body = "Details are as follows:\r";
				$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
				$body .= str_pad("Nationality: ", $padding) . "{$this->data->client->nationality}\r";
				$body .= str_pad("Passport No.: ", $padding) . "{$this->data->client->passport}\r";
				$body .= "\r";
				if ($this->data->client->contract_type == "Consultant") {
					$body .= "Kindly note that the staff member is a consultant and does not qualify for diplomatic privileges hence the attached NoA and copy of the ground pass.\r\r";
				}
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
					// $body .= ucwords($this->data->client->relationship) . "\r";
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
			case 'work-permit-endorsement':
			case 'work-permit-renewal':
				$body = "Details are as follows:\r";

				if($this->data->type == "endorsement" && $this->data->endorsementType == "dependant_pass"){

				}else{
					$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
					$body .= str_pad("Passport No.: ", $padding) . "{$this->data->client->passport}\r";
					$body .= str_pad("Nationality: ", $padding) . "{$this->data->client->nationality}\r";
					if($this->data->type == "new-case"){
						$body .= str_pad("R. No: ", $padding) . "New Case\r";
					}else{
						$body .= str_pad("R. No: ", $padding) . "{$this->data->client->RNO}\r";
					}
					$body .= str_pad("Validity: ", $padding) . "{$this->data->client->passport_validity}\r";
				}

				if($this->data->client->type == "staff"){
					
					if(!is_null($this->data->caseData->DEPENDENTS) && $this->data->caseData->DEPENDENTS != ""){
						$relationshipString = "";
						if($this->data->client->relationships){
							$relationships = [];
							foreach ($this->data->client->relationships as $relationship) {
								if ($relationship != "Spouse") {
									$relationship = ucwords("dependants");
								}else{
									$relationship = ucwords(strtolower($relationship));
								}

								array_push($relationships, $relationship);
							}

							$uniqueRelationships = collect($relationships)->unique()->toArray();
							$relationshipString = implode(" and ", $uniqueRelationships);
						}

						if($this->data->type == "endorsement" && $this->data->endorsementType == "dependant_pass"){
						}else{ 
							$body .= "\r{$relationshipString}\r";
						}
						$body .= str_pad("Name", 30) . str_pad("Passport No", 15) . str_pad("Nationality", 20) . "Validity\r";
						foreach ($this->data->client->dependents as $dependant) {
							// dd($dependant->latest_passport);
							$passport_no = ($dependant->latest_passport) ? $dependant->latest_passport->PASSPORT_NO : "N/A";
							$passport_validity = ($dependant->latest_passport) ? $dependant->latest_passport->EXPIRY_DATE : "N/A";

							$body .= str_pad(ucwords(strtolower($dependant->OTHER_NAMES)) . " " . strtoupper($dependant->LAST_NAME), 30) . str_pad($passport_no, 15) . str_pad($dependant->COUNTRY, 20) . str_pad($passport_validity, 20);
						}
					}

					if($this->process == "work-permit-new-case" || ($this->process == "work-permit-renewal" && $this->data->caseData->TYPE == "renewal")){
						$include_28 = (!is_null($this->data->caseData->DEPENDENTS) && $this->data->caseData->DEPENDENTS != "") ? "& 28" : "";
						$body .= "\rDuly completed Forms 25 {$include_28} together with a copy of the above mentioned passport is attached herewith.\r";
					}else if ($this->process == "work-permit-endorsement"){
						$body .= "\rThe above mentioned passport is attached herewith.";
					}else if ($this->process == "work-permit-renewal" && $this->data->caseData->TYPE == "transfer"){
						$body .= "\rAttached herewith is the above mentioned passport(s). Also attached is a copy/copies of previous exemption from the old passports";
					}
				}

				if($this->data->caseData->COMMENTS && $this->data->client->type == "staff"){
					$body .= "\r{$this->data->caseData->COMMENTS}\r";
				}

				if($this->data->caseData->ADDITIONAL_COMMENTS){
					$body .= "\r{$this->data->caseData->ADDITIONAL_COMMENTS}\r";
				}

				if($this->data->caseData->JUSTIFICATION && $this->data->client->type == "domestic-worker"){
					$body .= "\r{$this->data->caseData->JUSTIFICATION}\r";
				}

				if($this->data->client->type == "domestic-worker"){
					$body .= "\rDuly completed Form 25, copies of above-mentioned passport, other relevant supporting documents and a copy of the staff member's Passport No. {$this->data->client->principal->latest_passport->PASSPORT_NO} with valid Exemption from Kenya Work Permit No. R-{$this->data->client->principal->R_NO}.\r";
				}
				
			break;

		case 'domestic-worker-justification':
			$body = "The details of the house help are as follows:\r";
			$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
			$body .= str_pad("Passport No.: ", $padding) . "{$this->data->client->passport}\r";
			$body .= str_pad("Nationality: ", $padding) . "{$this->data->client->nationality}\r";
			$body .= str_pad("Validity: ", $padding) . "{$this->data->client->passport_validity}\r";

			$body .= "\rThe Host Country Agreement signed between UNEP and Government of Kenya provides in article XI, section 22 (a)i, that privileges granted to officials of the United Nations include among other things residence Permits for \"Members\" of permanent missions and other representatives of Member States, their families and other members of their households.\r";
			$body .= "\rThe office of the Director General, UNON wishes to inform the esteemed Ministry that {$this->data->caseData->JUSTIFICATION} and therefore issuance of Exemption from Kenya Work Permit to her domestic worker is highly recommended.\r";
			break;

		case 'pro1a':
		case 'pro1b':
		// dd($this->data->caseData->vehicle->make->MAKE_MODEL);
			$body = "Details are as follows:\r";
			$body .= str_pad("Serial No: ", $padding) . "{$this->data->case_no}\r";
			$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
			if($this->process == "pro1b" && $this->data->caseData->TYPE_OF_GOODS == "vehicle"){
				$this->data->description = "1 (ONE) {$this->data->caseData->vehicle->VEHICLE_CATEGORY} vehicle, ({$this->data->caseData->vehicle->make->MAKE_MODEL}) Chassis No. {$this->data->caseData->vehicle->CHASSIS_NO}; YOM {$this->data->caseData->vehicle->YOM}; Engine No. {$this->data->caseData->vehicle->ENGINE_NO}; Rating: {$this->data->caseData->vehicle->RATING} CC; Color: {$this->data->caseData->vehicle->color->COLOUR}";
			}
			$body .= str_pad("Description: ", $padding) . "{$this->data->description}\r";
			$body .= str_pad("AWB/BL NO: ", $padding) . "{$this->data->caseData->AIRWAY_BILL_NO}\r";
			$body .= str_pad("Invoice No: ", $padding) . "{$this->data->caseData->INVOICE_NO}\r";

			if($this->process == "pro1b"){
				$body .= "\r{$this->data->caseData->EXTRA_COMMENTS}\r";
			}

			break;
		case 'pro1c':
			$body = "Details are as follows:\r";
			$padding += 3;
			$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
			$body .= str_pad("Registration No: ", $padding) . "{$this->data->vehicle->registration}\r";
			$body .= str_pad("Chassis No: ", $padding) . "{$this->data->vehicle->chassis_no}\r";
			$body .= str_pad("Engine No: ", $padding) . "{$this->data->vehicle->engine_no}\r";
			$body .= str_pad("Make: ", $padding) . "{$this->data->vehicle->make_model}\r";
			break;
		case 'form_a':
			$body = "Vehicle details are as follows:\r";

			$body .= "\r{$this->data->caseData->EXTRA_COMMENTS}\r";
			$body .= str_pad("Make: ", $padding) . "{$this->data->caseData->vehicle->make->MAKE_MODEL}\r";
			$body .= str_pad("Chassis No: ", $padding) . "{$this->data->caseData->vehicle->ENGINE_NO}\r";
			$body .= str_pad("Engine No: ", $padding) . "{$this->data->caseData->vehicle->CHASSIS_NO}\r";

			$body .= "\rA copy of duplicate Insurance Certificate and two copies of approved Pro 1B are attached herewith.";
			break;

		case 'logbook':
			$padding = $padding + 3;
			$body = str_pad("Registration No:", $padding) . "{$this->data->caseData->forma->PLATE_NO}\r";
			$body .= str_pad("Chassis No:", $padding) . "{$this->data->caseData->forma->vehicle->CHASSIS_NO}\r";
			$body .= str_pad("Engine No:", $padding) . "{$this->data->caseData->forma->vehicle->ENGINE_NO}\r";
			$body .= str_pad("Make:", $padding) . "{$this->data->caseData->forma->vehicle->make->MAKE_MODEL}\r";

			$body .= "\rThe following documents are also enclosed:\n";
			// $documents = json_decode($this->data->caseData->SUBMITTED_DOCUMENTS);
			$documents = [
				'Original approved Form Pro-1B',
				'Original Form C-17B',
				'Original Form C-17B receipt',
				'Original Bill of Lading/Airway Bill',
				'Original Invoice',
				'Original Insurance Certificate',
				'Original Form A',
				'Copy of Import Declaration Form',
				'Original Export Certificate',
				'Original Certificate of Roadworthiness',
				"Copy of Buyer's PIN",
				"Copy of Buyer's Passport"
			];
			$count = 1;
			foreach ($documents as $document) {
				$body .= "{$count}. {$document}\n";
				$count++;
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
