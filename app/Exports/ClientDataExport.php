<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;

class ClientDataExport implements FromCollection, WithHeadings, WithEvents, ShouldAutoSize
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

    public function registerEvents(): array
    {
        return [
            AfterSheet::class 	=>	function(AfterSheet $event) {
                $event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0);
                $event->sheet->getHeaderFooter()->setOddHeader('&C&H&BCLIENT DATA');
                $event->sheet->getHeaderFooter()->setOddFooter('&CPage &P of &N');
            }
        ];
    }

    public function headings(): array
    {
        return ["HOST_COUNTRY_ID", "CASE_NO", "CLIENT", "APPLICATION_BY", "GRADE", "AGENCY", "CASE_START_DATE", "CASE_END_DATE", "CASE_STATUS", "PRO_TITLE", "PROCESS_CATEGORY", "TASK", "LOCATION"];
    }

    public function headingRow(): int {
        return 1;
    }
}
