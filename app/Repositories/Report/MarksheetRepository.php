<?php

namespace App\Repositories\Report;

use App\Traits\ReturnFormatTrait;
use App\Models\Examination\MarksGrade;
use App\Interfaces\Report\MarksheetInterface;
use Illuminate\Support\Facades\DB;

class MarksheetRepository implements MarksheetInterface
{
    use ReturnFormatTrait;

    /**
     * Search student exam report using stored procedure
     *
     * @param object $request
     * @return array
     */
    public function search($request)
    {
        // Call stored procedure with parameters including session and term
        $examResults = DB::select("CALL GetStudentExamReport(?, ?, ?, ?, ?, ?)", [
            $request->student,
            $request->class,
            $request->section,
            $request->exam_type,
            $request->session,
            $request->term
        ]);

        // Transform results to collection of stdClass objects for consistency
        $examResults = collect($examResults)->map(function($item) {
            return (object) $item;
        });

        // Calculate overall result and GPA from stored procedure results
        $result = ___('examination.Passed');
        $totalMarks = 0;
        $subjectCount = $examResults->count();

        // Check pass/fail status
        foreach($examResults as $examResult) {
            // If student is absent or marks below pass threshold, result is Failed
            if($examResult->is_absent ||
               $examResult->result < examSetting('average_pass_marks')) {
                $result = ___('examination.Failed');
            }
            $totalMarks += (float) $examResult->result;
        }

        // Calculate average marks
        $avgMarks = $subjectCount > 0 ? $totalMarks / $subjectCount : 0;

        // Calculate GPA based on average percentage
        $gpa = $this->calculateGPA($avgMarks);

        // Return data in format compatible with existing views
        $data = [];
        $data['exam_results']    = $examResults;  // New key for stored procedure results
        $data['marks_registers'] = $examResults;  // Backward compatibility key
        $data['result']          = $result;
        $data['gpa']             = $gpa;
        $data['avg_marks']       = $avgMarks;

        return $data;
    }

    /**
     * Calculate GPA based on average marks percentage
     *
     * @param float $avgMarks
     * @return string
     */
    private function calculateGPA($avgMarks)
    {
        $grades = MarksGrade::where('session_id', setting('session'))->get();

        foreach($grades as $grade) {
            if($grade->percent_from <= $avgMarks && $grade->percent_upto >= $avgMarks) {
                return (string) $grade->point;
            }
        }

        return '0.00';
    }
}
