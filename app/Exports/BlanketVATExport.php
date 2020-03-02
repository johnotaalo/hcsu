<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use App\Exports\Sheets\ListPerSupplierSheet;

class BlanketVATExport implements WithMultipleSheets
{
    protected $suppliers;
	protected $data;
	protected $batch;

	function __construct($suppliers = null, $data = null, $batch = null)
	{
        $this->suppliers = $suppliers;
		$this->data = $data;
		$this->batch = $batch;
	}

    public function sheets(): array
    {
        $sheets = [];

        // foreach ($this->data as $key => $data) {
        //     $sheets[] = new ListPerSupplierSheet($data, $key);
        // }

        foreach ($this->suppliers as $supplier) {
            $cleanedData = []; 
            $data = $this->data[$supplier];
            foreach ($data as $key => $d) {
                $d['NO'] = $key + 1;
                $cleanedData[] = $d;
            }
            $cleanedData = collect($cleanedData);
           
            $sheets[] = new ListPerSupplierSheet($cleanedData, $supplier, $this->batch);
        }

        return $sheets;
    }
}
