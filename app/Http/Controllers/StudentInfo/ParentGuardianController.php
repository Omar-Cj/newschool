<?php

namespace App\Http\Controllers\StudentInfo;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Repositories\StudentInfo\ParentGuardianRepository;
use App\Http\Requests\StudentInfo\ParentGuardian\ParentGuardianStoreRequest;
use App\Http\Requests\StudentInfo\ParentGuardian\ParentGuardianUpdateRequest;

class ParentGuardianController extends Controller
{
    private $repo;

    function __construct(ParentGuardianRepository $repo)
    {
        $this->repo       = $repo;
    }

    public function index()
    {
        $data['title']   = ___('student_info.parent_list');
        $data['parents'] = $this->repo->getPaginateAll();
        return view('backend.student-info.parent.index', compact('data'));
    }

    public function search(Request $request)
    {
        $data['title']   = ___('student_info.parent_list');
        $data['request'] = $request;
        $data['parents'] = $this->repo->searchParent($request);
        return view('backend.student-info.parent.index', compact('data'));
    }

    public function create()
    {
        $data['title']              = ___('student_info.parent_create');
        return view('backend.student-info.parent.create', compact('data'));
    }

    public function getParent(Request $request)
    {
        $result = $this->repo->getParent($request);
        return response()->json($result);
    }

    public function store(ParentGuardianStoreRequest $request)
    {
        $result = $this->repo->store($request);
        if($result['status']){
            return redirect()->route('parent.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function edit($id)
    {
        $data['parent']      = $this->repo->show($id);
        $data['title']       = ___('student_info.parent_edit');
        return view('backend.student-info.parent.edit', compact('data'));
    }

    public function update(ParentGuardianUpdateRequest $request, $id)
    {
        $result = $this->repo->update($request, $id);
        if($result){
            return redirect()->route('parent.index')->with('success', $result['message']);
        }
        return back()->with('danger', $result['message']);
    }

    public function delete($id)
    {
        $result = $this->repo->destroy($id);
        if($result['status']):
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

    /**
     * Get children details for a parent guardian
     *
     * @param int $id Parent Guardian ID
     * @return \Illuminate\Http\JsonResponse
     */
    public function getChildrenDetails($id)
    {
        try {
            $parent = $this->repo->show($id);

            if (!$parent) {
                return response()->json([
                    'success' => false,
                    'message' => ___('alert.parent_not_found')
                ], 404);
            }

            // Eager load relationships to prevent N+1
            $students = $parent->children()
                ->with([
                    'session_class_student.class',
                    'session_class_student.section',
                    'feesCollects' => function($query) {
                        $query->where('academic_year_id', activeAcademicYear())
                              ->whereColumn('total_paid', '<', 'amount');
                    }
                ])
                ->get()
                ->map(function($student) {
                    $outstandingFees = $student->feesCollects->sum(function($fee) {
                        return $fee->amount - $fee->total_paid;
                    });

                    return [
                        'id' => $student->id,
                        'name' => $student->full_name,
                        'grade' => $student->grade ?? 'N/A',
                        'class' => $student->session_class_student?->class?->name ?? 'N/A',
                        'section' => $student->session_class_student?->section?->name ?? 'N/A',
                        'outstanding_fees' => (float) $outstandingFees,
                        'formatted_outstanding' => Setting('currency_symbol') . number_format($outstandingFees, 2),
                        'status' => $student->status,
                        'status_label' => $student->status == \App\Enums\Status::ACTIVE ?
                                         ___('common.active') : ___('common.inactive')
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => [
                    'parent_name' => $parent->guardian_name,
                    'total_children' => $students->count(),
                    'students' => $students
                ]
            ]);

        } catch (\Throwable $th) {
            \Log::error('Failed to fetch children details', [
                'parent_id' => $id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => ___('alert.something_went_wrong_please_try_again')
            ], 500);
        }
    }
}
