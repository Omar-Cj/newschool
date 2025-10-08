<?php

namespace App\Repositories\StudentPanel;

use App\Interfaces\StudentPanel\MarksheetInterface;
use App\Models\Examination\MarksGrade;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\Examination\MarksRegister;

class MarksheetRepository implements MarksheetInterface
{
    public function index()
    {
        $student = Student::where('user_id', Auth::user()->id)->first();
        $classSection = SessionClassStudent::where('session_id', setting('session'))->where('student_id', $student->id)->latest()->first();

        $request = new Request([
            'class' => @$classSection->classes_id,
            'section' => @$classSection->section_id,
        ]);
        return $request;
    }

    public function search($request)
    {
        try {
            $student = Student::where('user_id', Auth::user()->id)->first();
            $classSection = SessionClassStudent::where('session_id', setting('session'))->where('student_id', $student->id)->latest()->first();

            $searchRequest = new Request([
                'exam_type' => $request->exam_type,
                'class' => @$classSection->classes_id,
                'section' => @$classSection->section_id,
                'session' => $request->session,
                'term' => $request->term,
            ]);

            // Call stored procedure with parameters including session and term
            $examResults = \DB::select("CALL GetStudentExamReport(?, ?, ?, ?, ?, ?)", [
                $student->id,
                $searchRequest->class,
                $searchRequest->section,
                $searchRequest->exam_type,
                $searchRequest->session,
                $searchRequest->term
            ]);

            // Transform results to collection of stdClass objects
            $examResults = collect($examResults)->map(function($item) {
                return (object) $item;
            });

            // Calculate overall result and GPA
            $result = ___('examination.Passed');
            $totalMarks = 0;
            $subjectCount = $examResults->count();

            foreach($examResults as $examResult) {
                if($examResult->is_absent ||
                   $examResult->result < examSetting('average_pass_marks')) {
                    $result = ___('examination.Failed');
                }
                $totalMarks += (float) $examResult->result;
            }

            $avgMarks = $subjectCount > 0 ? $totalMarks / $subjectCount : 0;

            // Calculate GPA
            $grades = MarksGrade::where('session_id', setting('session'))->get();
            $gpa = '0.00';
            foreach ($grades as $grade) {
                if ($grade->percent_from <= $avgMarks && $grade->percent_upto >= $avgMarks) {
                    $gpa = (string) $grade->point;
                    break;
                }
            }

            $data = [];
            $data['marks_registers'] = $examResults;  // Backward compatibility
            $data['exam_results']    = $examResults;  // New key
            $data['result']          = $result;
            $data['gpa']             = $gpa;
            $data['avg_marks']       = $avgMarks;
            $data['student']         = $student;

            return $data;
        } catch (\Throwable $th) {
            return false;
        }
    }
}
