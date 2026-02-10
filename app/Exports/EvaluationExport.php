<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;

class EvaluationExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    protected $results;
    protected $rowNumber = 1;

    public function __construct($results)
    {
        $this->results = $results;
    }

    public function collection()
    {
        return $this->results;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function map($result): array
    {
        return [
            $this->rowNumber++,
            $result->user->name ?? '-',
            $result->user->nik ?? '-',
            ucfirst($result->user->division ?? '-'),
            $result->mc_score,
            $result->essay_score,
            $result->score,
            $result->created_at->format('d/m/Y H:i'),
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama User',
            'NIK',
            'Bagian',
            'Nilai PG',
            'Nilai Essay',
            'Total Nilai',
            'Tanggal & Waktu',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            4 => [
                'font' => ['bold' => true],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E0E0E0'],
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;

                // Title
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'Laporan Hasil Evaluasi - Training Center Part Production');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Date Generated
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Tanggal Export: ' . now()->translatedFormat('d F Y'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Borders for data
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A4:H' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Center align specific columns (No, NIK, Bagian, Nilai PG, Nilai Essay, Total, Tanggal)
                $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C4:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
