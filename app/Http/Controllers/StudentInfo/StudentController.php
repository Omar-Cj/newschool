<?php

namespace App\Http\Controllers\StudentInfo;

use App\Models\Staff\Staff;
use App\Models\LeaveRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Imports\StudentsImport;
use App\Models\StudentInfo\Student;
use App\Http\Controllers\Controller;
use Maatwebsite\Excel\Facades\Excel;
use App\Models\Attendance\Attendance;
use App\Models\Examination\ExamAssign;
use App\Models\Examination\MarksGrade;
use App\Repositories\GenderRepository;
use App\Models\Examination\MarksRegister;
use App\Interfaces\Fees\FeesCollectInterface;
use App\Repositories\Academic\ShiftRepository;
use App\Repositories\Academic\ClassesRepository;
use App\Repositories\Academic\SectionRepository;
use App\Repositories\Academic\ClassSetupRepository;
use App\Repositories\StudentInfo\StudentRepository;
use App\Repositories\Examination\ExamAssignRepository;
use App\Interfaces\StudentInfo\StudentCategoryInterface;
use App\Repositories\StudentInfo\ParentGuardianRepository;
use App\Repositories\StudentInfo\StudentCategoryRepository;
use Maatwebsite\Excel\Exceptions\ImportValidationException;
use App\Http\Requests\StudentInfo\Student\StudentStoreRequest;
use App\Http\Requests\StudentInfo\Student\StudentImportRequest;
use App\Http\Requests\StudentInfo\Student\StudentUpdateRequest;
use App\Services\StudentServiceManager;

class StudentController extends Controller
{
    private $repo;
    private $classRepo;
    private $sectionRepo;
    private $classSetupRepo;
    private $shiftRepo;
    private $genderRepo;
    private $categoryRepo;
    private $examAssignRepo;
    private $parentGuardianRepo;
    private $feesAssignedRepo;
    private $serviceManager;

    function __construct(
        StudentRepository $repo,
        ClassesRepository $classRepo,
        SectionRepository $sectionRepo,
        ClassSetupRepository $classSetupRepo,
        ShiftRepository   $shiftRepo,
        GenderRepository             $genderRepo,
        StudentCategoryRepository    $categoryRepo,
        ExamAssignRepository         $examAssignRepo,
        ParentGuardianRepository     $parentGuardianRepo,
        FeesCollectInterface         $feesAssignedRepo,
        StudentServiceManager        $serviceManager,
    ) {
        $this->repo               = $repo;
        $this->classRepo          = $classRepo;
        $this->sectionRepo        = $sectionRepo;
        $this->classSetupRepo     = $classSetupRepo;
        $this->shiftRepo          = $shiftRepo;
        $this->genderRepo         = $genderRepo;
        $this->categoryRepo       = $categoryRepo;
        $this->examAssignRepo     = $examAssignRepo;
        $this->parentGuardianRepo = $parentGuardianRepo;
        $this->feesAssignedRepo   = $feesAssignedRepo;
        $this->serviceManager     = $serviceManager;
    }

    public function index()
    {
        $data['classes']  = $this->classRepo->assignedAll();
        $data['sections'] = [];
        $data['title']    = ___('student_info.student_list');
        $data['students'] = $this->repo->getPaginateAll();

        // Calculate outstanding amounts for fee display
        $this->calculateOutstandingAmounts($data['students']);

        return view('backend.student-info.student.index', compact('data'));
    }

    public function search(Request $request)
    {
        $data['classes']  = $this->classRepo->assignedAll();
        $data['sections'] = $this->classSetupRepo->getSections($request->class);
        $data['request']  = $request;
        $data['title']    = ___('student_info.student_list');
        $data['students'] = $this->repo->searchStudents($request);

        // Calculate outstanding amounts for fee display
        $this->calculateOutstandingAmounts($data['students']);

        return view('backend.student-info.student.index', compact('data'));
    }

    public function create()
    {
        $data['title']           = ___('student_info.student_create');
        $data['classes']         = $this->classRepo->assignedAll();
        $data['sections']        = [];
        $data['shifts']          = $this->shiftRepo->all();
        $data['genders']         = $this->genderRepo->all();
        $data['categories']      = $this->categoryRepo->all();
        $data['parentGuardians'] = $this->parentGuardianRepo->get();

        // Load only optional fee types for manual selection
        // Mandatory services are automatically assigned based on student's grade level
        $data['fee_types'] = \App\Models\Fees\FeesType::active()
            ->where('is_mandatory_for_level', false)
            ->get();

        return view('backend.student-info.student.create', compact('data'));
    }

    public function addNewDocument(Request $request)
    {
        $counter = $request->counter;
        return view('backend.student-info.student.add-document', compact('counter'))->render();
    }
    public function getStudents(Request $request)
    {
        $examAssign = $this->examAssignRepo->getExamAssign($request);
        // dd($examAssign->mark_distribution);
        $students = $this->repo->getStudents($request);
        return view('backend.student-info.student.students-list', compact('students', 'examAssign'))->render();
    }



    public function store(StudentStoreRequest $request)
    {
        $result = $this->repo->store($request);

        if ($result['status']) {
            // Handle optional service subscriptions if provided
            if ($request->has('selected_services') && !empty($request->selected_services)) {
                try {
                    $student = Student::find($result['student_id'] ?? null);
                    if ($student) {
                        foreach ($request->selected_services as $serviceId) {
                            $feeType = \App\Models\Fees\FeesType::find($serviceId);
                            if ($feeType) {
                                $this->serviceManager->subscribeToService($student, $feeType, [
                                    'academic_year_id' => session('academic_year_id'),
                                    'notes' => 'Selected during registration'
                                ]);
                            }
                        }
                        
                        return redirect()->route('student.index')
                            ->with('success', $result['message'] . ' Optional services have been assigned successfully.');
                    }
                } catch (\Exception $e) {
                    \Log::warning('Failed to assign optional services during registration', [
                        'student_id' => $student->id ?? null,
                        'services' => $request->selected_services,
                        'error' => $e->getMessage()
                    ]);
                    
                    // Don't fail registration if optional services fail
                    return redirect()->route('student.index')
                        ->with('success', $result['message'] . ' Note: Some optional services could not be assigned.');
                }
            }
            
            return redirect()->route('student.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function edit($id)
    {
        $data['title']                 = ___('student_info.student_edit');
        $data['session_class_student'] = $this->repo->getSessionStudent($id);
        $data['student']               = $this->repo->show($data['session_class_student']->student_id);
        $data['classes']               = $this->classRepo->assignedAll();
        $data['sections']              = $this->classSetupRepo->getSections($data['session_class_student']->classes_id);
        $data['shifts']                = $this->shiftRepo->all();
        $data['genders']               = $this->genderRepo->all();
        $data['categories']            = $this->categoryRepo->all();
        $data['parentGuardians']       = $this->parentGuardianRepo->get();
        
        // Load fee types and student services for service management
        $data['fee_types'] = \App\Models\Fees\FeesType::all();
        
        // Load student services with relationships
        $student = $data['student'];
        $student->load(['studentServices.feeType']);

        return view('backend.student-info.student.edit', compact('data'));
    }


    public function show($id)
    {
        $data = $this->repo->show($id);
        
        // Initialize fees array
        $fees = [];
        
        // Enhanced Fee Processing System - Service-based fee calculation
        $academicYearId = session('academic_year_id');
        
        // If no academic year in session, try to get the current active academic year
        if (!$academicYearId) {
            $academicYearId = \App\Models\Session::active()->value('id');
        }
        
        // Check if student has service subscriptions (enhanced system)
        if ($academicYearId && $data->hasActiveServices($academicYearId)) {
            // Use service-based fee system
            $servicesSummary = $data->getServicesSummary($academicYearId);
            $activeServices = $data->activeServices($academicYearId)->get();
            $outstandingFees = $data->getOutstandingFees($academicYearId);
            
            $fees['system_type'] = 'service_based';
            $fees['services'] = $activeServices;
            $fees['services_summary'] = $servicesSummary;
            $fees['outstanding_services'] = $outstandingFees;

            // Calculate totals from actual generated fees (FeesCollect records) not service subscriptions
            $allGeneratedFees = $data->feesPayments()
                ->where('academic_year_id', $academicYearId)
                ->get();

            $fees['total_fees'] = $allGeneratedFees->sum('amount');
            $fees['total_paid'] = $allGeneratedFees->sum('total_paid');
            $fees['total_discounts'] = $allGeneratedFees->sum('discount_applied');

            // Use getBalanceAmount() for consistency with listing page and accurate outstanding calculation
            // Formula: (amount + fine + late_fee - discount_applied) - total_paid
            $fees['fees_due'] = $allGeneratedFees->sum(function($fee) {
                return $fee->getBalanceAmount();
            });

            $fees['fees_payments'] = $data->feesPayments;

            // Get monthly fees grouped by billing period for service-based system
            $monthlyFees = $data->feesPayments()
                ->where('academic_year_id', $academicYearId)
                ->with('feeType')
                ->orderBy('billing_period', 'desc')
                ->orderBy('created_at', 'desc')
                ->get()
                ->groupBy(function($fee) {
                    // Group by billing period, using due date as fallback for legacy fees
                    if ($fee->billing_period) {
                        return $fee->billing_period;
                    }

                    return 'unknown';
                });

            $fees['monthly_fees_by_period'] = $monthlyFees;
            
        } else {
            // Fallback to legacy fee system
            $fees['system_type'] = 'legacy';
            $fees['fees_masters'] = $data->feesMasters;
            $fees['fees_payments'] = $data->feesPayments;
            $fees['fees_discounts'] = $data->feesDiscounts;
            
            // Calculate fees due based on actual fee assignments (avoid duplicates)
            $feesAssigned = $this->feesAssignedRepo->feesAssigned($id);
            $totalFees = 0;
            $totalPaid = 0;
            $totalDiscounts = $data->feesDiscounts->sum('discount_amount');
            
            foreach ($feesAssigned as $assignment) {
                $feeAmount = $assignment->feesMaster->amount ?? 0;
                $totalFees += $feeAmount;
                
                // Only count as paid if payment_method exists
                if ($assignment->feesCollect && $assignment->feesCollect->isPaid()) {
                    $totalPaid += $assignment->feesCollect->amount;
                }
            }
            
            $fees['fees_due'] = $totalFees - ($totalPaid + $totalDiscounts);
            $fees['fees_assigned'] = $this->feesAssignedRepo->feesAssigned($id);
            $fees['total_fees'] = $totalFees;
            $fees['total_paid'] = $totalPaid;
            $fees['total_discounts'] = $totalDiscounts;
        }

        $attendances['total_attendance'] = Attendance::where('student_id', $id)->where('session_id', setting('session'))->get();
        $attendances['total_present'] = $attendances['total_attendance']->where('attendance', 1)->count();
        $attendances['total_absent'] = $attendances['total_attendance']->where('attendance', 2)->count();




        $attendances['avg_present'] = $attendances['total_present'] > 0
            ? ($attendances['total_present'] / count($attendances['total_attendance'])) * 100
            : 0;


        $leave_data['leave_requests'] = LeaveRequest::where('user_id', $data->user_id)->latest()->with(['leaveType:id,name','approvedBy','requestedBy'])->get();
        $leave_data['leave_apprvd'] =  $leave_data['leave_requests']->where('approval_status', 'approved')->count();

        $marks_registers = MarksRegister::select('id', 'exam_type_id', 'subject_id')
            ->where('session_id', setting('session'))
            ->with([
                'exam_type:id,name',
                'subject:id,name,code,type',
                'marksRegisterChilds' => function ($query) use ($id) {
                    $query->where('student_id', $id)->select('id', 'mark', 'title', 'student_id', 'marks_register_id');
                }
            ])
            ->get()
            ->groupBy(function ($item) {
                return $item->exam_type->name ?? 'Unknown';
            });

            $examTypeMarksSum = [];

            foreach ($marks_registers as $examType => $registers) {
                $total = 0;

                foreach ($registers as $register) {
                    $total += $register->marksRegisterChilds->sum('mark');
                }

                $examTypeMarksSum[$examType] = $total;
            }

        $examAssigns = ExamAssign::latest()->with('exam_type:id,name','subject:id,name,code,type','mark_distribution')
                            ->where('classes_id', $data->session_class_student->classes_id)
                            ->where('section_id', $data->session_class_student->section_id)
                            ->where('session_id', setting('session'))
                            ->with('exam_type:id,name') // Ensure relation is loaded if you use it in groupBy
                            ->get()
                            ->groupBy(function ($item) {
                                return $item->exam_type->name ?? 'Unknown';
                            });

        $siblings = Student::with('session_class_student.class')
            ->where('parent_guardian_id', $data->parent_guardian_id)
            ->where('id', '!=', $data->id)
            ->get();

            $attendDaysInMonth = [];
            $date = \Carbon\Carbon::createFromDate(date('Y'), date('m'), 1);
            $endOfMonth = $date->copy()->endOfMonth();


            // Step 1: Create all days in 'dd-mm-yyyy' => [] format
            while ($date->lte($endOfMonth)) {
                    $formattedKey = $date->format('l ') . ($date->day) . $date->format(' F Y'); // 'Monday 26th May 2025'
                    $daysInMonth[$formattedKey] = [];
                    $date->addDay();
                }

            // Step 2: Loop attendance records and overwrite if date exists
            foreach ($attendances['total_attendance'] as $record) {
                $carbonDate = Carbon::parse($record['date']);
                $formattedKey = $carbonDate->format('l ') . ($carbonDate->day) . $carbonDate->format(' F Y');

                if (isset($daysInMonth[$formattedKey])) {
                    $type = match ($record['attendance']) {
                        1 => ___('attendance.Present'),
                        2 => ___('attendance.Late'),
                        3 => ___('attendance.Absent'),
                        4 => ___('attendance.Half'),
                        5 => ___('attendance.Leave'),
                        default => 'Unknown',
                    };
                    $note = $record['note'];

                    $attendDaysInMonth[$formattedKey] = ['type' => $type,'note' => $note];
                }
            }




        return view('backend.student-info.student.show', compact('data', 'siblings', 'fees', 'attendances', 'leave_data', 'marks_registers','examTypeMarksSum','examAssigns','attendDaysInMonth'));
    }


    public function update(StudentUpdateRequest $request)
    {
        $result = $this->repo->update($request, $request->id);

        if ($result['status']) {
            return redirect()->route('student.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function delete($id)
    {

        $result = $this->repo->destroy($id);
        if ($result['status']):
            $success[0] = $result['message'];
            $success[1] = 'success';
            $success[2] = ___('alert.deleted');
            $success[3] = ___('alert.OK');
            return response()->json($success);
        else:
            $success[0] = $result['message'];
            $success[1] = 'error';
            $success[2] = ___('alert.oops');
            return response()->json($success);
        endif;
    }

    public function import()
    {
        $data['title']     = ___('student_info.student_import');
        $data['classes']   = $this->classRepo->assignedAll();
        $data['categories']   = $this->categoryRepo->all();
        $data['sections']  = [];
        return view('backend.student-info.student.import', compact('data'));
    }

    public function importSubmit(StudentImportRequest $request)
    {
        try {
            Excel::import(new StudentsImport($request->class, $request->section), $request->file('file'));
            return redirect()->route('student.index')->with('success', ___('alert.Operation Successful'));
        } catch (ImportValidationException $e) {
            $errors = $e->errors();
            return back()->withErrors($errors)->withInput();
        }
    }

    public function sampleDownload()
    {
        $filePath = public_path('student_bulk_import_sample.xlsx');
        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            return redirect()->back()->with('error', 'File not found!');
        }
    }

    public function getChildren($parentId)
    {
        $data = $this->parentGuardianRepo->getStudentsByParent($parentId);
        return response()->json([
            'status' => 'success',
            'data' => $data
        ]);
    }

    /**
     * Get AJAX data for DataTables server-side processing
     */
    public function ajaxData(Request $request)
    {
        try {
            $result = $this->repo->getAjaxData($request);
            return response()->json($result);
        } catch (\Exception $e) {
            \Log::error('Error in ajaxData: ' . $e->getMessage(), [
                'request' => $request->all(),
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'draw' => intval($request->input('draw')),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => [],
                'error' => 'An error occurred while loading data.'
            ], 500);
        }
    }

    /**
     * Get sections for a specific class via AJAX
     */
    public function ajaxSections($classId)
    {
        try {
            $sections = $this->classSetupRepo->getSections($classId);

            $sectionsData = $sections->map(function($section) {
                return [
                    'id' => $section->section->id,
                    'name' => $section->section->name
                ];
            });

            return response()->json([
                'success' => true,
                'data' => $sectionsData
            ]);
        } catch (\Exception $e) {
            \Log::error('Error in ajaxSections: ' . $e->getMessage(), [
                'class_id' => $classId,
                'error' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to load sections',
                'data' => []
            ], 500);
        }
    }

    /**
     * Calculate outstanding amounts for students for fee display
     * Leverages the existing service-based fee system
     *
     * @param \Illuminate\Pagination\LengthAwarePaginator $students
     */
    private function calculateOutstandingAmounts($students)
    {
        // Get academic year ID - same logic as show() method
        $academicYearId = session('academic_year_id');

        if (!$academicYearId) {
            $academicYearId = \App\Models\Session::active()->value('id');
        }

        // Only proceed if we have an academic year
        if (!$academicYearId) {
            return;
        }

        // Load fee relationships for all students to prevent N+1 queries
        $students->load([
            'student.feesPayments' => function($query) use ($academicYearId) {
                $query->where('academic_year_id', $academicYearId);
            },
            'student.studentServices.feeType'
        ]);

        // Calculate outstanding amount for each student
        foreach ($students as $row) {
            $student = $row->student;

            if (!$student) {
                $row->outstanding_amount = 0;
                continue;
            }

            try {
                // Check if student has active services (same logic as show() method)
                if ($student->hasActiveServices($academicYearId)) {
                    // Use service-based fee system
                    $allGeneratedFees = $student->feesPayments()
                        ->where('academic_year_id', $academicYearId)
                        ->get();

                    // Use the model's getBalanceAmount() method which correctly handles discounts
                    // Formula: (amount + fine + late_fee - discount_applied) - total_paid
                    $outstandingAmount = $allGeneratedFees->sum(function($fee) {
                        return $fee->getBalanceAmount();
                    });

                    $row->outstanding_amount = $outstandingAmount; // Already non-negative from getBalanceAmount()
                } else {
                    // No active services or fallback
                    $row->outstanding_amount = 0;
                }
            } catch (\Exception $e) {
                // Log error but don't break the page
                \Log::warning('Error calculating outstanding amount for student', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage()
                ]);
                $row->outstanding_amount = 0;
            }
        }
    }

}
