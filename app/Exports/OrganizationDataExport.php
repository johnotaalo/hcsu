<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class OrganizationDataExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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
        return ['CASE_UID', 'CASE_NO', 'CASE_STATUS', 'OWNER_LAST_NAME', 'OWNER_OTHER_NAMES', 'INDEX_NO', 'agency', 'application_by', 'grade', 'designation', 'contract_type', 'residence_no', 'CASE_START_DATE', 'CASE_END_DATE', 'PRO_UID', 'PRO_TITLE'];
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
    			$event->sheet->getHeaderFooter()->setOddHeader('&C&H&BHCSU ORGANIZATION DATA');
    			$event->sheet->getHeaderFooter()->setOddFooter('&CPage &P of &N');
    		}
    	];
    }
}
