<?php

namespace App\Helpers\HCSU\PDFTK;

use App\Models\Agency;

class NoteVerbal {
	protected $process;
	public $header, $footer, $body;
	protected $data, $initials, $showdate;

	public function __construct($process, $data, $initials, $showdate=true){
		$this->process = $process;
		// dd($this->process);
		$this->data = $data;
		$this->initials = $initials;
		$this->showdate = $showdate;
	}

	public function getHeader(){
		$connector = "";
		$end_header = "";
//		$body = "";
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
				if($this->data->type == "new"){
					$connector = "apply for a diplomatic identity card for";
				}
				else{
					$connector = "apply for {$this->data->dipData->APPLICATION_TYPE} of Diplomatic ID card for";
					if($this->data->dipData->REPLACEMENT_REASON){
						if ($this->data->dipData->REPLACEMENT_REASON == "lost") {
							$end_header .= ". The original ID card was stolen as confirmed by the attached original Abstract from Police Records.";
						}else{
							$end_header .= ". The original ID card was defaced as confirmed by the attached original Abstract from Police Records.";
						}
					}
				}
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
							if($this->data->caseData->DEPENDENTS){
								$end_header .= " and Dependants Pass";
							}
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
							if($this->data->caseData->DEPENDENTS){
								$genderConjunction = ($this->data->client->gender == "MALE") ? "his" : "her";
								$end_header .= " and {$genderConjunction} {$relationshipString}";
							}
						}
					}

					if ($this->data->type == "renewal" && $this->data->caseData->TYPE == "transfer"){
						$end_header .= " from the old passport(s) to the new one(s)";
					}

					$end_header .= ".";
				}else{
					$connector = "apply for";
					$end_header = "";
					if ($this->data->type == "new-case") {
						$end_header .= "issuance";
					}else if ($this->data->type == "endorsement"){
						$end_header .= "endorsement";
					}else if($this->data->type == "renewal"){
						if($this->data->caseData->TYPE == "renewal"){
							$end_header .= "the renewal";
						}else{
							$end_header .= "the transfer";
						}
					}

					if ($this->data->client->type == "dependent") {
						// dd($this->data->client->principal);
						$your_ref = "Your Ref: {$this->data->client->principal->R_NO}\n";
						$end_header .= " of Dependants Pass for the under mentioned dependant(s) of {$this->data->client->principal->principal_name}, a {$this->data->client->contract_type} of {$this->data->client->organization}.";
					}else{
						$end_header .= " of Exemption from Kenya Work Permit for the under mentioned domestic staff of {$this->data->client->principal->principal_name}, a {$this->data->client->contract_type} of {$this->data->client->organization}.";
					}
				}

				if ($this->data->type == "new-case") {
					if ($this->data->caseData->TYPE == "New Status") {
						$your_ref = "Your Ref: {$this->data->client->RNO}\n";
					}
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
			case 'revalidation':
				$connector = "forward the attached {$this->data->applicationType} for";
				if ($this->data->client->type == "staff") {
					$end_header .= " the under mentioned {$this->data->client->contract_type} of {$this->data->client->organization}";
				}else if($this->data->client->type == "agency"){
					$end_header .= " {$this->data->client->fullname}";
				}else if($this->data->client->type == "dependent"){
					$end_header .= " the under mentioned {$this->data->client->relationship} of {$this->data->client->principal->fullname}, a {$this->data->client->contract_type}  of {$this->data->client->organization}";
				}

				$end_header .= ", for revalidation.";
				break;
			case 'form_a':
				$connector = "submit the attached Form A for";
				if ($this->data->client->type == "staff") {
					$end_header .= "{$this->data->client->fullname}, a {$this->data->client->contract_type} of {$this->data->client->organization}";
				}else if($this->data->client->type == "agency"){
					$end_header .= " {$this->data->client->fullname}";
				}else if($this->data->client->type == "dependent"){
					$end_header .= "{$this->data->client->fullname}, a {$this->data->client->contract_type}  of {$this->data->client->organization}";
				}

				$end_header .= ", for approval.";
				break;
			case 'form-7':
				$connector = "submit an application for Kenyan Driving License for the undermentioned";
				if ($this->data->client->type == "staff") {
					$connector .= " {$this->data->client->contract_type} of {$this->data->client->organization}.";
				}else{
					$connector .= " {$this->data->client->relationship} of {$this->data->client->principal->LAST_NAME}, {$this->data->client->principal->OTHER_NAMES}, a {$this->data->client->contract_type} of {$this->data->client->organization}.";
				}
				break;
			case 'logbook':
				if ($this->data->client->type == "staff") {
					$end_header .= "{$this->data->client->name}, a {$this->data->client->contract_type} of {$this->data->client->organization}";
				}else if($this->data->client->type == "agency"){
					$end_header .= " {$this->data->client->name}";
				}else if($this->data->client->type == "dependent"){
					$end_header .= "{$this->data->client->name}, {$this->data->client->relationship} of {$this->data->client->principal}, a {$this->data->client->contract_type}  of {$this->data->client->organization}";
				}
				break;
			case 'nod':
				$end_header= "the undermentioned staff member of {$this->data->client->organization}";
				if (isset($this->data->client->dependents)) {
					$end_header .= " {$this->data->client->include}.";
				}
				$connector = "forward a completed Notification of Departure for";
				break;
			case 'airport-pass':
				$connector = "request the esteemed Ministry's approval for the issuance of Annual Airport Passes for the year {$this->data->client->applicationYear} for the following staff member of {$this->data->client->organization} whose duties require them to visit the airport often to meet senior United Nations officials arriving at JKIA, Nairobi:";
				break;

			case 'staff-management':
				$applicationtype = "";
				if ($this->data->client->type == "staff") {
					$applicationtype = "Staff";
				}else if($this->data->client->type == "dependent"){
					$applicationtype = "Dependent";
				}

				if ($this->data->staffRegistrationData->APPLICATION_TYPE == 'registration') {
					$applicationtype = "{$applicationtype} Registration";
				}else{
					$applicationtype = "{$applicationtype} Modification";
				}
				$connector = "submit a {$applicationtype} for";

				$end_header = (isset($this->data->client->relationship)) ? "the under mentioned {$this->data->client->relationship} of {$this->data->client->principal}" : "{$this->data->client->name}";
				$end_header .= ", an internationally recruited staff member of {$this->data->client->organization}.";
				break;

			case 'firearms':
				$your_ref = "Your Ref: \n";
				$connector = "request for your assistance in securing approval and issuance of import/export permits for {$this->data->client->name} as per the following details: {$this->data->firearmsData->REQUEST_DETAILS}";
				break;
		}

		if($this->process == "logbook"){
			$this->header = "Your Ref:";
			$this->header .= "\rOur Ref:{$this->data->ref}/$this->initials\n\n";
			$this->header .= "                            VEHICLE REGISTRATION\n";
			$this->header .= "Please find enclosed approved registration Form A for {$end_header}, for the registration of his vehicle whose details are given below:\n\n";
		}
		else if($this->process == "airport-pass-locals"){
			$this->header = "Our Ref: {$this->data->ref}/{$this->initials}    {$this->data->date}\n\n";
			$this->header .= "General Manager\nSecurity Services\nKenya Airports Authority\nP.O Box 19087-00501\nNairobi\n\n\n";
			$this->header .= "RE: Request for {$this->data->type} of Annual Airport Pass - {$this->data->client->organization}\n\n";
			$this->header .= "The United Nations Office at Nairobi (UNON) wishes to apply for issuance of Annual Airport Pass for the following staff member of {$this->data->client->organization} whose duties require him to go to the airport very often to meet with senior United Nations officials arriving at JKIA, Nairobi.";
		}
		else{
			$this->header = "{$your_ref}Our Ref: {$this->data->ref}/$this->initials\n\nThe United Nations Office at Nairobi (UNON) presents its compliments to the Ministry of Foreign Affairs of the Republic of Kenya and has the honour to {$connector} {$end_header}\n\n";
		}

		return $this;
	}

	public function getFooter(){
		if($this->process == "logbook"){
			$this->footer = "Your assistance to facilitate the registration of the vehicle will be highly appreciated\r\r\r\r\r\r\r\r                            {$this->data->date}";
		}
		else if($this->process == "airport-pass-locals"){
			$this->footer = "Any assistance that could be extended to the above-mentioned staff member of {$this->data->client->organization} to obtain the annual airport pass for the year {$this->data->actual->APPLICATION_YEAR} would be highly appreciated.\r\r\r\r\r\r\r\r                            Samuel OLAGO, Manager\n";
			$this->footer .= "                        Host Country Services Unit, UNON";
		}
		else{
			$dateContent = "";
			if ($this->showdate) {
				$dateContent = "\r\r\r\r\r\r\r\r                            {$this->data->date}";
			}
			$this->footer = "The United Nations Office at Nairobi (UNON) avails itself of this opportunity to renew to the Ministry of Foreign Affairs of the Republic of Kenya the assurances of its highest consideration.{$dateContent}";
		}

		return $this;
	}

	public function getBody(){
		$body = "";
		$padding = 20;
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

			case "nod":
				$body = "Details are as follows:\r";
				$body .= str_pad("Name:", $padding, " ", STR_PAD_RIGHT) . "{$this->data->client->name}\r";
				$body .= str_pad("Nationality:", $padding, " ", STR_PAD_RIGHT) . "{$this->data->client->nationality}\r";
				$body .= str_pad("Title:", $padding, " ", STR_PAD_RIGHT) . "{$this->data->client->designation}/{$this->data->client->grade}\r";

				if (isset($this->data->client->dependents)) {
					$body .= "\r";
					$body .= ucwords(strtolower($this->data->client->dependentLine)) . "\r";
					$body .= str_pad("Name", 40 , " ", STR_PAD_RIGHT) . str_pad("Nationality", 27 , " ", STR_PAD_RIGHT) . "Title\r";
					foreach ($this->data->client->dependents as $dep) {
						$body .= substr((str_pad(trim($dep['name']), 40, " ", STR_PAD_RIGHT) . str_pad(trim($dep['nationality']), 27, " ", STR_PAD_RIGHT) . trim($dep['title'])), 0, 78) . "\r\r";
					}
				}

				if (count($this->data->caseData->diplomaticid_status)) {
					$returned_statement = "";
                    $lost_statement = "";
					$keys = array_keys($this->data->caseData->diplomaticid_status);
					if (in_array("Returned", $keys) && in_array("Lost", $keys)) {
						$returned_connector = (count($this->data->caseData->diplomaticid_status['Returned']) > 1) ? "are" : "is";
						$returned_pluralised = (count($this->data->caseData->diplomaticid_status['Returned']) > 1) ? "Diplomatic ID Cards No. " : "Diplomatic ID Card No.";

						$lost_connector = (count($this->data->caseData->diplomaticid_status['Lost']) > 1) ? "were" : "was";
						$lost_pluralised = (count($this->data->caseData->diplomaticid_status['Lost']) > 1) ? "Diplomatic ID Cards No. " : "Diplomatic ID Card No.";
						$lost_connector2 = (count($this->data->caseData->diplomaticid_status['Lost']) > 1) ? "are copies" : "is a copy";

						$returned_statement = "Attached herewith {$returned_connector} the original {$returned_pluralised} (" . combined_string($this->data->caseData->diplomaticid_status['Returned']) . ") for cancellation.";

						$lost_statement = "The original {$lost_pluralised} (". combined_string($this->data->caseData->diplomaticid_status['Lost']) . ") {$lost_connector} reported lost. Attached herewith {$lost_connector2} of police abstract(s) for your attention.";

						$body .= $returned_statement . " " . $lost_statement;
					}else if (in_array("Returned", $keys) && !in_array("Lost", $keys)){
						$returned_connector = (count($this->data->caseData->diplomaticid_status['Returned']) > 1) ? "are" : "is";
						$returned_pluralised = (count($this->data->caseData->diplomaticid_status['Returned']) > 1) ? "Diplomatic ID Cards No. " : "Diplomatic ID Card No.";

						$returned_statement = "Attached herewith {$returned_connector} the original {$returned_pluralised} (" . combined_string($this->data->caseData->diplomaticid_status['Returned']) . ") for cancellation.";

						$body .= $returned_statement;
					}

					else if (!in_array("Returned", $keys) && in_array("Lost", $keys)){
						$lost_connector = (count($this->data->caseData->diplomaticid_status['Lost']) > 1) ? "were" : "was";
						$lost_pluralised = (count($this->data->caseData->diplomaticid_status['Lost']) > 1) ? "Diplomatic ID Cards No. " : "Diplomatic ID Card No.";
						$lost_connector2 = (count($this->data->caseData->diplomaticid_status['Lost']) > 1) ? "are copies" : "is a copy";

						$lost_statement = "The original {$lost_pluralised} (". combined_string($this->data->caseData->diplomaticid_status['Lost']) . ") {$lost_connector} reported lost. Attached herewith {$lost_connector2} of police abstract(s) for your attention.";

						$body .= $lost_statement;
					}
				}
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

				if($this->data->type == "new"){
					$body .= "The Ministry's assistance in issuance of a Diplomatic Identity Card will be highly appreciated.";
				}
				else{
					$body .= "The Ministry's assistance in {$this->data->dipData->APPLICATION_TYPE} of the Diplomatic Identity Card would be highly appreciated.";
				}
			break;

			case 'work-permit-new-case':
			case 'work-permit-endorsement':
			case 'work-permit-renewal':
				$body = "Details are as follows:\r";

				// dd($this->data->type);

				if($this->data->type == "endorsement" && $this->data->endorsementType == "dependant_pass"){

				}else{
					if($this->data->client->type == "dependent"){
						$dependant = $this->data->client->data;
						$body .= str_pad("Name", 30) . str_pad("Passport No", 15) . str_pad("Nationality", 20) . "Validity\r";
						if(count($this->data->client->allDependents)){
							foreach ($this->data->client->allDependents as $dependant) {
								$passport_no = ($dependant->latest_passport) ? $dependant->latest_passport->PASSPORT_NO : "N/A";
								$passport_validity = ($dependant->latest_passport) ? $dependant->latest_passport->EXPIRY_DATE : "N/A";
								$body .= str_pad(ucwords(strtolower($dependant->OTHER_NAMES)) . " " . strtoupper($dependant->LAST_NAME), 30) . str_pad($passport_no, 15) . str_pad($dependant->COUNTRY, 20) . str_pad($passport_validity, 20);
							}
						}
					}else{
						$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
						$body .= str_pad("Passport No.: ", $padding) . "{$this->data->client->passport}\r";
						$body .= str_pad("Nationality: ", $padding) . "{$this->data->client->nationality}\r";
						if($this->data->type == "new-case"){
							if ($this->data->client->RNO && $this->data->client->RNO != "NULL") {
								$body .= str_pad("R. No: ", $padding) . "{$this->data->client->RNO}\r";
							}else{
								$body .= str_pad("R. No: ", $padding) . "New Case\r";
							}
						}else{
							$body .= str_pad("R. No: ", $padding) . "{$this->data->client->RNO}\r";
						}
						$body .= str_pad("Validity: ", $padding) . "{$this->data->client->passport_validity}\r";
					}
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
			$body .= str_pad("Make: ", $padding) . "{$this->data->vehicle->make_model}\r\r";
			if($this->data->caseData->DUTY_PAID){
				$body .= "All relevant taxes have been duly paid at Kenya Revenue Authority as confirmed by the provided copy of duty payment receipt. ";
			}

			$body .= "Find attached herewith, copies of Log book, previous approved PRO 1B and C17B. ";

			if($this->data->caseData->NUMBER_PLATES_STATUS == 1){
				$body .= "The number plates have been surrendered as confirmed by the attached list of returned number plates acknowledged by NTSA.";
			}

			if($this->data->caseData->TYPE_OF_BUYER == "privileged"){
				if ($this->data->caseData->BUYER_AGENCY) {
					$agency = $this->data->client->organization;

					$buyerAgency = Agency::where('AGENCY_ID', $this->data->caseData->BUYER_AGENCY)->first();

					if ($buyerAgency->ACRONYM == $agency) {
						$body .= "The number plates have been reassigned to the buyer in line with the attached Note Verbale Ref. No. MFA/PROT. 7/2 dated March 14, 2014 from the Ministry.";
					}
				}
			}

			if ($this->data->caseData->ADDITIONAL_COMMENTS) {
				$body .= "\r\r{$this->data->caseData->ADDITIONAL_COMMENTS}\r";
			}
			break;

		case "revalidation":
			$padding += 3;
			$body .= str_pad("Serial No: ", $padding) . "{$this->data->caseData->INITIAL_CASE_NO}\r";
			$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
			if (in_array($this->data->applicationType, ["PRO1A", "PRO1B"])) {
				$body .= str_pad("Description: ", $padding) . "{$this->data->description}\r";
			}else{
				$body .= str_pad("Registration No: ", $padding) . "{$this->data->vehicle->registration}\r";
				$body .= str_pad("Chassis No: ", $padding) . "{$this->data->vehicle->chassis_no}\r";
				$body .= str_pad("Engine No: ", $padding) . "{$this->data->vehicle->engine_no}\r";
				$body .= str_pad("Make: ", $padding) . "{$this->data->vehicle->make_model}\r\r";
			}
			break;

		case 'form-7':
			$body = "Details are as follows:\r";
			$padding += 3;
			$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
			$body .= str_pad("Nationality: ", $padding) . "{$this->data->client->nationality}\r";
			$body .= str_pad("License No: ", $padding) . "{$this->data->caseData->LICENSE_NO}\r";
			$body .= str_pad("Expiry Date: ", $padding) . "{$this->data->caseData->EXPIRY_DATE}\r\r";

			$body .= "Duly completed Form VII in duplicate, two passport size photos, and copies of Diplomatic ID Card and Passport are attached herewith.\r";
			break;
		case 'form_a':
		    $vehicle_type = $this->data->caseData->vehicle->type->VEH_TYPE;
			$body = "{$vehicle_type} details are as follows:\r";

			// $body .= "\r{$this->data->caseData->EXTRA_COMMENTS}\r";
			$body .= str_pad("Make: ", $padding) . "{$this->data->caseData->vehicle->make->MAKE_MODEL}\r";
			$body .= str_pad("Chassis No: ", $padding) . "{$this->data->caseData->vehicle->CHASSIS_NO}\r";
			$body .= str_pad("Engine No: ", $padding) . "{$this->data->caseData->vehicle->ENGINE_NO}\r";

			if ($this->data->caseData->COMMENTS) {
                $body .= "\r{$this->data->caseData->COMMENTS}\r";
            }

			if($this->data->caseData->DUTY_PAID == "YES"){
			    if($this->data->caseData->vehicle->type->ID == 2){
			        if ($this->data->caseData->MOTOR_BIKE_PLATES == "1"){
                        if ($this->data->caseData->PLATE_TYPE == "civilian") {
                            $body .= "\rKindly note that the {$vehicle_type} is duty paid and currently registered on civilian plates Reg. {$this->data->caseData->vehicle->ORIGINAL_REGISTRATION}. The staff member would like to re-register the vehicle and change the plates from civilian to diplomatic.\r";
                        } else {
                            $body .= "\rKindly note that the {$vehicle_type} is duty paid and currently registered on diplomatic plates Reg. {$this->data->caseData->vehicle->PLATE_NO}. The staff member would like to re-register the vehicle and change the plates from diplomatic to diplomatic.\r";
                        }
                    }
                }else {
                    if ($this->data->caseData->PLATE_TYPE == "civilian") {
                        $body .= "\rKindly note that the {$vehicle_type} is duty paid and currently registered on civilian plates Reg. {$this->data->caseData->vehicle->ORIGINAL_REGISTRATION}. The staff member would like to re-register the vehicle and change the plates from civilian to diplomatic.\r";
                    } else {
                        $body .= "\rKindly note that the {$vehicle_type} is duty paid and currently registered on diplomatic plates Reg. {$this->data->caseData->vehicle->PLATE_NO}. The staff member would like to re-register the vehicle and change the plates from diplomatic to diplomatic.\r";
                    }
                }

			    if ($this->data->caseData->DOCUMENTS){
			        $docsArray = json_decode($this->data->caseData->DOCUMENTS);

			        $noDocs = (count($docsArray) > 1) ? "are" : "is";
			        $body .= "\r" . combined_string($docsArray) . " {$noDocs} attached herewith. \r";
                }else{
                    $body .= "\rCopies of current logbook and insurance certificate are attached herewith.\r";
                }

			}

			else{
				$body .= "\rCopies of duplicate Insurance Certificate and approved Pro 1B are attached herewith.";
			}


			break;

		case 'airport-pass':
			$body = "Details are as follows:\r";
			$padding += 3;
			$closure = ($this->data->actual->APPLICATION_TYPE == "renewal") ? "Renewal" : "New";
			$body .= str_pad("{$this->data->client->name}", $padding += 8) . str_pad($this->data->client->designation, $padding += 4) . " ({$closure})";

			if ($this->data->actual->ADDITIONAL_COMMENTS) {
				$body .= "\r{$this->data->actual->ADDITIONAL_COMMENTS}\r";
			}
			break;

		case 'airport-pass-locals':
			$body = "\n\nDetails are as follows:\r";
			$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
			$body .= str_pad("National ID: ", $padding) . "{$this->data->actual->IDENTIFICATION}\r";

			$body .= "\n\nFollowing the implementation of IPMIS (Integrated Protocol Management Information System) by the Ministry of Foreign Affairs and International Trade, Kenya nationals are not required to seek approval through the ministry.";
			break;

		case 'staff-management':
			$body .= "Details are as follows:\r";
			if ($this->data->client->type == "staff") {
				$body .= str_pad("Name: ", $padding) . "{$this->data->client->name}\r";
				$body .= str_pad("Nationality: ", $padding) . "{$this->data->client->nationality}\r";
				$body .= str_pad("Title: ", $padding) . "{$this->data->client->designation}/{$this->data->client->grade}\r";
			}else{
				// $body .= ucwords($this->data->client->relationship) . "\r";
				$body .= str_pad("Name", $padding) . str_pad("Nationality", 25) . "Title\r";
				$body .= str_pad($this->data->client->name, $padding) . str_pad($this->data->client->nationality, 25) . ucwords($this->data->client->relationship) . "\r";
			}
			$body .= str_pad("Passport No: ", $padding) . "{$this->data->client->passport}\r";
			if($this->data->staffRegistrationData->COMMENTS){
				$body .= "\n{$this->data->staffRegistrationData->COMMENTS}\n";
			}
			$body .= "\rThe Ministry's assistance in processing the application will be highly appreciated.";
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

		case 'firearms':
			if (count($this->data->firearmsData->staff_details)) {
				$body = str_pad("NAME OF OFFICER", $padding) . str_pad("WEAPON TYPE", $padding) . str_pad("SERIAL_NO", $padding) . str_pad("AMMUNITION_ROUNDS", $padding);

				foreach($this->data->firearmsData->staff_details as $details){
					$body .= str_pad("{$details->lastName} {$details->otherNames}", $padding) . str_pad(strtoupper($details->weaponType), $padding) . str_pad(strtoupper($details->serialNo), $padding) . str_pad(strtoupper($details->ammunitionRounds), $padding);
				}
			}

			if ($this->data->firearmsData->EXTRA_COMMENTS) {
				$body .= "\r{$this->data->firearmsData->EXTRA_COMMENTS}";
			}
			

			break;
		}

		$this->body = $body;
	}

	public function getContent(){
		try{
			$this->getHeader();
			$this->getFooter();
			$this->getBody();

			return  $this->header . $this->body . "\n\n" . $this->footer;
		}catch(\Exeption $ex){
			dd($ex->getMessage());
		}
	}
}
