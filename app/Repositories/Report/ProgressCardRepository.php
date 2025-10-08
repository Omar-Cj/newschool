<?php

namespace App\Repositories\Report;

use App\Traits\ReturnFormatTrait;
use App\Models\Examination\MarksGrade;
use App\Models\Examination\MarksRegister;
use App\Interfaces\Report\MarksheetInterface;
use App\Interfaces\Report\ProgressCardInterface;
use Illuminate\Support\Facades\DB;

class ProgressCardRepository implements ProgressCardInterface
{
    use ReturnFormatTrait;

    public function search($request)
    {
        // Call stored procedure with session and term parameters
        $results = DB::select("CALL GetStudentGradebook(?, ?, ?, ?, ?)", [
            $request->session,
            $request->term,
            $request->class,
            $request->section,
            $request->student
        ]);

        // Check for error (student not enrolled in specified class/section)
        if (!empty($results) && isset($results[0]->error_message)) {
            return [
                'error' => $results[0]->error_message,
                'subjects' => [],
                'exams' => [],
                'marks_registers' => [],
                'result' => [],
                'gpa' => [],
                'total_marks' => [],
                'avg_marks' => []
            ];
        }

        // Transform results to collection
        $results = collect($results);

        // Return empty if no results
        if ($results->isEmpty()) {
            return [
                'subjects' => [],
                'exams' => [],
                'marks_registers' => [],
                'result' => [],
                'gpa' => [],
                'total_marks' => [],
                'avg_marks' => []
            ];
        }

        // Map stored procedure columns to exam_type_ids (hardcoded in the procedure)
        $examMapping = [
            'mid_term' => 4,
            'monthly_exam_2' => 5,
            'monthly_exam_1' => 6,
            'final_term' => 7
        ];

        // Query exam types from database to get proper names
        $examTypes = \App\Models\Examination\ExamType::whereIn('id', array_values($examMapping))
            ->where('status', 1)
            ->orderByRaw("FIELD(id, 4, 5, 6, 7)")
            ->get();

        // Build subjects array - stored procedure already returns one row per subject (no duplication!)
        $subjects = $results->map(function($item) {
            return (object)[
                'subject_id' => $item->subject_id,
                'subject' => (object)[
                    'id' => $item->subject_id,
                    'name' => $item->subject_name,
                    'code' => ''
                ]
            ];
        })->values();

        // Build exams array from database exam types
        $exams = $examTypes->map(function($examType) {
            return (object)[
                'exam_type_id' => $examType->id,
                'exam_type' => (object)[
                    'id' => $examType->id,
                    'name' => $examType->name
                ]
            ];
        })->values();

        // Helper function to parse exam values: "99/100", "Absent", or "-"
        $parseExamValue = function($examValue) {
            $examValue = trim($examValue ?? '');

            if ($examValue === 'Absent') {
                return ['mark' => 0, 'is_absent' => 1, 'total_marks' => 0];
            } elseif ($examValue === '-' || $examValue === '') {
                return ['mark' => null, 'is_absent' => 0, 'total_marks' => 0];
            } else {
                // Parse "99/100" format
                $parts = explode('/', $examValue);
                return [
                    'mark' => isset($parts[0]) ? (int)$parts[0] : 0,
                    'is_absent' => 0,
                    'total_marks' => isset($parts[1]) ? (int)$parts[1] : 0
                ];
            }
        };

        // Un-pivot the exam columns into marks_registers structure
        $marks_registers = [];
        $result_array = [];
        $gpa_array = [];
        $total_marks_array = [];
        $avg_marks_array = [];

        // For each exam type, create a marks_registers entry
        foreach ($examMapping as $columnName => $examTypeId) {
            $examMarks = [];

            // For each subject, extract marks from the exam column
            foreach ($results as $subjectRow) {
                $parsed = $parseExamValue($subjectRow->$columnName);

                $examMarks[] = (object)[
                    'subject_id' => $subjectRow->subject_id,
                    'subject' => (object)[
                        'id' => $subjectRow->subject_id,
                        'name' => $subjectRow->subject_name,
                        'code' => ''
                    ],
                    'marksRegisterChilds' => collect([(object)[
                        'mark' => $parsed['mark'] ?? 0,
                        'is_absent' => $parsed['is_absent']
                    ]])
                ];
            }

            $marks_registers[] = collect($examMarks);
        }

        // Calculate per-exam statistics across all subjects
        foreach ($examMapping as $columnName => $examTypeId) {
            $examTotalObtained = 0;
            $examTotalPossible = 0;
            $examSubjectCount = 0;
            $examHasFailure = false;
            $passMarks = examSetting('average_pass_marks') ?? 40;

            foreach ($results as $subjectRow) {
                $parsed = $parseExamValue($subjectRow->$columnName);

                if ($parsed['mark'] !== null && !$parsed['is_absent'] && $parsed['total_marks'] > 0) {
                    $examTotalObtained += $parsed['mark'];
                    $examSubjectCount++;

                    // Check if this subject failed (below pass marks threshold)
                    $percentage = ($parsed['mark'] / $parsed['total_marks']) * 100;
                    if ($percentage < $passMarks) {
                        $examHasFailure = true;
                    }
                } elseif ($parsed['is_absent']) {
                    $examHasFailure = true; // Absent = automatic failure
                }
            }

            $examAverage = $examSubjectCount > 0 ? $examTotalObtained / $examSubjectCount : 0;

            $result_array[] = $examHasFailure ? ___('examination.Failed') : ___('examination.Passed');
            $total_marks_array[] = $examTotalObtained;
            $avg_marks_array[] = $examAverage;

            // Calculate GPA using session-specific marks grades
            $grades = MarksGrade::where('session_id', $request->session)->get();
            $gpa = '0.00';
            foreach ($grades as $grade) {
                if ($grade->percent_from <= $examAverage && $grade->percent_upto >= $examAverage) {
                    $gpa = (string)$grade->point;
                    break;
                }
            }
            $gpa_array[] = $gpa;
        }

        // Return data in format compatible with existing template
        return [
            'subjects' => $subjects,
            'exams' => $exams,
            'marks_registers' => $marks_registers,
            'result' => $result_array,
            'gpa' => $gpa_array,
            'total_marks' => $total_marks_array,
            'avg_marks' => $avg_marks_array
        ];
    }
}
