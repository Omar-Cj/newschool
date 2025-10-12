<?php

namespace App\Repositories\Report;

use Illuminate\Support\Facades\DB;

class StudentReportRepository
{

    /**
     * Get student list report by calling stored procedure
     *
     * @param object $request
     * @return array
     */
    public function getStudentList($request)
    {
        // Call stored procedure GetStudentListReport
        // Use filled() to ensure empty strings are converted to NULL for "all records" filtering
        $results = DB::select("CALL GetStudentListReport(?, ?, ?, ?, ?, ?, ?, ?)", [
            $request->filled('session') ? $request->session : null,
            $request->filled('grade') ? $request->grade : null,
            $request->filled('class') ? $request->class : null,
            $request->filled('section') ? $request->section : null,
            $request->filled('shift') ? $request->shift : null,
            $request->filled('category') ? $request->category : null,
            $request->filled('status') ? $request->status : null,
            $request->filled('gender') ? $request->gender : null
        ]);

        // Transform results to collection and extract only needed fields
        $studentList = collect($results)->map(function ($item) {
            return (object) [
                'full_name' => $item->full_name ?? '',
                'mobile' => $item->mobile ?? '',
                'grade' => $item->grade ?? '',
                'class' => $item->class_name ?? '',
                'section' => $item->section_name ?? '',
                'guardian_name' => $item->guardian_name ?? ''
            ];
        });

        return [
            'students' => $studentList,
            'total_count' => $studentList->count()
        ];
    }

    /**
     * Get student registration report by calling stored procedure
     *
     * @param object $request
     * @return array
     */
    public function getStudentRegistration($request)
    {
        // Call stored procedure GetStudentRegistrationReport
        // Use filled() to ensure empty strings are converted to NULL for "all records" filtering
        $results = DB::select("CALL GetStudentRegistrationReport(?, ?, ?, ?, ?, ?, ?, ?)", [
            $request->filled('start_date') ? $request->start_date : null,
            $request->filled('end_date') ? $request->end_date : null,
            $request->filled('grade') ? $request->grade : null,
            $request->filled('class') ? $request->class : null,
            $request->filled('section') ? $request->section : null,
            $request->filled('shift') ? $request->shift : null,
            $request->filled('status') ? $request->status : null,
            $request->filled('gender') ? $request->gender : null
        ]);

        // Transform results to collection and extract only needed fields
        $studentRegistration = collect($results)->map(function ($item) {
            return (object) [
                'admission_date' => $item->admission_date ?? '',
                'full_name' => $item->full_name ?? '',
                'mobile' => $item->mobile ?? '',
                'grade' => $item->grade ?? '',
                'class_name' => $item->class_name ?? '',
                'section_name' => $item->section_name ?? '',
                'shift_name' => $item->shift_name ?? ''
            ];
        });

        return [
            'students' => $studentRegistration,
            'total_count' => $studentRegistration->count()
        ];
    }

    /**
     * Get guardian list report by calling stored procedure
     *
     * @return array
     */
    public function getGuardianList()
    {
        // Call parameter-less stored procedure GetGuardianListReport
        $results = DB::select("CALL GetGuardianListReport()");

        // Transform results to collection and extract all 5 columns with null safety
        $guardianList = collect($results)->map(function ($item) {
            return (object) [
                'guardian_name' => $item->guardian_name ?? '',
                'guardian_mobile' => $item->guardian_mobile ?? '',
                'guardian_address' => $item->guardian_address ?? '',
                'total_students' => $item->total_students ?? 0,
                'relation_type' => $item->relation_type ?? ''
            ];
        });

        return [
            'guardians' => $guardianList,
            'total_count' => $guardianList->count()
        ];
    }
}
