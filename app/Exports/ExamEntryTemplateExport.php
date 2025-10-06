<?php

namespace App\Exports;

use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\Academic\Subject;
use App\Models\Academic\SubjectAssign;
use App\Models\Academic\SubjectAssignChildren;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class ExamEntryTemplateExport implements FromCollection, WithHeadings, WithStyles, WithColumnWidths, WithEvents
{
    protected $params;
    protected $students;
    protected $subjects;

    public function __construct($params)
    {
        $this->params = $params;

        // Get students through SessionClassStudent junction table
        $sessionClassStudents = SessionClassStudent::query()
            ->where('session_id', $params['session_id'])
            ->where('classes_id', $params['class_id'])
            ->where('section_id', $params['section_id'])
            ->with(['student', 'class', 'section'])
            ->orderBy('roll', 'asc')
            ->get();

        // Extract active students with their session data
        $this->students = $sessionClassStudents->map(function($scs) {
            if ($scs->student && $scs->student->status == \App\Enums\Status::ACTIVE) {
                return (object)[
                    'id' => $scs->student->id,
                    'full_name' => $scs->student->full_name,
                    'grade' => $scs->student->grade ?? '',
                    'class_name' => $scs->class->name ?? '',
                    'section_name' => $scs->section->name ?? '',
                ];
            }
            return null;
        })->filter()->values();

        // Get subjects
        if ($params['subject_id'] === 'all') {
            $subjectAssign = SubjectAssign::where('session_id', $params['session_id'])
                ->where('classes_id', $params['class_id'])
                ->where('section_id', $params['section_id'])
                ->first();

            if ($subjectAssign) {
                $this->subjects = SubjectAssignChildren::where('subject_assign_id', $subjectAssign->id)
                    ->with('subject')
                    ->get()
                    ->pluck('subject');
            } else {
                $this->subjects = collect();
            }
        } else {
            $this->subjects = Subject::where('id', $params['subject_id'])->get();
        }
    }

    public function collection()
    {
        $data = [];

        foreach ($this->students as $index => $student) {
            $row = [
                $student->id,
                $student->full_name,
                $student->grade,
                $student->class_name,
                $student->section_name,
            ];

            // Add empty columns for each subject (marks to be entered)
            foreach ($this->subjects as $subject) {
                $row[] = ''; // Empty for marks entry
            }

            $data[] = $row;
        }

        return collect($data);
    }

    public function headings(): array
    {
        $headings = [
            'Student ID',
            'Student Name',
            'Grade',
            'Class',
            'Section',
        ];

        // Add subject columns
        foreach ($this->subjects as $subject) {
            $headings[] = $subject->name;
        }

        return $headings;
    }

    public function styles(Worksheet $sheet)
    {
        // Style header row
        $sheet->getStyle('A1:' . $sheet->getHighestColumn() . '1')->applyFromArray([
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => Fill::FILL_SOLID,
                'startColor' => ['rgb' => '5764c6'],
            ],
        ]);

        return [];
    }

    public function columnWidths(): array
    {
        $widths = [
            'A' => 12, // Student ID
            'B' => 30, // Student Name
            'C' => 12, // Grade
            'D' => 15, // Class
            'E' => 15, // Section
        ];

        // Add width for subject columns
        $column = 'F';
        foreach ($this->subjects as $subject) {
            $widths[$column] = 15;
            $column++;
        }

        return $widths;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Protect student info columns (A to E) - make them read-only
                $highestRow = $sheet->getHighestRow();
                $sheet->getStyle('A2:E' . $highestRow)->getProtection()
                    ->setLocked(Protection::PROTECTION_PROTECTED);

                // Unlock subject mark columns (F onwards) - make them editable
                $lastColumn = $sheet->getHighestColumn();
                $sheet->getStyle('F2:' . $lastColumn . $highestRow)->getProtection()
                    ->setLocked(Protection::PROTECTION_UNPROTECTED);

                // Apply sheet protection
                $sheet->getProtection()->setSheet(true);
                $sheet->getProtection()->setPassword('examentry2025');

                // Add data validation for marks columns (must be numeric)
                $subjectStartColumn = 'F';
                $subjectEndColumn = $sheet->getHighestColumn();

                $validation = $sheet->getDataValidation('F2:' . $subjectEndColumn . $highestRow);
                $validation->setType(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::TYPE_DECIMAL);
                $validation->setOperator(\PhpOffice\PhpSpreadsheet\Cell\DataValidation::OPERATOR_BETWEEN);
                $validation->setFormula1('0');
                $validation->setFormula2($this->params['total_marks'] ?? '100');
                $validation->setShowErrorMessage(true);
                $validation->setErrorTitle('Invalid Marks');
                $validation->setError('Please enter marks between 0 and ' . ($this->params['total_marks'] ?? '100'));
            },
        ];
    }
}
