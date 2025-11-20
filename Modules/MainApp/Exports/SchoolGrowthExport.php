<?php

declare(strict_types=1);

namespace Modules\MainApp\Exports;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

/**
 * School Growth Export
 *
 * Exports school growth report to Excel format with trends
 */
class SchoolGrowthExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithEvents
{
    private string $dateFrom;
    private string $dateTo;

    /**
     * Constructor
     *
     * @param string $dateFrom Start date filter
     * @param string $dateTo End date filter
     */
    public function __construct(string $dateFrom, string $dateTo)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
    }

    /**
     * Fetch data collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $result = DB::select(
            'CALL sp_get_school_growth_report(?, ?)',
            [$this->dateFrom, $this->dateTo]
        );

        return collect($result)->map(function ($row) {
            $growthIndicator = $row->growth_percentage > 0 ? '↑' : ($row->growth_percentage < 0 ? '↓' : '→');

            return [
                'period' => $row->period_label,
                'new_schools' => (int) $row->new_schools,
                'growth_percentage' => number_format((float) $row->growth_percentage, 2) . '% ' . $growthIndicator,
                'cumulative_schools' => (int) $row->cumulative_schools,
            ];
        });
    }

    /**
     * Define column headings
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Period',
            'New Schools',
            'Growth %',
            'Cumulative Total',
        ];
    }

    /**
     * Apply styles to worksheet
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '00C48C'],
                ],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Set worksheet title
     *
     * @return string
     */
    public function title(): string
    {
        return 'School Growth Report';
    }

    /**
     * Register events for additional formatting
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Add filters
                $sheet->setAutoFilter('A1:D1');

                // Add summary metadata
                $lastRow = $sheet->getHighestRow();
                $metaStartRow = $lastRow + 2;

                $sheet->setCellValue("A{$metaStartRow}", 'Report Generated:');
                $sheet->setCellValue("B{$metaStartRow}", Carbon::now()->format('d M Y H:i:s'));

                $sheet->setCellValue("A" . ($metaStartRow + 1), 'Analysis Period:');
                $sheet->setCellValue(
                    "B" . ($metaStartRow + 1),
                    Carbon::parse($this->dateFrom)->format('d M Y') . ' to ' .
                    Carbon::parse($this->dateTo)->format('d M Y')
                );

                // Calculate and add summary statistics
                $totalNewSchools = $sheet->getCell("B" . $lastRow)->getValue();
                $sheet->setCellValue("A" . ($metaStartRow + 2), 'Total New Schools:');
                $sheet->setCellValue("B" . ($metaStartRow + 2), "=SUM(B2:B{$lastRow})");

                $sheet->getStyle("A{$metaStartRow}:B" . ($metaStartRow + 2))->applyFromArray([
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0'],
                    ],
                ]);

                $sheet->freezePane('A2');
            },
        ];
    }
}
