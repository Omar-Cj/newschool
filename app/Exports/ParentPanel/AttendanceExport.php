<?php

namespace App\Exports\ParentPanel;

use App\Enums\AttendanceType;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Illuminate\Support\Collection;

/**
 * Parent Panel Attendance Excel Export
 *
 * Supports two modes:
 * 1. All students mode: Creates multiple sheets (one per student)
 * 2. Single student mode: Creates one sheet
 */
class AttendanceExport implements WithMultipleSheets
{
    protected $results;
    protected $request;
    protected $isAllStudents;

    /**
     * Create a new export instance.
     *
     * @param mixed $results Attendance results (array or collection)
     * @param \Illuminate\Http\Request $request Request object with filters
     */
    public function __construct($results, $request)
    {
        $this->results = $results;
        $this->request = $request;

        // Detect if this is "all students" mode
        $this->isAllStudents = is_array($results) && isset($results[0]['student']);
    }

    /**
     * Generate sheets for the export
     *
     * @return array
     */
    public function sheets(): array
    {
        $sheets = [];

        try {
            if ($this->isAllStudents) {
                // Multiple sheets mode - one per student
                foreach ($this->results as $studentData) {
                    $sheets[] = new StudentAttendanceSheet(
                        $studentData['student'],
                        $studentData['records'],
                        $studentData['summary'],
                        $studentData['class_name'] ?? 'N/A',
                        $studentData['section_name'] ?? 'N/A'
                    );
                }
            } else {
                // Single sheet mode
                $student = $this->request->student ?? null;

                // Get student details from session or default
                $studentName = 'Student';
                $className = 'N/A';
                $sectionName = 'N/A';

                if ($student && method_exists($student, 'getAttribute')) {
                    $studentName = $student->getAttribute('first_name') . ' ' . $student->getAttribute('last_name');
                }

                // Calculate summary from results
                $summary = $this->calculateSummary($this->results);

                $sheets[] = new StudentAttendanceSheet(
                    $student,
                    $this->results,
                    $summary,
                    $className,
                    $sectionName
                );
            }
        } catch (\Throwable $th) {
            // Fallback to empty sheet on error
            $sheets[] = new StudentAttendanceSheet(
                null,
                collect([]),
                ['present' => 0, 'late' => 0, 'absent' => 0, 'halfday' => 0],
                'N/A',
                'N/A'
            );
        }

        return $sheets;
    }

    /**
     * Calculate attendance summary from records
     *
     * @param mixed $records
     * @return array
     */
    protected function calculateSummary($records): array
    {
        $summary = [
            'present' => 0,
            'late' => 0,
            'absent' => 0,
            'halfday' => 0
        ];

        // Handle pagination or collection
        $items = $records;
        if (method_exists($records, 'items')) {
            $items = $records->items();
        } elseif (!is_array($items) && !($items instanceof Collection)) {
            $items = collect($items);
        }

        foreach ($items as $record) {
            $attendance = is_object($record) ? $record->attendance : ($record['attendance'] ?? null);

            if (isset($summary[$attendance])) {
                $summary[$attendance]++;
            }
        }

        return $summary;
    }
}

/**
 * Individual student attendance sheet
 */
class StudentAttendanceSheet implements FromCollection, WithHeadings, WithTitle, ShouldAutoSize, WithStyles
{
    protected $student;
    protected $records;
    protected $summary;
    protected $className;
    protected $sectionName;

    /**
     * Create a new sheet instance.
     *
     * @param mixed $student Student model or null
     * @param mixed $records Attendance records
     * @param array $summary Attendance summary counts
     * @param string $className Class name
     * @param string $sectionName Section name
     */
    public function __construct($student, $records, array $summary, string $className, string $sectionName)
    {
        $this->student = $student;
        $this->records = $records;
        $this->summary = $summary;
        $this->className = $className;
        $this->sectionName = $sectionName;
    }

    /**
     * Get the collection of attendance data
     *
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $data = collect();

        try {
            // Get records as collection
            $items = $this->records;
            if (method_exists($this->records, 'items')) {
                $items = $this->records->items();
            } elseif (!($items instanceof Collection)) {
                $items = collect($items);
            }

            // Add attendance records
            foreach ($items as $record) {
                $data->push([
                    'date' => $this->formatDate($record),
                    'attendance' => $this->formatAttendance($record),
                    'note' => $this->getNote($record)
                ]);
            }

            // Add empty row before summary
            $data->push([
                'date' => '',
                'attendance' => '',
                'note' => ''
            ]);

            // Add summary rows
            $data->push([
                'date' => 'Summary',
                'attendance' => '',
                'note' => ''
            ]);

            $data->push([
                'date' => 'Total Present',
                'attendance' => $this->summary['present'] ?? 0,
                'note' => ''
            ]);

            $data->push([
                'date' => 'Total Late',
                'attendance' => $this->summary['late'] ?? 0,
                'note' => ''
            ]);

            $data->push([
                'date' => 'Total Absent',
                'attendance' => $this->summary['absent'] ?? 0,
                'note' => ''
            ]);

            $data->push([
                'date' => 'Total Half Day',
                'attendance' => $this->summary['halfday'] ?? 0,
                'note' => ''
            ]);

            // Calculate total and percentage
            $total = ($this->summary['present'] ?? 0) +
                     ($this->summary['late'] ?? 0) +
                     ($this->summary['absent'] ?? 0) +
                     ($this->summary['halfday'] ?? 0);

            $data->push([
                'date' => 'Total Days',
                'attendance' => $total,
                'note' => ''
            ]);

            if ($total > 0) {
                $presentDays = ($this->summary['present'] ?? 0) +
                               (($this->summary['late'] ?? 0) * 0.5) +
                               (($this->summary['halfday'] ?? 0) * 0.5);
                $percentage = round(($presentDays / $total) * 100, 2);

                $data->push([
                    'date' => 'Attendance Percentage',
                    'attendance' => $percentage . '%',
                    'note' => ''
                ]);
            }

        } catch (\Throwable $th) {
            // Return empty data on error
            $data = collect([
                [
                    'date' => 'Error loading data',
                    'attendance' => '',
                    'note' => $th->getMessage()
                ]
            ]);
        }

        return $data;
    }

    /**
     * Define column headings
     *
     * @return array
     */
    public function headings(): array
    {
        return [
            'Date',
            'Attendance Status',
            'Note'
        ];
    }

    /**
     * Get sheet title
     *
     * @return string
     */
    public function title(): string
    {
        try {
            if (!$this->student) {
                return 'Attendance Report';
            }

            // Get student name
            $firstName = is_object($this->student)
                ? ($this->student->first_name ?? 'Student')
                : 'Student';
            $lastName = is_object($this->student)
                ? ($this->student->last_name ?? '')
                : '';

            $name = trim($firstName . ' ' . $lastName);

            // Truncate if too long (Excel has 31 char limit for sheet names)
            if (strlen($name) > 25) {
                $name = substr($name, 0, 25) . '...';
            }

            return $name;
        } catch (\Throwable $th) {
            return 'Attendance Report';
        }
    }

    /**
     * Apply styles to the worksheet
     *
     * @param Worksheet $sheet
     * @return array
     */
    public function styles(Worksheet $sheet)
    {
        try {
            // Get the last row number
            $lastRow = $sheet->getHighestRow();

            // Style the header row
            return [
                1 => [
                    'font' => ['bold' => true, 'size' => 12],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E0E0E0']
                    ]
                ],
                // Style summary section (last 8 rows typically)
                ($lastRow - 7) . ':' . $lastRow => [
                    'font' => ['bold' => true],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'F5F5F5']
                    ]
                ]
            ];
        } catch (\Throwable $th) {
            return [];
        }
    }

    /**
     * Format date from attendance record
     *
     * @param mixed $record
     * @return string
     */
    protected function formatDate($record): string
    {
        try {
            $date = is_object($record) ? $record->date : ($record['date'] ?? null);

            if (!$date) {
                return 'N/A';
            }

            // Try to parse and format the date
            if ($date instanceof \DateTime || $date instanceof \Carbon\Carbon) {
                return $date->format('M d, Y');
            }

            // Try to create Carbon instance
            try {
                $carbonDate = \Carbon\Carbon::parse($date);
                return $carbonDate->format('M d, Y');
            } catch (\Exception $e) {
                return $date;
            }
        } catch (\Throwable $th) {
            return 'N/A';
        }
    }

    /**
     * Format attendance status
     *
     * @param mixed $record
     * @return string
     */
    protected function formatAttendance($record): string
    {
        try {
            $attendance = is_object($record) ? $record->attendance : ($record['attendance'] ?? null);

            // Map attendance type to readable string
            switch ($attendance) {
                case AttendanceType::PRESENT:
                case 'present':
                case 1:
                    return 'Present';

                case AttendanceType::LATE:
                case 'late':
                case 2:
                    return 'Late';

                case AttendanceType::ABSENT:
                case 'absent':
                case 3:
                    return 'Absent';

                case AttendanceType::HALFDAY:
                case 'halfday':
                case 4:
                    return 'Half Day';

                case AttendanceType::LEAVE:
                case 'leave':
                case 5:
                    return 'Leave';

                default:
                    return 'Unknown';
            }
        } catch (\Throwable $th) {
            return 'N/A';
        }
    }

    /**
     * Get note from attendance record
     *
     * @param mixed $record
     * @return string
     */
    protected function getNote($record): string
    {
        try {
            $note = is_object($record) ? ($record->note ?? '') : ($record['note'] ?? '');
            return $note ?: '-';
        } catch (\Throwable $th) {
            return '-';
        }
    }
}
