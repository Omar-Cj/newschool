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
 * Payment Collection Export
 *
 * Exports payment collection report to Excel format with styling
 */
class PaymentCollectionExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithTitle,
    ShouldAutoSize,
    WithEvents
{
    private string $dateFrom;
    private string $dateTo;
    private ?int $schoolId;

    /**
     * Constructor
     *
     * @param string $dateFrom Start date filter
     * @param string $dateTo End date filter
     * @param int|null $schoolId School filter (null for all)
     */
    public function __construct(string $dateFrom, string $dateTo, ?int $schoolId = null)
    {
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->schoolId = $schoolId;
    }

    /**
     * Fetch data collection
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $result = DB::select(
            'CALL sp_get_payment_collection_report(?, ?, ?)',
            [$this->dateFrom, $this->dateTo, $this->schoolId]
        );

        return collect($result)->map(function ($row) {
            return [
                'school_name' => $row->school_name,
                'sub_domain' => $row->sub_domain_key,
                'payment_date' => Carbon::parse($row->payment_date)->format('d M Y'),
                'amount' => number_format((float) $row->amount, 2),
                'payment_method' => $row->payment_method,
                'status' => $row->status,
                'approved_by' => $row->approved_by ?? 'N/A',
                'approved_at' => $row->approved_at ? Carbon::parse($row->approved_at)->format('d M Y H:i') : 'N/A',
                'invoice_number' => $row->invoice_number ?? 'N/A',
                'transaction_id' => $row->transaction_id ?? 'N/A',
                'package' => $row->subscription_package ?? 'N/A',
                'subscription_expiry' => $row->subscription_expiry ? Carbon::parse($row->subscription_expiry)->format('d M Y') : 'N/A',
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
            'Payment Date',
            'Amount',
            'Payment Method',
            'Status',
            'Approved By',
            'Approved At',
            'Invoice Number',
            'Transaction ID',
            'Package',
            'Subscription Expiry',
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
            // Style the first row (headings)
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '5669FF'],
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
        return 'Payment Collection Report';
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

                // Add filters to header row
                $sheet->setAutoFilter('A1:L1');

                // Add report metadata
                $lastRow = $sheet->getHighestRow();
                $metaStartRow = $lastRow + 2;

                $sheet->setCellValue("A{$metaStartRow}", 'Report Generated:');
                $sheet->setCellValue("B{$metaStartRow}", Carbon::now()->format('d M Y H:i:s'));

                $sheet->setCellValue("A" . ($metaStartRow + 1), 'Period:');
                $sheet->setCellValue(
                    "B" . ($metaStartRow + 1),
                    Carbon::parse($this->dateFrom)->format('d M Y') . ' to ' .
                    Carbon::parse($this->dateTo)->format('d M Y')
                );

                // Style metadata
                $sheet->getStyle("A{$metaStartRow}:B" . ($metaStartRow + 1))->applyFromArray([
                    'font' => ['italic' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F0F0F0'],
                    ],
                ]);

                // Freeze header row
                $sheet->freezePane('A2');
            },
        ];
    }
}
