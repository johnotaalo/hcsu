<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use PhpOffice\PhpSpreadsheet\Shared\Date;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class VATExport implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize
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
        return ['CASE_NO', 'AGENCY', 'PIN_NO', 'SUPPLIER', 'DESCRIPTION', 'PFNO', 'PFDATE', 'AMOUNT'];
    }

    public function headingRow(): int {
        return 1;
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY,
            'H' => NumberFormat::FORMAT_NUMBER_00,
        ];
    }
}
