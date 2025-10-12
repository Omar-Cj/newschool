<?php

declare(strict_types=1);

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Illuminate\Support\Collection;

/**
 * Dynamic Report Excel Export with advanced formatting
 * Supports multiple data types with proper cell formatting
 */
class DynamicReportExport implements
    FromCollection,
    WithHeadings,
    WithStyles,
    WithColumnWidths,
    WithEvents,
    WithTitle
{
    protected array $results;
    protected array $columns;
    protected array $metadata;

    /**
     * Constructor
     *
     * @param array $results Query results array
     * @param array $columns Column definitions with types and labels
     * @param array $metadata Report metadata (name, parameters, etc.)
     */
    public function __construct(array $results, array $columns, array $metadata = [])
    {
        $this->results = $results;
        $this->columns = $columns;
        $this->metadata = $metadata;
    }

    /**
     * Return collection of data rows
     *
     * @return Collection
     */
    public function collection(): Collection
    {
        return collect($this->results)->map(function ($row) {
            $formattedRow = [];

            foreach ($this->columns as $column) {
                $key = $column['key'];
                $value = $row[$key] ?? null;

                // Format based on column type
                $formattedRow[] = $this->formatCellValue($value, $column);
            }

            return $formattedRow;
        });
    }

    /**
     * Define column headings
     *
     * @return array
     */
    public function headings(): array
    {
        return array_column($this->columns, 'label');
    }

    /**
     * Apply styles to worksheet
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet): array
    {
        $highestRow = $sheet->getHighestRow();
        $highestColumn = $sheet->getHighestColumn();

        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '5764c6'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => '000000'],
                    ],
                ],
            ],

            // Data rows styling
            "A2:{$highestColumn}{$highestRow}" => [
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => Border::BORDER_THIN,
                        'color' => ['rgb' => 'CCCCCC'],
                    ],
                ],
                'alignment' => [
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Define column widths for better readability
     *
     * @return array
     */
    public function columnWidths(): array
    {
        $widths = [];
        $columnLetter = 'A';

        foreach ($this->columns as $column) {
            // Calculate width based on column type and label length
            $width = $this->calculateColumnWidth($column);
            $widths[$columnLetter] = $width;
            $columnLetter++;
        }

        return $widths;
    }

    /**
     * Register events for advanced formatting
     *
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $highestRow = $sheet->getHighestRow();

                // Apply number formatting to specific column types
                $columnLetter = 'A';
                foreach ($this->columns as $column) {
                    $range = "{$columnLetter}2:{$columnLetter}{$highestRow}";

                    $this->applyNumberFormatting($sheet, $range, $column);

                    // Apply text alignment based on type
                    $alignment = $this->getAlignmentForType($column['type'] ?? 'string');
                    $sheet->getStyle($range)->getAlignment()->setHorizontal($alignment);

                    $columnLetter++;
                }

                // Add metadata header rows if available
                if (!empty($this->metadata)) {
                    $this->addMetadataRows($sheet);
                }

                // Auto-filter for headers
                $sheet->setAutoFilter("A1:{$sheet->getHighestColumn()}1");

                // Freeze header row
                $sheet->freezePane('A2');

                // Set print settings
                $sheet->getPageSetup()
                    ->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE)
                    ->setPaperSize(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::PAPERSIZE_A4)
                    ->setFitToWidth(1)
                    ->setFitToHeight(0);

                // Set print margins
                $sheet->getPageMargins()
                    ->setTop(0.75)
                    ->setRight(0.25)
                    ->setLeft(0.25)
                    ->setBottom(0.75);
            },
        ];
    }

    /**
     * Set worksheet title
     *
     * @return string
     */
    public function title(): string
    {
        $title = $this->metadata['name'] ?? 'Report';

        // Limit to 31 characters (Excel limit)
        return substr($title, 0, 31);
    }

    /**
     * Format cell value based on column type
     *
     * @param mixed $value Raw value
     * @param array $column Column metadata
     * @return mixed Formatted value
     */
    protected function formatCellValue($value, array $column)
    {
        if ($value === null) {
            return '';
        }

        $type = $column['type'] ?? 'string';

        return match($type) {
            'currency', 'number', 'percentage' => is_numeric($value) ? (float) $value : $value,
            'date', 'datetime' => $this->parseDateValue($value),
            'boolean' => $value ? 'Yes' : 'No',
            default => (string) $value,
        };
    }

    /**
     * Parse date value for Excel
     *
     * @param mixed $value Date value
     * @return mixed
     */
    protected function parseDateValue($value)
    {
        if (empty($value)) {
            return '';
        }

        try {
            $date = \Carbon\Carbon::parse($value);
            return \PhpOffice\PhpSpreadsheet\Shared\Date::PHPToExcel($date);
        } catch (\Exception $e) {
            return (string) $value;
        }
    }

    /**
     * Apply number formatting to column range
     *
     * @param Worksheet $sheet Worksheet instance
     * @param string $range Cell range
     * @param array $column Column metadata
     * @return void
     */
    protected function applyNumberFormatting(Worksheet $sheet, string $range, array $column): void
    {
        $type = $column['type'] ?? 'string';

        $format = match($type) {
            'currency' => '_($* #,##0.00_);_($* (#,##0.00);_($* "-"??_);_(@_)',
            'number' => '#,##0.00',
            'percentage' => '0.0"%"',
            'date' => 'yyyy-mm-dd',
            'datetime' => 'yyyy-mm-dd hh:mm:ss',
            default => NumberFormat::FORMAT_TEXT,
        };

        $sheet->getStyle($range)->getNumberFormat()->setFormatCode($format);
    }

    /**
     * Get text alignment for column type
     *
     * @param string $type Column type
     * @return string Alignment constant
     */
    protected function getAlignmentForType(string $type): string
    {
        return match($type) {
            'currency', 'number', 'percentage' => Alignment::HORIZONTAL_RIGHT,
            'date', 'datetime' => Alignment::HORIZONTAL_CENTER,
            'boolean' => Alignment::HORIZONTAL_CENTER,
            default => Alignment::HORIZONTAL_LEFT,
        };
    }

    /**
     * Calculate optimal column width
     *
     * @param array $column Column metadata
     * @return int Column width
     */
    protected function calculateColumnWidth(array $column): int
    {
        $type = $column['type'] ?? 'string';
        $labelLength = strlen($column['label'] ?? '');

        // Base width on type and label length
        $width = match($type) {
            'currency' => max(15, $labelLength + 2),
            'number' => max(12, $labelLength + 2),
            'percentage' => max(10, $labelLength + 2),
            'date' => max(12, $labelLength + 2),
            'datetime' => max(18, $labelLength + 2),
            'boolean' => max(8, $labelLength + 2),
            default => max(20, min(50, $labelLength + 5)),
        };

        return $width;
    }

    /**
     * Add metadata rows to top of sheet
     *
     * @param Worksheet $sheet Worksheet instance
     * @return void
     */
    protected function addMetadataRows(Worksheet $sheet): void
    {
        // Insert rows at top for metadata
        $metadataRowCount = 0;

        if (!empty($this->metadata['name'])) {
            $metadataRowCount++;
        }

        if (!empty($this->metadata['parameters'])) {
            $metadataRowCount += count($this->metadata['parameters']);
        }

        if (!empty($this->metadata['generated_at'])) {
            $metadataRowCount++;
        }

        if ($metadataRowCount > 0) {
            $sheet->insertNewRowBefore(1, $metadataRowCount + 1);

            $currentRow = 1;

            // Report title
            if (!empty($this->metadata['name'])) {
                $sheet->setCellValue("A{$currentRow}", $this->metadata['name']);
                $sheet->mergeCells("A{$currentRow}:{$sheet->getHighestColumn()}{$currentRow}");
                $sheet->getStyle("A{$currentRow}")->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '333333'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                $currentRow++;
            }

            // Parameters used
            if (!empty($this->metadata['parameters'])) {
                foreach ($this->metadata['parameters'] as $paramName => $paramValue) {
                    $sheet->setCellValue("A{$currentRow}", ucfirst(str_replace('_', ' ', $paramName)) . ':');
                    $sheet->setCellValue("B{$currentRow}", $paramValue);
                    $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
                    $currentRow++;
                }
            }

            // Generated timestamp
            if (!empty($this->metadata['generated_at'])) {
                $sheet->setCellValue("A{$currentRow}", 'Generated:');
                $sheet->setCellValue("B{$currentRow}", $this->metadata['generated_at']);
                $sheet->getStyle("A{$currentRow}")->getFont()->setBold(true);
                $currentRow++;
            }

            // Add blank row before data
            $currentRow++;
        }
    }
}
