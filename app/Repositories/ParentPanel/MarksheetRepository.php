<?php

namespace App\Repositories\ParentPanel;

use App\Interfaces\ParentPanel\MarksheetInterface;
use App\Models\Examination\MarksGrade;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\StudentInfo\Student;
use Illuminate\Http\Request;
use App\Models\Examination\MarksRegister;
use Illuminate\Support\Facades\Session;
use App\Models\StudentInfo\ParentGuardian;
use Illuminate\Support\Facades\Auth;

class MarksheetRepository implements MarksheetInterface
{
    public function studentInfo($id) // student id
    {
        try {

            $classSection   = SessionClassStudent::where('session_id', setting('session'))->where('student_id', $id)->latest()->first();
            $request = new Request([
                'class'   => @$classSection->classes_id,
                'section' => @$classSection->section_id,
            ]);
            return $request;

        } catch (\Throwable $th) {
            return false;
        }
    }
    
    public function index()
    {
        try {
            $parent           = ParentGuardian::where('user_id', Auth::user()->id)->first();
            $data['students'] = Student::where('parent_guardian_id', $parent->id)->get();
            $data['student']  = Student::where('id', Session::get('student_id'))->first();

            return $data;
        } catch (\Throwable $th) {
            return false;
        }
    }

    public function search($request)
    {
        try {
            $data = [];

            Session::put('student_id', $request->student);
            $parent   = ParentGuardian::where('user_id', Auth::user()->id)->first();
            $data['students'] = Student::where('parent_guardian_id', $parent->id)->get();

            $student        = Student::where('id', Session::get('student_id'))->first();
            $classSection   = SessionClassStudent::where('session_id', setting('session'))->where('student_id', @$student->id)->latest()->first();

            $searchRequest = new Request([
                'exam_type'   => $request->exam_type,
                'class'       => @$classSection->classes_id,
                'section'     => @$classSection->section_id,
                'session'     => $request->session,
                'term'        => $request->term,
            ]);

            // Call stored procedure with parameters: session_id, term_id, exam_type_id, class_id, section_id, student_id, branch_id
            $examResults = \DB::select("CALL GetStudentExamReport(?, ?, ?, ?, ?, ?, ?)", [
                $searchRequest->session,           // p_session_id
                $searchRequest->term,              // p_term_id
                $searchRequest->exam_type,         // p_exam_type_id
                $searchRequest->class,             // p_class_id
                $searchRequest->section,           // p_section_id
                $student->id,                      // p_student_id
                $student->branch_id ?? null        // p_branch_id (null for non-multi-branch)
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
                if($examResult->grade === 'Absent' ||
                   $examResult->result < examSetting('average_pass_marks')) {
                    $result = ___('examination.Failed');
                }
                $totalMarks += (float) $examResult->result;
            }

            $avgMarks = $subjectCount > 0 ? $totalMarks / $subjectCount : 0;

            // Calculate GPA
            $grades = MarksGrade::where('session_id', setting('session'))->get();
            $gpa = '0.00';
            foreach($grades as $grade) {
                if($grade->percent_from <= $avgMarks && $grade->percent_upto >= $avgMarks) {
                    $gpa = (string) $grade->point;
                    break;
                }
            }

            $data['marks_registers'] = $examResults;  // Backward compatibility
            $data['exam_results']    = $examResults;  // New key
            $data['result']          = $result;
            $data['gpa']             = $gpa;
            $data['avg_marks']       = $avgMarks;
            $data['student']         = $student;

            return $data;
        } catch (\Throwable $th) {
            \Log::error('Stored procedure execution failed: ' . $th->getMessage(), [
                'exception' => $th,
                'parameters' => [
                    'session' => $searchRequest->session,
                    'term' => $searchRequest->term,
                    'exam_type' => $searchRequest->exam_type,
                    'class' => $searchRequest->class,
                    'section' => $searchRequest->section,
                    'student_id' => $student->id,
                    'branch_id' => $student->branch_id ?? null
                ],
                'trace' => $th->getTraceAsString()
            ]);
            return false;
        }
    }
}
