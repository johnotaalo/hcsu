<?php

namespace App\Helpers\HCSU\PDFTK\Templates;

class VATExemptionForm{
	private $ref;
	private $mission;
	private $date;
	private $nameTitle;
	private $supplierName;
	private $supplierAddress;
	private $supplierVAT;
	private $clientArrival;
	private $clientDipID;
	private $goodsDescription;
	private $pfNo;
	private $vatAmount;

	function __construct($data){
		foreach ($data as $k => $d) {
			$this->$k = $d;
		}
	}

	function data(){
		return [
			'agency' 			=>	(string) $this->mission,
			'today'				=>	$this->date,
			'name_title'		=>	$this->nameTitle,
			'supplier_name'		=>	$this->supplierName,
			'supplier_address'	=>	$this->supplierAddress,
			'supplier_vat'		=>	$this->supplierVAT,
			'doa_dip_id_no'		=>	$this->clientArrival,
			'goods_desc'		=>	$this->goodsDescription,
			'invoice_no'		=>	$this->pfNo,
			'vat_amt'			=>	$this->vatAmount,
			'serial_no'			=>	$this->ref
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

	// function setRef($value){
	// 	$this->ref = $value;
	// 	return $this;
	// }

	// function setMission($value){
	// 	$this->mission = $value;
	// 	return $this;
	// }

	// function setDate($value){
	// 	$this->data = $value;
	// 	return $this;
	// }

	// function setNameTitle($value){
	// 	$this->nameTitle = $value;
	// 	return $this;
	// }

	// function setSupplierName($value){
	// 	$this->supplierName = $value;
	// 	return $this;
	// }

	// function setSupplierAddress($value){
	// 	$this->supplierAddress = $value;
	// 	return $this;
	// }

	// function setSupplierVAT($value){
	// 	$this->supplierVAT = $value;
	// 	return $this;
	// }

	// function setClientArrival($value){
	// 	$this->clientArrival = $value;
	// 	return $this;
	// }

	// function setGoodsDescription($value){
	// 	$this->goodsDescription = $value;
	// 	return $this;
	// }

	// function setPFNO($value){
	// 	$this->pfNo = $value;
	// 	return $this;
	// }

	// function setVATAmount($value){
	// 	$this->vatAmount = $value;
	// 	return $this;
	// }
}