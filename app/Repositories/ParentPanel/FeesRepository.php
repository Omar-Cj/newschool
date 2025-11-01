<?php

namespace App\Repositories\ParentPanel;

use App\Enums\Settings;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\Auth;
use App\Models\Fees\FeesCollect;
use App\Models\StudentInfo\ParentGuardian;
use App\Interfaces\ParentPanel\FeesInterface;

class FeesRepository implements FeesInterface
{
    public function index($request)
    {
        try {
            $parent                 = ParentGuardian::where('user_id', Auth::user()->id)->first();
            $data['students']       = Student::where('parent_guardian_id', $parent->id)->get();
            $data['fees_assigned']  = [];
            $data['children_fees_summary'] = [];

            // Calculate fees summary for all children
            $data['children_fees_summary'] = $this->calculateChildrenFeesSummary($parent, $data['students']);

            if ($request->filled('student_id')) {
                $data['fees_assigned']  = FeesCollect::with(['feeType'])
                                        ->where('student_id', $request->student_id)
                                        ->where('academic_year_id', setting('session'))
                                        ->orderBy('due_date')
                                        ->paginate(Settings::PAGINATE);
            }

            return $data;
        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Calculate fees summary for all children (Services-Based Approach)
     *
     * @param ParentGuardian $parent
     * @param \Illuminate\Support\Collection $students
     * @return array
     */
    private function calculateChildrenFeesSummary($parent, $students)
    {
        $summary = [];
        $academicYearId = setting('session');

        foreach ($students as $student) {
            // Query FeesCollect directly (services-based approach)
            $feesCollects = $student->feesCollects()
                ->where('academic_year_id', $academicYearId)
                ->get();

            // Calculate outstanding amount using FeesCollect's built-in method
            $outstandingAmount = $feesCollects->sum(function($fee) {
                return $fee->getBalanceAmount(); // Outstanding = (amount + fine - discount) - paid
            });

            // Get student's class and section
            $classSection = 'N/A';
            if ($student->session_class_student) {
                $className = $student->session_class_student->class->name ?? '';
                $sectionName = $student->session_class_student->section->name ?? '';
                $classSection = trim("$className - $sectionName", ' -');
            }

            $summary[] = [
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'enrollment_number' => $student->user->admission_no ?? 'N/A',
                'class_section' => $classSection,
                'outstanding_amount' => $outstandingAmount,
            ];
        }

        return $summary;
    }
}
