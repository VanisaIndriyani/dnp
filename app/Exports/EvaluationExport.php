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
    protected $passingGrade;

    public function __construct($results, $passingGrade = 70)
    {
        $this->results = $results;
        $this->passingGrade = $passingGrade;
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
        $date = $result->completed_at ?? $result->created_at;
        // Use snapshot passing_grade if available, otherwise fallback to global setting
        $effectivePassingGrade = $result->passing_grade ?? $this->passingGrade;
        $kelulusan = $result->score >= $effectivePassingGrade ? 'LULUS' : 'TIDAK LULUS';
        $division = ucfirst($result->user->division ?? '-');
        $category = $result->sub_categories ?: '-';

        return [
            $this->rowNumber++,
            $result->user->name ?? '-',
            $result->user->nik ?? '-',
            $division, // Bagian
            $category, // Kategori
            $kelulusan,
            strval($result->mc_score),
            strval($result->essay_score),
            strval($result->score),
            $date ? $date->format('d/m/Y H:i') : '-',
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama User',
            'NIK',
            'Bagian',
            'Kategori',
            'Kelulusan',
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
                $sheet->mergeCells('A1:J1');
                $sheet->setCellValue('A1', 'Laporan Hasil Evaluasi - Training Center Part Production');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Date Generated
                $sheet->mergeCells('A2:J2');
                $sheet->setCellValue('A2', 'Tanggal Export: ' . now()->translatedFormat('d F Y'));
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(2)->setRowHeight(20);

                // Borders for data
                $lastRow = $sheet->getHighestRow();
                $sheet->getStyle('A4:J' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Center align specific columns
                $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('C4:J' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Conditional Formatting for Kelulusan (Column F)
                for ($row = 5; $row <= $lastRow; $row++) {
                    $kelulusan = $sheet->getCell('F' . $row)->getValue();
                    if ($kelulusan == 'LULUS') {
                        $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('008000'); // Green
                        $sheet->getStyle('F' . $row)->getFont()->setBold(true);
                    } else {
                        $sheet->getStyle('F' . $row)->getFont()->getColor()->setARGB('FF0000'); // Red
                        $sheet->getStyle('F' . $row)->getFont()->setBold(true);
                    }
                }
            },
        ];
    }
}
