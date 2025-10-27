<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;

class StudentTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithColumnFormatting
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
            'mobile',
            'email',
            'username',
            'date_of_birth',
            'admission_date',
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
            'G' => 18,  // mobile
            'H' => 25,  // email
            'I' => 18,  // username
            'J' => 18,  // date_of_birth
            'K' => 18,  // admission_date
            'L' => 20,  // parent_name
            'M' => 18,  // parent_relation
            'N' => 20,  // fee_services
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
            'G' => NumberFormat::FORMAT_TEXT,  // mobile - preserve leading zeros
            'N' => NumberFormat::FORMAT_TEXT,  // fee_services - comma-separated IDs
        ];
    }
}
