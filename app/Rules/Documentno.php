<?php

namespace App\Rules;

use Illuminate\Contracts\Validation\Rule;

class Documentno implements Rule
{
    /**
     * Create a new rule instance.
     *
     * @return void
     */
    protected $supplier_id;
    protected $supplier_name;
    protected $vat;
    protected $document_no;
    protected $edit;
    protected $caseNo;

    public function __construct($supplier, $edit = false, $caseNo = null)
    {
        $this->supplier_id = $supplier['ID'];
        $this->supplier_name = $supplier['SUPPLIER_NAME'];
        $this->edit = $edit;
        $this->caseNo = $caseNo;
    }

    /**
     * Determine if the validation rule passes.
     *
     * @param  string  $attribute
     * @param  mixed  $value
     * @return bool
     */
    public function passes($attribute, $value)
    {
        $vat = \App\Models\VAT::where('SUPPLIER_ID', $this->supplier_id)->whereHas('invoices', function ($query) use ($value){
            $query->where('DOCUMENT_NO', $value);
        })->first();

        if($vat && !$this->edit){
            $this->vat = $vat;
            $this->document_no = $value;
            return false;
        }else if ($vat && $this->edit){
            if($vat->CASE_NO == $this->caseNo){
                return true;
            }else{
                $this->vat = $vat;
                $this->document_no = $value;
                return false;
            }
        }

        return true;
    }

    /**
     * Get the validation error message.
     *
     * @return string
     */
    public function message()
    {
        return "The Document No {$this->document_no} for {$this->supplier_name} already exists in another case";
    }
}
