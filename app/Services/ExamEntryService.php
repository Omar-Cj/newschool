<?php

namespace App\Services;

use App\Models\Examination\ExamEntry;
use App\Models\Examination\ExamEntryResult;
use App\Models\Examination\MarksGrade;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\Academic\Subject;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExamEntryTemplateExport;
use App\Imports\ExamEntryImport;

class ExamEntryService
{
    /**
     * Generate Excel template for exam entry
     */
    public function generateExcelTemplate($params)
    {
        $fileName = 'exam_entry_template_' . date('Y-m-d_His') . '.xlsx';

        return Excel::download(
            new ExamEntryTemplateExport($params),
            $fileName
        );
    }

    /**
     * Process Excel upload
     */
    public function processExcelUpload($file, $params)
    {
        try {
            $import = new ExamEntryImport($params);
            Excel::import($import, $file);

            return [
                'status' => true,
                'students' => $import->getStudents(),
                'subjects' => $import->getSubjects(),
                'results' => $import->getResults()
            ];
        } catch (\Exception $e) {
            return [
                'status' => false,
                'message' => $e->getMessage()
            ];
        }
    }

    /**
     * Calculate grade based on obtained marks and total marks
     */
    public function calculateGrade($obtainedMarks, $totalMarks)
    {
        if ($totalMarks == 0) {
            return null;
        }

        $percentage = ($obtainedMarks / $totalMarks) * 100;

        $grade = MarksGrade::where('percent_from', '<=', $percentage)
            ->where('percent_upto', '>=', $percentage)
            ->first();

        if ($grade) {
            return [
                'grade_name' => $grade->grade_name,
                'grade_point' => $grade->point,
                'percentage' => round($percentage, 2)
            ];
        }

        return null;
    }

    /**
     * Auto-calculate and update grades for exam entry results
     */
    public function autoCalculateGrades($examEntryId)
    {
        $examEntry = ExamEntry::findOrFail($examEntryId);
        $results = $examEntry->results;

        foreach ($results as $result) {
            if ($result->obtained_marks !== null && !$result->is_absent) {
                $gradeData = $this->calculateGrade($result->obtained_marks, $examEntry->total_marks);

                if ($gradeData) {
                    $result->update([
                        'grade' => $gradeData['grade_name']
                    ]);
                }
            }
        }

        return ['status' => true, 'message' => 'Grades calculated successfully'];
    }

    /**
     * Get exam entry statistics
     */
    public function getStatistics($examEntryId)
    {
        $examEntry = ExamEntry::with('results')->findOrFail($examEntryId);
        $results = $examEntry->results;

        // Get unique students - fix for multi-subject exams
        $uniqueStudentIds = $results->pluck('student_id')->unique();
        $totalStudents = $uniqueStudentIds->count();

        // Group results by student to determine student-level attendance
        $studentAttendance = $results->groupBy('student_id');

        // Student is present if they have ANY non-absent result
        $presentStudents = $studentAttendance->filter(function($studentResults) {
            return $studentResults->where('is_absent', false)->count() > 0;
        })->count();

        // Students who are absent for ALL subjects
        $absentStudents = $totalStudents - $presentStudents;

        // Calculate pass/fail based on student averages
        $passMarks = $examEntry->total_marks * 0.4; // 40% pass marks
        $passedStudents = 0;

        foreach ($studentAttendance as $studentId => $studentResults) {
            $nonAbsentResults = $studentResults->where('is_absent', false);
            if ($nonAbsentResults->count() > 0) {
                $avgMarks = $nonAbsentResults->avg('obtained_marks');
                if ($avgMarks >= $passMarks) {
                    $passedStudents++;
                }
            }
        }

        $failedStudents = $presentStudents - $passedStudents;

        // Marks statistics (at result level)
        $averageMarks = $results->where('is_absent', false)->avg('obtained_marks');
        $highestMarks = $results->where('is_absent', false)->max('obtained_marks');
        $lowestMarks = $results->where('is_absent', false)->min('obtained_marks');

        return [
            'total_students' => $totalStudents,
            'present_students' => $presentStudents,
            'absent_students' => $absentStudents,
            'passed_students' => $passedStudents,
            'failed_students' => $failedStudents,
            'pass_percentage' => $presentStudents > 0 ? round(($passedStudents / $presentStudents) * 100, 2) : 0,
            'average_marks' => round($averageMarks, 2),
            'highest_marks' => $highestMarks,
            'lowest_marks' => $lowestMarks,
        ];
    }

    /**
     * Validate exam entry data
     */
    public function validateExamEntry($params)
    {
        $errors = [];

        // Check if exam entry already exists
        $exists = ExamEntry::where('session_id', $params['session_id'])
            ->where('term_id', $params['term_id'])
            ->where('class_id', $params['class_id'])
            ->where('section_id', $params['section_id'])
            ->where('exam_type_id', $params['exam_type_id'])
            ->where('subject_id', $params['subject_id'] === 'all' ? null : $params['subject_id'])
            ->exists();

        if ($exists) {
            $errors[] = 'Exam entry already exists for this combination';
        }

        // Check if students exist in the class through SessionClassStudent
        $sessionClassStudents = SessionClassStudent::query()
            ->where('session_id', $params['session_id'])
            ->where('classes_id', $params['class_id'])
            ->where('section_id', $params['section_id'])
            ->with('student')
            ->get();

        // Count only active students
        $studentCount = $sessionClassStudents->filter(function($scs) {
            return $scs->student && $scs->student->status == \App\Enums\Status::ACTIVE;
        })->count();

        if ($studentCount == 0) {
            $errors[] = 'No active students found in the selected class and section';
        }

        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * Bulk store results from array
     */
    public function bulkStoreResults($examEntryId, $resultsArray)
    {
        try {
            foreach ($resultsArray as $result) {
                ExamEntryResult::create([
                    'exam_entry_id' => $examEntryId,
                    'student_id' => $result['student_id'],
                    'subject_id' => $result['subject_id'],
                    'obtained_marks' => $result['obtained_marks'] ?? null,
                    'is_absent' => $result['is_absent'] ?? false,
                    'entry_source' => $result['entry_source'] ?? 'excel',
                    'entered_by' => auth()->id(),
                ]);
            }

            // Auto-calculate grades
            $this->autoCalculateGrades($examEntryId);

            return ['status' => true, 'message' => 'Results saved successfully'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }
}
