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
 * Outstanding Payments Export
 *
 * Exports outstanding payments report with urgency levels
 */
class OutstandingPaymentsExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithEvents
{
    private bool $gracePeriodExceeded;

    /**
     * Constructor
     *
     * @param bool $gracePeriodExceeded Filter for grace period exceeded
     */
    public function __construct(bool $gracePeriodExceeded = false)
    {
        $this->gracePeriodExceeded = $gracePeriodExceeded;
    }

    /**
     * Fetch data collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $result = DB::select(
            'CALL sp_get_outstanding_payments_report(?)',
            [$this->gracePeriodExceeded ? 1 : 0]
        );

        return collect($result)->map(function ($row) {
            return [
                'school_name' => $row->school_name,
                'sub_domain' => $row->sub_domain_key,
                'email' => $row->school_email,
                'phone' => $row->school_phone,
                'package' => $row->package_name,
                'subscription_price' => number_format((float) $row->subscription_price, 2),
                'expiry_date' => Carbon::parse($row->expiry_date)->format('d M Y'),
                'grace_expiry' => Carbon::parse($row->grace_expiry_date)->format('d M Y'),
                'days_overdue' => (int) $row->days_overdue,
                'days_beyond_grace' => (int) $row->days_beyond_grace,
                'urgency_level' => $row->urgency_level,
                'outstanding_amount' => number_format((float) $row->outstanding_amount, 2),
                'last_payment' => $row->last_payment_date ? Carbon::parse($row->last_payment_date)->format('d M Y') : 'Never',
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
            'School Name',
            'Sub Domain',
            'Email',
            'Phone',
            'Package',
            'Subscription Price',
            'Expiry Date',
            'Grace Period Ends',
            'Days Overdue',
            'Days Beyond Grace',
            'Urgency Level',
            'Outstanding Amount',
            'Last Payment Date',
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
                    'startColor' => ['rgb' => 'FF6B6B'],
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
        return 'Outstanding Payments';
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
                $sheet->setAutoFilter('A1:M1');

                // Apply conditional formatting for urgency levels
                $lastRow = $sheet->getHighestRow();

                // Color code urgency levels
                for ($row = 2; $row <= $lastRow; $row++) {
                    $urgencyCell = "K{$row}";
                    $urgencyValue = $sheet->getCell($urgencyCell)->getValue();

                    $color = 'FFFFFF'; // Default white
                    switch ($urgencyValue) {
                        case 'Critical':
                            $color = 'FFE5E5'; // Light red
                            break;
                        case 'In Grace Period':
                            $color = 'FFF4E5'; // Light orange
                            break;
                        case 'Expiring Soon':
                            $color = 'FFF9E5'; // Light yellow
                            break;
                    }

                    $sheet->getStyle("A{$row}:M{$row}")->applyFromArray([
                        'fill' => [
                            'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $color],
                        ],
                    ]);
                }

                // Add summary statistics
                $metaStartRow = $lastRow + 2;

                $sheet->setCellValue("A{$metaStartRow}", 'Report Generated:');
                $sheet->setCellValue("B{$metaStartRow}", Carbon::now()->format('d M Y H:i:s'));

                $sheet->setCellValue("A" . ($metaStartRow + 1), 'Filter:');
                $sheet->setCellValue(
                    "B" . ($metaStartRow + 1),
                    $this->gracePeriodExceeded ? 'Grace Period Exceeded Only' : 'All Outstanding Payments'
                );

                $sheet->setCellValue("A" . ($metaStartRow + 2), 'Total Outstanding:');
                $sheet->setCellValue("B" . ($metaStartRow + 2), "=SUM(L2:L{$lastRow})");

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
