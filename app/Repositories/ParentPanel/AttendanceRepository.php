<?php

namespace App\Repositories\ParentPanel;

use Illuminate\Http\Request;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\Auth;
use App\Models\Attendance\Attendance;
use Illuminate\Support\Facades\Session;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\SessionClassStudent;
use App\Interfaces\ParentPanel\AttendanceInterface;

class AttendanceRepository implements AttendanceInterface
{
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
            Session::put('student_id', $request->student);
            $parent = ParentGuardian::where('user_id', Auth::user()->id)->first();
            $data['students'] = Student::where('parent_guardian_id', $parent->id)->get();
            $data['student'] = Student::where('id', Session::get('student_id'))->first();

            // Check if "all students" mode is selected
            if ($request->student == 'all') {
                $allStudents = Student::where('parent_guardian_id', $parent->id)->get();
                $data['results'] = [];

                foreach ($allStudents as $student) {
                    // Get student's class and section with eager loading
                    $classSection = SessionClassStudent::with(['class', 'section'])
                        ->where('session_id', setting('session'))
                        ->where('student_id', $student->id)
                        ->latest()
                        ->first();

                    // Skip if student has no class assignment
                    if (!$classSection) {
                        continue;
                    }

                    // Build attendance query
                    $result = Attendance::query()
                        ->where('session_id', setting('session'))
                        ->where('classes_id', $classSection->classes_id)
                        ->where('section_id', $classSection->section_id)
                        ->where('student_id', $student->id);

                    // Apply month filter
                    if ($request->month != "") {
                        $result = $result->where('date', 'LIKE', $request->month . '%');
                    }

                    // Apply date filter
                    if ($request->date != "") {
                        $result = $result->where('date', $request->date);
                    }

                    // Get records based on view mode
                    if ($request->view == 0) {
                        $attendanceRecords = $result->get();
                    } else {
                        $attendanceRecords = $result->paginate(10);
                    }

                    // Calculate attendance summary
                    $summary = [
                        'present' => 0,
                        'late' => 0,
                        'absent' => 0,
                        'halfday' => 0
                    ];

                    $recordsCollection = $request->view == 0 ? $attendanceRecords : $attendanceRecords->items();
                    foreach ($recordsCollection as $record) {
                        if (isset($summary[$record->attendance])) {
                            $summary[$record->attendance]++;
                        }
                    }

                    // Add student data to results
                    $data['results'][] = [
                        'student' => $student,
                        'class_name' => $classSection->class->name ?? 'N/A',
                        'section_name' => $classSection->section->name ?? 'N/A',
                        'records' => $attendanceRecords,
                        'summary' => $summary
                    ];
                }
            } else {
                // Single student mode (existing logic)
                $student = Student::where('id', Session::get('student_id'))->first();
                $classSection = SessionClassStudent::with(['class', 'section'])
                    ->where('session_id', setting('session'))
                    ->where('student_id', $student->id)
                    ->latest()
                    ->first();

                $result = Attendance::query()
                    ->where('session_id', setting('session'))
                    ->where('classes_id', $classSection->classes_id)
                    ->where('section_id', $classSection->section_id)
                    ->where('student_id', $student->id);

                if ($request->month != "") {
                    $result = $result->where('date', 'LIKE', $request->month . '%');
                }

                if ($request->date != "") {
                    $result = $result->where('date', $request->date);
                }

                if ($request->view == 0) {
                    $data['results'] = $result->get();
                } else {
                    $data['results'] = $result->paginate(10);
                }
            }

            return $data;

        } catch (\Throwable $th) {
            return false;
        }
    }
}
