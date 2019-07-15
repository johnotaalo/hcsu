<?php

namespace App\Helpers\HCSU\PDFTK;

class VATExemptionForm{
	private $filename;

	public function __construct(){
		
	}

	public function getFileName(){
		return $this->filename;
	}

	public function getData($case_no, $document){
		$vat_data = \App\Models\VAT::where('CASE_NO', $case_no)->first();
		$name = $filename = "";
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

        $goods = $this->generateCombinedString($goodsArray);
        $invoice_numbers = $this->generateCombinedString($invoiceNumbersArray);
        $totalVAT = ($vat_data->invoices)->sum('VAT_AMOUNT');

        $mission = $client_name = $arrival = "";

        if($firstIDChar == "1"){
            $contract = collect(\DB::select("CALL GET_LATEST_PRINCIPAL_CONTRACT({$vat_data->HOST_COUNTRY_ID})"))->first();
            $principal = \App\Models\Principal::where('HOST_COUNTRY_ID', $vat_data->HOST_COUNTRY_ID)->first();

            // die($principal->current_arrival);
            $name = strtoupper($principal->LAST_NAME). ", " . ucwords(strtolower($principal->OTHER_NAMES)) ;
            $client_name = $name;
            $name = $name . "; " . $contract->DESIGNATION;
            $mission = $contract->ACRONYM;
            $arrival = "{$principal->current_arrival->ARRIVAL} (Dip. Id No: {$principal->latest_diplomatic_card->DIP_ID_NO})";
        }else if ($firstIDChar == "3"){
            $agency = \App\Models\Agency::where('HOST_COUNTRY_ID', $vat_data->HOST_COUNTRY_ID)->first();
            $name = $agency->ACRONYM;
            $mission = $name;
            $client_name = $name;
            $arrival = "N/A";
        }

        $date = date('F d, Y', strtotime($vat_data->CREATED_AT));

        $tabData = [
            'ref'               =>  $vat_data->EXEMPTION_FORM_REF_NO,
            'mission'           =>  $mission,
            'date'              =>  $date,
            'nameTitle'         =>  $name,
            'supplierName'      =>  $vat_data->supplier->SUPPLIER_NAME,
            'supplierAddress'   =>  $vat_data->supplier->SUPPLIER_ADDRESS,
            'supplierVAT'       =>  $vat_data->supplier->PIN,
            'goodsDescription'  =>  $goods,
            'pfNo'              =>  $invoice_numbers,
            'vatAmount'         =>  number_format($totalVAT, 2),
            'clientArrival'     =>  $arrival
        ];

        $template = new \App\Helpers\HCSU\PDFTK\Templates\VATExemptionForm($tabData);
        $this->filename = "{$document->form_name} for {$client_name}, Vendor {$vat_data->supplier->SUPPLIER_NAME} on {$date}";
        return $template->data();
	}

	function generateCombinedString($array){
        if(count($array) == 1){
            return $array[0];
        }else{
            $last = array_pop($array);
            $combined = implode(", ", $array) . " & " . $last;
            return $combined;
        }
    }
}