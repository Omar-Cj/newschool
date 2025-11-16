<?php

namespace App\Repositories;

use App\Enums\AttendanceType;
use App\Interfaces\DashboardInterface;
use App\Models\Academic\Classes;
use App\Models\Academic\ClassSetup;
use App\Models\Accounts\Expense;
use App\Models\Accounts\Income;
use App\Models\Attendance\Attendance;
use App\Models\Event;
use App\Models\Fees\FeesCollect;
use App\Models\Role;
use App\Models\Session;
use App\Models\Staff\Staff;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\StudentInfo\Student;

/**
 * DashboardRepository - School-Isolated Dashboard Statistics
 *
 * IMPORTANT: All queries in this repository are automatically filtered by school_id
 * through SchoolScope applied to BaseModel. This ensures complete data isolation
 * between different schools in the multi-school system.
 *
 * School Context Behavior:
 * - School Users (school_id NOT NULL): See ONLY their school's data
 * - System Admin (school_id NULL): See data from ALL schools (no filtering)
 *
 * Models with SchoolScope (extend BaseModel):
 * - SessionClassStudent, ParentGuardian, Staff, Session, Event
 * - Income, Expense, FeesCollect, Attendance, ClassSetup
 *
 * setting('session') is also school-aware through the fixed setting() helper
 */
class DashboardRepository implements DashboardInterface
{
    /**
     * Get dashboard overview statistics for current school context
     *
     * All counts and sums are automatically filtered by:
     * 1. SchoolScope (school_id from authenticated user)
     * 2. Session filter using setting('session') which is school-aware
     *
     * @return array Dashboard statistics scoped to current school
     */
    public function index()
    {
        // All models extend BaseModel → SchoolScope auto-applied
        // School users see only their school's data, System Admin sees all

        // Student count for current school's active session
        $data['student'] = SessionClassStudent::where('session_id', setting('session'))->count();

        // Parent count for current school (SchoolScope filters automatically)
        $data['parent']  = ParentGuardian::count();

        // Teacher count for current school (role_id=5 + SchoolScope filtering)
        $data['teacher'] = Staff::where('role_id', 5)->count();

        // Session count for current school
        $data['session'] = Session::count();

        // Upcoming events for current school's active session
        $data['events']  = Event::where('session_id', setting('session'))
                                ->active()
                                ->where('date', '>=', date('Y-m-d'))
                                ->orderBy('date')
                                ->take(5)
                                ->get();

        // Financial summary for current school's active session
        $data['income']  = Income::where('session_id', setting('session'))->sum('amount');
        $data['expense'] = Expense::where('session_id', setting('session'))->sum('amount');
        $data['balance'] = $data['income'] - $data['expense'];

        return $data;
    }

    public function feesCollectionYearly() {
        $data = [];
        for($i = 1; $i <= 12; $i++) {
            $data[] = FeesCollect::where('session_id', setting('session'))->whereMonth('date', $i)->sum('amount');
        }
        return $data;
    }

    public function revenueYearly() {
        $data['income']  = [];
        $data['expense'] = [];
        $data['revenue'] = [];

        $n = 0;
        for($i = 1; $i <= date('m'); $i++) {
            $data['income'][]  = Income::where('session_id', setting('session'))->whereMonth('date', $i)->sum('amount');
            $data['expense'][] = Expense::where('session_id', setting('session'))->whereMonth('date', $i)->sum('amount');
            $data['revenue'][] = $data['income'][$n] - $data['expense'][$n];
            $n++;
        }
        return $data;
    }

    public function feesCollection() {
        for ($i = 1; $i <=  date('t'); $i++) {
            $data['collection'][] = FeesCollect::where('session_id', setting('session'))->whereMonth('date', date('m'))->whereDay('date', $i)->sum('amount');
            $data['dates'][]      = str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        return response()->json($data, 200);
    }

    public function incomeExpense() {
        for ($i = 1; $i <=  date('t'); $i++) {
            $data['incomes'][]  = Income::where('session_id', setting('session'))->whereMonth('date', date('m'))->whereDay('date', $i)->sum('amount');
            $data['expenses'][] = Expense::where('session_id', setting('session'))->whereMonth('date', date('m'))->whereDay('date', $i)->sum('amount');
            $data['dates'][]    = str_pad($i, 2, '0', STR_PAD_LEFT);
        }
        return response()->json($data, 200);
    }

    public function attendance() {
        $items = ClassSetup::active()->where('session_id', setting('session'))->get();

        $data['classes'] = [];
        $data['present'] = [];
        $data['absent']  = [];

        $data['classes'] = [];
        foreach ($items as $key => $value) {
            $data['classes'][] = $value->class->name;
            $data['present'][] = Attendance::where('session_id', setting('session'))
                                ->where('classes_id', $value->classes_id)
                                ->whereDay('date', date('d'))
                                ->whereIn('attendance', [AttendanceType::PRESENT, AttendanceType::LATE, AttendanceType::HALFDAY])
                                ->count();
            $data['absent'][]  = Attendance::where('session_id', setting('session'))
                                ->where('classes_id', $value->classes_id)
                                ->whereDay('date', date('d'))
                                ->where('attendance', AttendanceType::ABSENT)
                                ->count();
        }
        return $data;
    }

    public function eventsCurrentMonth() {
        $events = Event::where('session_id', setting('session'))->active()->whereMonth('date', date('m'))->orderBy('date')->get();
        $data = [];
        foreach ($events as $key => $value) {
            $data[] = [
                'title' => $value->title,
                'start' => $value->date.'T'.$value->start_time,
                'end'   => $value->date.'T'.$value->end_time,
            ];
        }
        return response()->json($data, 200);
    }

    /**
     * Get student distribution by gender for current session
     */
    public function genderDistribution()
    {
        $currentSessionId = setting('session');

        return SessionClassStudent::with(['student.gender'])
            ->where('session_id', $currentSessionId)
            ->whereHas('student', function($q) {
                $q->where('status', \App\Enums\Status::ACTIVE);
            })
            ->get()
            ->pluck('student')
            ->filter()
            ->groupBy('gender_id')
            ->map(function($students) {
                return [
                    'name' => $students->first()->gender->name ?? 'Unknown',
                    'count' => $students->count(),
                ];
            })
            ->values();
    }

    /**
     * Get student distribution by category for current session
     */
    public function categoryDistribution()
    {
        $currentSessionId = setting('session');

        return SessionClassStudent::with(['student.studentCategory'])
            ->where('session_id', $currentSessionId)
            ->whereHas('student', function($q) {
                $q->where('status', \App\Enums\Status::ACTIVE)
                  ->whereNotNull('student_category_id');
            })
            ->get()
            ->pluck('student')
            ->filter()
            ->groupBy('student_category_id')
            ->map(function($students) {
                return [
                    'name' => $students->first()->studentCategory->name,
                    'count' => $students->count()
                ];
            })
            ->values();
    }

    /**
     * Get student distribution by shift
     */
    public function shiftDistribution()
    {
        return SessionClassStudent::with(['shift', 'student'])
            ->where('session_id', setting('session'))
            ->whereHas('student', function($q) {
                $q->where('status', \App\Enums\Status::ACTIVE);
            })
            ->get()
            ->groupBy('shift_id')
            ->map(function($items) {
                return [
                    'name' => $items->first()->shift->name ?? 'No Shift',
                    'count' => $items->count()
                ];
            })
            ->filter(fn($item) => $item['name'] !== 'No Shift')
            ->values();
    }

    /**
     * Get student distribution by grade for current session
     */
    public function gradeDistribution()
    {
        $currentSessionId = setting('session');

        $students = SessionClassStudent::with('student')
            ->where('session_id', $currentSessionId)
            ->whereHas('student', function($q) {
                $q->where('status', \App\Enums\Status::ACTIVE)
                  ->whereNotNull('grade');
            })
            ->get()
            ->pluck('student')
            ->filter()
            ->groupBy('grade')
            ->map(function($items, $grade) {
                return [
                    'grade' => $grade,
                    'count' => $items->count()
                ];
            })
            ->sortBy(function($item) {
                $gradeOrder = ['KG-1', 'KG-2', 'Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8', 'Form1', 'Form2', 'Form3', 'Form4'];
                $index = array_search($item['grade'], $gradeOrder);
                return $index !== false ? $index : 999;
            })
            ->values();

        return $students;
    }

    /**
     * Get student distribution by transportation area for current session
     */
    public function transportationDistribution()
    {
        $currentSessionId = setting('session');

        $studentsByBus = SessionClassStudent::with('student.bus')
            ->where('session_id', $currentSessionId)
            ->whereHas('student', function($q) {
                $q->where('status', \App\Enums\Status::ACTIVE)
                  ->whereNotNull('bus_id');
            })
            ->get()
            ->pluck('student')
            ->filter(fn($student) => $student && $student->bus)
            ->groupBy('bus_id')
            ->map(function($students, $busId) {
                $bus = $students->first()->bus;
                return [
                    'area_name' => $bus->area_name,
                    'count' => $students->count()
                ];
            })
            ->values();

        return [
            'areas' => $studentsByBus->pluck('area_name')->toArray(),
            'counts' => $studentsByBus->pluck('count')->toArray()
        ];
    }

}
