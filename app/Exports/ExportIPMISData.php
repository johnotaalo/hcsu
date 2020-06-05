<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class ExportIPMISData implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
{
    protected $data;

    function __construct($data = null)
	{
		$this->data = $data;
	}

	/**
    * @return \Illuminate\Support\Collection
    */

    public function collection()
    {
        return $this->data;
    }

    public function headings(): array
    {
    	return ['CASE_NO', 'IPMIS_CASE_NO', 'IPMIS_STATUS', 'CLIENT', 'APP_STATUS', 'PROCESS', 'TASK', 'TASK_DATE', 'CREATED_BY'];
    }

    public function headingRow(): int {
        return 1;
    }

     public function registerEvents(): array
    {
    	return [
    		AfterSheet::class 	=>	function(AfterSheet $event) {
    			$event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0);
    		}
    	];
    }
}
