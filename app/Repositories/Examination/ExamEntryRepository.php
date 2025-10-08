<?php

namespace App\Repositories\Examination;

use App\Models\Examination\ExamEntry;
use App\Models\Examination\ExamEntryResult;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\Academic\Subject;
use App\Models\Academic\SubjectAssign;
use App\Models\Academic\SubjectAssignChildren;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ExamEntryRepository
{
    /**
     * Get all exam entries for DataTables with filters
     */
    public function getAjaxData($request)
    {
        // DataTables parameters
        $draw = intval($request->input('draw'));
        $start = intval($request->input('start'));
        $length = intval($request->input('length'));
        $searchValue = $request->input('search.value');
        $orderColumn = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'desc');

        // Base query with relationships
        $query = ExamEntry::with(['session', 'term', 'class', 'section', 'examType', 'subject', 'creator']);

        // Apply filters
        if ($request->filled('session_id')) {
            $query->where('session_id', $request->session_id);
        }

        if ($request->filled('term_id')) {
            $query->where('term_id', $request->term_id);
        }

        if ($request->filled('class_id')) {
            $query->where('class_id', $request->class_id);
        }

        if ($request->filled('section_id')) {
            $query->where('section_id', $request->section_id);
        }

        if ($request->filled('exam_type_id')) {
            $query->where('exam_type_id', $request->exam_type_id);
        }

        if ($request->filled('grade')) {
            $query->where('grade', $request->grade);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Count total records
        $totalRecords = ExamEntry::count();

        // Apply search filter
        if (!empty($searchValue)) {
            $query->where(function($q) use ($searchValue) {
                $q->whereHas('class', function($sq) use ($searchValue) {
                    $sq->where('name', 'LIKE', "%{$searchValue}%");
                })
                ->orWhereHas('section', function($sq) use ($searchValue) {
                    $sq->where('name', 'LIKE', "%{$searchValue}%");
                })
                ->orWhereHas('examType', function($sq) use ($searchValue) {
                    $sq->where('name', 'LIKE', "%{$searchValue}%");
                })
                ->orWhere('grade', 'LIKE', "%{$searchValue}%");
            });
        }

        // Count filtered records
        $filteredRecords = $query->count();

        // Apply ordering
        $columns = ['id', 'session_id', 'term_id', 'class_id', 'exam_type_id', 'status', 'created_at'];
        if (isset($columns[$orderColumn])) {
            $query->orderBy($columns[$orderColumn], $orderDir);
        } else {
            $query->orderBy('created_at', 'desc');
        }

        // Apply pagination
        $examEntries = $query->offset($start)->limit($length)->get();

        // Format data for DataTables
        $data = [];
        $key = $start + 1;

        foreach ($examEntries as $row) {
            // Status badge
            $badgeClass = [
                'draft' => 'warning',
                'completed' => 'info',
                'published' => 'success'
            ][$row->status] ?? 'secondary';

            $statusBadge = '<span class="badge bg-'.$badgeClass.'">'.ucfirst($row->status).'</span>';

            // Entry method badge
            $methodBadge = $row->entry_method === 'manual'
                ? '<span class="badge bg-primary"><i class="fas fa-keyboard"></i> Manual</span>'
                : '<span class="badge bg-success"><i class="fas fa-file-excel"></i> Excel</span>';

            // Subject info
            $subjectInfo = $row->is_all_subjects
                ? '<span class="text-primary">All Subjects</span>'
                : ($row->subject ? $row->subject->name : '-');

            // Results count
            $resultsCount = $row->results()->count();

            // Actions
            $action = '<div class="btn-group" role="group">';

            // View button
            $action .= '<a href="'.route('exam-entry.show', $row->id).'" class="btn btn-sm btn-info" title="View">
                        <i class="fas fa-eye"></i></a>';

            // Edit button (only for draft/completed)
            if (in_array($row->status, ['draft', 'completed'])) {
                $action .= '<a href="'.route('exam-entry.edit', $row->id).'" class="btn btn-sm btn-primary" title="Edit">
                            <i class="fas fa-edit"></i></a>';
            }

            // Delete button - always shown, validation happens on click
            $examTypeName = $row->examType->name ?? 'Unknown';
            $className = $row->class->name ?? 'Unknown';

            // Publish button (only for completed)
            if ($row->status === 'completed') {
                $subjectName = $row->is_all_subjects ? 'All Subjects' : ($row->subject->name ?? 'Unknown');
                $action .= '<button type="button" class="btn btn-sm btn-success publish-entry"
                            data-id="'.$row->id.'"
                            data-exam-type="'.htmlspecialchars($examTypeName, ENT_QUOTES).'"
                            data-class="'.htmlspecialchars($className, ENT_QUOTES).'"
                            data-subject="'.htmlspecialchars($subjectName, ENT_QUOTES).'"
                            data-results-count="'.$resultsCount.'"
                            title="Publish">
                            <i class="fas fa-paper-plane"></i></button>';
            }
            $action .= '<button type="button" class="btn btn-sm btn-danger delete-entry"
                        data-id="'.$row->id.'"
                        data-exam-type="'.htmlspecialchars($examTypeName, ENT_QUOTES).'"
                        data-class="'.htmlspecialchars($className, ENT_QUOTES).'"
                        data-results-count="'.$resultsCount.'"
                        title="Delete">
                        <i class="fas fa-trash"></i></button>';

            $action .= '</div>';

            $data[] = [
                'DT_RowIndex' => $key++,
                'session_name' => $row->session->name ?? '-',
                'term_name' => $row->term->termDefinition->name ?? '-',
                'class_name' => $row->class->name ?? '-',
                'section_name' => $row->section->name ?? '-',
                'exam_type_name' => $row->examType->name ?? '-',
                'subject_info' => $subjectInfo,
                'entry_method' => $methodBadge,
                'results_count' => $resultsCount,
                'status_badge' => $statusBadge,
                'created_at' => $row->created_at->format('d M, Y'),
                'action' => $action
            ];
        }

        return [
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ];
    }

    /**
     * Get students with subjects for exam entry
     */
    public function getStudentsWithSubjects($params)
    {
        // Query students through SessionClassStudent junction table
        $sessionClassStudents = SessionClassStudent::query()
            ->where('session_id', $params['session_id'])
            ->where('classes_id', $params['class_id'])
            ->where('section_id', $params['section_id'])
            ->with(['student', 'class', 'section'])
            ->orderBy('roll', 'asc')
            ->get();

        // Extract active Student model instances with junction table data
        $students = $sessionClassStudents->map(function($scs) {
            if ($scs->student && $scs->student->status == \App\Enums\Status::ACTIVE) {
                $student = $scs->student;
                // Add roll from junction table as dynamic attribute for display
                $student->setAttribute('roll', $scs->roll);
                // Add class and section names from junction table relationships
                $student->setAttribute('class_name', $scs->class->name ?? '-');
                $student->setAttribute('section_name', $scs->section->name ?? '-');
                return $student;
            }
            return null;
        })
        ->filter()
        ->values(); // Re-index the collection

        if ($params['subject_id'] === 'all') {
            // Get all subjects assigned to this class and section
            $subjectAssign = SubjectAssign::where('session_id', $params['session_id'])
                ->where('classes_id', $params['class_id'])
                ->where('section_id', $params['section_id'])
                ->first();

            if ($subjectAssign) {
                $subjects = SubjectAssignChildren::where('subject_assign_id', $subjectAssign->id)
                    ->with('subject')
                    ->get()
                    ->map(function($item) use ($params) {
                        return [
                            'id' => $item->subject->id,
                            'name' => $item->subject->name,
                            'total_marks' => $params['total_marks'] ?? 100
                        ];
                    });
            } else {
                $subjects = collect();
            }
        } else {
            // Single subject
            $subject = Subject::find($params['subject_id']);
            $subjects = collect([
                [
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'total_marks' => $params['total_marks'] ?? 100
                ]
            ]);
        }

        return [
            'students' => $students,
            'subjects' => $subjects
        ];
    }

    /**
     * Store exam entry with results
     */
    public function store($request)
    {
        DB::beginTransaction();
        try {
            // Create exam entry
            $examEntry = ExamEntry::create([
                'session_id' => $request->session_id,
                'term_id' => $request->term_id,
                'grade_id' => $request->grade_id,
                'class_id' => $request->class_id,
                'section_id' => $request->section_id,
                'exam_type_id' => $request->exam_type_id,
                'subject_id' => $request->subject_id === 'all' ? null : $request->subject_id,
                'is_all_subjects' => $request->subject_id === 'all',
                'entry_method' => $request->entry_method ?? 'manual',
                'upload_file_path' => $request->upload_file_path ?? null,
                'total_marks' => $request->total_marks ?? 100,
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            // Store results if provided
            if ($request->has('results')) {
                foreach ($request->results as $studentId => $subjectMarks) {
                    foreach ($subjectMarks as $subjectId => $marks) {
                        if (!is_null($marks) && $marks !== '') {
                            ExamEntryResult::create([
                                'exam_entry_id' => $examEntry->id,
                                'student_id' => $studentId,
                                'subject_id' => $subjectId,
                                'obtained_marks' => $marks,
                                'entry_source' => 'manual',
                                'entered_by' => auth()->id(),
                            ]);
                        }
                    }
                }

                // Update status to completed if results are entered
                $examEntry->update(['status' => 'completed']);
            }

            DB::commit();
            return ['status' => true, 'message' => 'Exam entry created successfully', 'data' => $examEntry];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Update exam entry
     */
    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $examEntry = ExamEntry::findOrFail($id);

            $examEntry->update([
                'total_marks' => $request->total_marks ?? $examEntry->total_marks,
                'status' => $request->status ?? $examEntry->status,
            ]);

            // Update results if provided
            if ($request->has('results')) {
                // Delete existing results
                $examEntry->results()->delete();

                // Insert new results
                foreach ($request->results as $studentId => $subjectMarks) {
                    foreach ($subjectMarks as $subjectId => $marks) {
                        if (!is_null($marks) && $marks !== '') {
                            ExamEntryResult::create([
                                'exam_entry_id' => $examEntry->id,
                                'student_id' => $studentId,
                                'subject_id' => $subjectId,
                                'obtained_marks' => $marks,
                                'entry_source' => 'manual',
                                'entered_by' => auth()->id(),
                            ]);
                        }
                    }
                }
            }

            DB::commit();
            return ['status' => true, 'message' => 'Exam entry updated successfully'];
        } catch (\Exception $e) {
            DB::rollback();
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Delete exam entry
     *
     * @param int $id
     * @return array ['status' => bool, 'message' => string]
     */
    public function destroy($id)
    {
        return DB::transaction(function () use ($id) {
            try {
                $examEntry = ExamEntry::with(['results'])->findOrFail($id);

                // Validation: Only allow deletion of draft entries
                if ($examEntry->status === 'published') {
                    return [
                        'status' => false,
                        'message' => 'Cannot delete published exam entries. Published results cannot be removed.'
                    ];
                }

                if ($examEntry->status === 'completed') {
                    return [
                        'status' => false,
                        'message' => 'Cannot delete completed exam entries. Only draft entries can be deleted.'
                    ];
                }

                // Count results that will be cascade deleted
                $resultsCount = $examEntry->results()->count();

                // Delete the entry (results will be cascade deleted automatically)
                $deleted = $examEntry->delete();

                if (!$deleted) {
                    return [
                        'status' => false,
                        'message' => 'Failed to delete exam entry. Please try again.'
                    ];
                }

                $message = 'Exam entry deleted successfully';
                if ($resultsCount > 0) {
                    $message .= " ({$resultsCount} student result(s) were also removed)";
                }

                return [
                    'status' => true,
                    'message' => $message,
                    'results_deleted' => $resultsCount
                ];
            } catch (\Exception $e) {
                return [
                    'status' => false,
                    'message' => 'Error deleting exam entry: ' . $e->getMessage()
                ];
            }
        });
    }

    /**
     * Show exam entry with results
     */
    public function show($id)
    {
        return ExamEntry::with([
            'session',
            'term',
            'class',
            'section',
            'examType',
            'subject',
            'results.student',
            'results.subject'
        ])->findOrFail($id);
    }

    /**
     * Publish exam entry results
     */
    public function publish($id)
    {
        try {
            $examEntry = ExamEntry::findOrFail($id);

            if ($examEntry->status !== 'completed') {
                return ['status' => false, 'message' => 'Only completed entries can be published'];
            }

            $examEntry->update([
                'status' => 'published',
                'published_at' => now()
            ]);

            return ['status' => true, 'message' => 'Results published successfully'];
        } catch (\Exception $e) {
            return ['status' => false, 'message' => $e->getMessage()];
        }
    }

    /**
     * Get all exam entries (paginated)
     */
    public function getPaginateAll($perPage = 25)
    {
        return ExamEntry::with(['session', 'term', 'class', 'section', 'examType'])
            ->orderBy('created_at', 'desc')
            ->paginate($perPage);
    }

    /**
     * Get all exam entries
     */
    public function all()
    {
        return ExamEntry::with(['session', 'term', 'class', 'section', 'examType'])->get();
    }
}
