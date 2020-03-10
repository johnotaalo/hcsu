<?php

namespace App\Helpers\HCSU\Data;

use \App\Models\VAT;
use \App\Models\Principal;
use \App\Models\Agency;

class VATData{
	public static function get($case_no){
        $case_issues = [];
		try{
    		$data = [];

    		$vat_data = VAT::where('CASE_NO', $case_no)->first();
    		$firstIDChar = substr($vat_data->HOST_COUNTRY_ID, 0, 1);

    		$goodsArray = ($vat_data->invoices)->map(function($invoice){
                return $invoice->GOODS_DESCRIPTION;
            })->toArray();

            $invoiceNumbersArray = ($vat_data->invoices)->map(function($invoice){
                return $invoice->DOCUMENT_NO;
            })->toArray();

            $vatAmounts = ($vat_data->invoices)->map(function($invoice){
                return $invoice->VAT_AMOUNT;
            })->toArray();

            // if(count($vat_data->invoices) > 1){
            //     echo $case_no;
            //     dd($vat_data->invoices);die;
            // }

            $pfDate = $vat_data->invoices->min('DOCUMENT_DATE');

            $goods = self::generateCombinedString($goodsArray);
            $invoice_numbers = self::generateCombinedString($invoiceNumbersArray);
            $totalVAT = ($vat_data->invoices)->sum('VAT_AMOUNT');

            $mission = $client_name = $arrival = "";

            $clientObj = new \StdClass;
            $data = new \StdClass;
            $vatObj = new \StdClass;

            if($firstIDChar == "1"){
                $contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$vat_data->HOST_COUNTRY_ID})"))->first();
                $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $vat_data->HOST_COUNTRY_ID)->first();

                // die($principal->current_arrival);
                $name = strtoupper($principal->LAST_NAME). ", " . format_other_names($principal->OTHER_NAMES) ;
                $client_name = $name;
                $name = $name . "; " . $contract->DESIGNATION;
                $mission = $contract->ACRONYM;
                $arrivalDate = ($principal->current_arrival) ? $principal->current_arrival->ARRIVAL : "N/A";
                $diplomaticCardNo = ($principal->latest_diplomatic_card) ? $principal->latest_diplomatic_card->DIP_ID_NO : "N/A";
                $arrival = "{$arrivalDate} (Dip. Id No: {$diplomaticCardNo})";

                $clientObj->name = $client_name;
                $clientObj->designation = $contract->DESIGNATION;
                $clientObj->organization = $mission;
                $clientObj->index_no = $contract->INDEX_NO;
                $clientObj->type = "staff";
                $clientObj->arrival = $arrival;

            }else if ($firstIDChar == "3"){
                $agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $vat_data->HOST_COUNTRY_ID)->first();
                $name = $agency->ACRONYM;
                $mission = $name;
                $client_name = $name;
                $arrival = "N/A";

                $clientObj->name = $name;
                $clientObj->organization = $mission;
                $clientObj->type = "agency";
                $clientObj->arrival = $arrival;
            } else if ($firstIDChar == "2"){
    			$dependent = \App\Models\PrincipalDependent::where('HOST_COUNTRY_ID', $vat_data->HOST_COUNTRY_ID)->first();

    			$relationship = $dependent->relationship->RELATIONSHIP;

    			$relationship = ($relationship == "Spouse") ? "s/o" : $relationship . " of";

    			$c_name = strtoupper($dependent->LAST_NAME). ", " . format_other_names($dependent->OTHER_NAMES) . " {$relationship} {$dependent->principal->fullname}";
    			$name = "{$c_name}; {$dependent->principal->latest_contract->DESIGNATION}";
                $mission= "";
                if ($dependent->principal->latest_contract) {
                    $mission = $dependent->principal->latest_contract->ACRONYM;
                }
                $arrivalDate = ($dependent->principal->current_arrival) ? $dependent->principal->current_arrival->ARRIVAL : "N/A";
                $diplomaticCardNo = ($dependent->principal->latest_diplomatic_card) ? $dependent->principal->latest_diplomatic_card->DIP_ID_NO : "N/A";
                $arrival = "{$arrivalDate} (Dip. Id No: {$diplomaticCardNo})";
    			// $arrival = ($dependent->principal->current_arrival) ? "{$dependent->principal->current_arrival->ARRIVAL} (Dip. Id No: {$dependent->principal->latest_diplomatic_card->DIP_ID_NO})" : "N/A";

    			$clientObj->name = $c_name;
    			$clientObj->designation = $dependent->principal->latest_contract->DESIGNATION;
    			$clientObj->organization = $mission;
    			$clientObj->index_no = $dependent->principal->latest_contract->INDEX_NO;
    			$clientObj->type = "dependent";
    			$clientObj->arrival = $arrival;
    		}

            $vatObj->supplierName      =  $vat_data->supplier->SUPPLIER_NAME;
            $vatObj->supplierAddress   =  $vat_data->supplier->SUPPLIER_ADDRESS;
            $vatObj->supplierVAT       =  $vat_data->supplier->PIN;
            $vatObj->goodsDescription  =  $goods;
            $vatObj->pfNo              =  $invoice_numbers;
            $vatObj->vatAmount         =  number_format($totalVAT, 2);
            $vatObj->pfDate            =  $pfDate;
            $vatObj->status            =  $vat_data->app->APP_STATUS;

            $data->case_no = $case_no;
            $data->ref = $vat_data->EXEMPTION_FORM_REF_NO;
            $data->date = date('F d, Y', strtotime($vat_data->CREATED_AT));
            $data->client = $clientObj;
            $data->vat = $vatObj;

            return $data;
        }catch(\Exception $ex){
            $case_issues[] = $case_no;
        }

        dd($case_issues);
	}

	static function generateCombinedString($array){
        if(count($array) == 1){
            return $array[0];
        }else{
            $last = array_pop($array);
            $combined = implode(", ", $array) . " & " . $last;
            return $combined;
        }
    }
}
