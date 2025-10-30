<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use Illuminate\Support\Collection;

class StudentTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting, WithEvents
{
    /**
     * Return empty collection for blank template
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        // Return empty collection for a blank template
        // Users will fill this in with their student data
        return new Collection([]);
    }

    /**
     * Define column headings
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'first_name',
            'last_name',
            'parent_mobile',
            'shift',
            'gender',
            'category',
            'parent_name',
            'parent_relation',
            'fee_services',
        ];
    }

    /**
     * Apply styles to the worksheet
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            // Style the header row (row 1)
            1 => [
                'font' => [
                    'bold' => true,
                    'size' => 12,
                    'color' => ['rgb' => 'FFFFFF'],
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '4472C4'], // Blue background
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Set column widths for better readability
     *
     * @return array
     */
    public function columnWidths(): array
    {
        return [
            'A' => 15,  // first_name
            'B' => 15,  // last_name
            'C' => 18,  // parent_mobile
            'D' => 10,  // shift
            'E' => 10,  // gender
            'F' => 12,  // category
            'G' => 20,  // parent_name
            'H' => 18,  // parent_relation
            'I' => 20,  // fee_services
        ];
    }

    /**
     * Force text format on numeric columns to prevent Excel auto-conversion
     *
     * @return array
     */
    public function columnFormats(): array
    {
        return [
            'C' => NumberFormat::FORMAT_TEXT,  // parent_mobile - preserve leading zeros
            'D' => NumberFormat::FORMAT_TEXT,  // shift - keep as string ID
            'E' => NumberFormat::FORMAT_TEXT,  // gender - keep as string ID
            'F' => NumberFormat::FORMAT_TEXT,  // category - keep as string ID
            'I' => NumberFormat::FORMAT_TEXT,  // fee_services - comma-separated IDs
        ];
    }

    /**
     * Register events to protect header row from editing
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Lock header row (row 1) to prevent editing
                $sheet->getStyle('1:1')
                      ->getProtection()
                      ->setLocked(Protection::PROTECTION_PROTECTED);

                // Unlock data rows (rows 2-1000) for user input
                $sheet->getStyle('2:1000')
                      ->getProtection()
                      ->setLocked(Protection::PROTECTION_UNPROTECTED);

                // Protect the worksheet (no password required)
                $protection = $sheet->getProtection();
                $protection->setSheet(true);              // Enable sheet protection
                $protection->setSort(false);              // Disable sorting
                $protection->setInsertRows(false);        // Disable row insertion
                $protection->setFormatCells(false);       // Disable cell formatting changes
            },
        ];
    }
}
