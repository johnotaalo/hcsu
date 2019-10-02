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

class BlanketVATExport implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents, WithTitle
{
	protected $data;
	protected $batch;

	function __construct($data = null, $batch = null)
	{
		$this->data = $data;
		$this->batch = $batch;
	}

	public function headings(): array
    {
        return ['SN No', 'ORGANISATION/MISSION', 'REF NO/CASE NUMBER', 'DATE APPLIED AT MFA', 'DATE APPLIED AT KRA', 'DATE APPROVED', 'VAT/PIN NO', "SUPPLIER'S NAME", 'ACCOUNT NO'];
    }

    public function headingRow(): int {
    	return 1;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->data;
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

    public function title(): string{
        return 'List - ' . \Carbon\Carbon::parse($this->batch->batch_date)->format('d.m.Y');
    }

    public function registerEvents(): array
    {
    	$highestColumn = get_highest_excel_column(count($this->headings()));
        $totalRows = count($this->data) + 1;

    	return [
    		AfterSheet::class 	=>	function(AfterSheet $event) use ($highestColumn, $totalRows){
    			$event->sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0);
    			$event->sheet->getHeaderFooter()->setOddHeader('&C&H&BBLANKET VAT EXEMPTIONS TO KRA ON ' . \Carbon\Carbon::parse($this->batch->batch_date)->format('d/m/Y'));
    			$event->sheet->getHeaderFooter()->setOddFooter('&CPage &P of &N');

                $event->sheet->getDelegate()->getStyle("A1:{$highestColumn}1")->getFont()->setBold(true);
                $event->sheet->getDelegate()->getStyle("A1:A{$totalRows}")->getAlignment()->setHorizontal('center');
                $event->sheet->getDelegate()->getStyle("B1:B{$totalRows}")->getAlignment()->setHorizontal('left');
                $event->sheet->getDelegate()->getStyle("C1:H{$totalRows}")->getAlignment()->setHorizontal('center');
                // $event->sheet->getDelegate()->getStyle("I1:I{$totalRows}")->getAlignment()->setHorizontal('center');

                $event->sheet->getDelegate()->getStyle("A1:{$highestColumn}{$totalRows}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);
    		}
    	];
    }
}
