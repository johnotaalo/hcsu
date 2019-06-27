<?php

namespace App\Helpers\HCSU\Templates;

class VATExemptionForm {

	function referenceTab($reference_number){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "reference-number", 'value' => $reference_number
        ]);
	}

	function missionTab($mission){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "mission", 'value' => $mission
        ]);
	}

	function dateTab($date){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "date", 'value' => $date
        ]);
	}

	function nameTitleTab($nameTitle){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "name-title", 'value' => $nameTitle
        ]);
	}

	function supplierNameTab($supplierName){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "supplier-name", 'value' => $supplierName
        ]);
	}

	function supplierAddressTab($supplierAddress){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "supplier-address", 'value' => $supplierAddress
        ]);
	}

	function supplierPINTab($supplierPIN){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "supplier-pin", 'value' => $supplierPIN
        ]);
	}

	function goodsTab($goods){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "goods", 'value' => $goods
        ]);
	}

	function invoiceNumberTab($invoiceNumber){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "invoice_number", 'value' => $invoiceNumber
        ]);
	}

	function totalVATTab($vat){
		return new \DocuSign\eSign\Model\Text([
            'tab_label' => "totalVAT", 'value' => $vat
        ]);
	}
}