<?php

namespace App\Exports\Sheets;

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

class ListPerSupplierSheet implements FromCollection, WithHeadings, WithColumnFormatting, ShouldAutoSize, WithEvents, WithTitle
{
	private $supplier_data;
	private $acronym;
	private $batch;

	public function __construct($data, $acronym, $batch){
		$this->supplier_data = $data;
		$this->acronym = $acronym;
		$this->batch = $batch;
	}

	public function collection()
    {
        return $this->supplier_data;
    }

	public function title(): string {
		return strtoupper($this->acronym);
	}

	public function headingRow(): int {
    	return 1;
    }

    public function columnFormats(): array
    {
        return [
            'G' => NumberFormat::FORMAT_DATE_DDMMYYYY
        ];
    }

	public function headings(): array
    {
        return ['SN No', 'ORGANISATION/MISSION', 'REF NO/CASE NUMBER', 'DATE APPLIED AT MFA', 'DATE APPLIED AT KRA', 'DATE APPROVED', 'VAT/PIN NO', "SUPPLIER'S NAME", 'ACCOUNT NO', 'YEAR'];
    }

    public function registerEvents(): array
    {
    	$highestColumn = get_highest_excel_column(count($this->headings()));
        $totalRows = count($this->supplier_data) + 1;

    	return [
    		AfterSheet::class 	=>	function(AfterSheet $event) use ($highestColumn, $totalRows){
    			$event->sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
    			$event->sheet->getPageSetup()->setFitToWidth(1);
                $event->sheet->getPageSetup()->setFitToHeight(0);
    			$event->sheet->getHeaderFooter()->setOddHeader('&C&H&B' . $this->acronym . ' ANNUAL VAT EXEMPTIONS TO KRA ON ' . \Carbon\Carbon::parse($this->batch->batch_date)->format('d/m/Y'));
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