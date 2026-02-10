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

class AttendanceExport implements FromCollection, WithHeadings, WithMapping, ShouldAutoSize, WithStyles, WithCustomStartCell, WithEvents
{
    protected $attendances;
    protected $date;
    protected $rowNumber = 1;

    public function __construct($attendances, $date)
    {
        $this->attendances = $attendances;
        $this->date = $date;
    }

    public function collection()
    {
        return $this->attendances;
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function map($attendance): array
    {
        // Handle status translation if needed (though controller/view usually handles it)
        // Previous requirement: remove 'late' status, treat as 'present' (Hadir)
        $status = $attendance->status;
        if ($status == 'late' || $status == 'present') {
            $status = 'Hadir';
        } elseif ($status == 'alpha') {
            $status = 'Tidak Hadir';
        } elseif ($status == 'sick') {
            $status = 'Sakit';
        } elseif ($status == 'permission') {
            $status = 'Izin';
        }

        return [
            $this->rowNumber++,
            $attendance->date,
            $attendance->user->name,
            $attendance->user->nik,
            ucfirst($attendance->user->division),
            $attendance->time_in,
            $attendance->time_out,
            ucfirst($status),
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Nama',
            'NIK',
            'Bagian',
            'Jam Masuk',
            'Jam Keluar',
            'Status',
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
                $sheet->setCellValue('A1', 'Data Absensi - Training Center Part Production');
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 14],
                    'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(25);

                // Date
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Tanggal: ' . ($this->date ? \Carbon\Carbon::parse($this->date)->translatedFormat('d F Y') : 'Semua Tanggal'));
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
                
                // Center align specific columns (No, Date, NIK, Time, Status)
                $sheet->getStyle('A4:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B4:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('D4:D' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F4:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('H4:H' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            },
        ];
    }
}
