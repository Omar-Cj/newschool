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

}
