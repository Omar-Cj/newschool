<?php

namespace Modules\Journals\Repositories;

use Modules\Journals\Interfaces\JournalInterface;
use Modules\Journals\Entities\Journal;
use Modules\Journals\Entities\JournalAuditLog;
use Modules\MultiBranch\Entities\Branch;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class JournalRepository implements JournalInterface
{
    use ReturnFormatTrait;

    private $model;

    public function __construct(Journal $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->active()->get();
    }

    public function active()
    {
        return $this->model->active()->get();
    }

    public function forSchool($schoolId)
    {
        return $this->model->active()->forSchool($schoolId)->get();
    }

    public function getPaginateAll()
    {
        return $this->model::with(['createdBy', 'branch'])->latest()->paginate(10);
    }

    public function search($request)
    {
        $query = $this->model->query();

        if ($request->has('search') && !empty($request->search)) {
            $query->search($request->search);
        }

        if ($request->has('status') && !empty($request->status)) {
            $query->where('status', $request->status);
        }

        // Handle both old branch text filter and new branch_id filter
        if ($request->has('branch') && !empty($request->branch)) {
            $query->where('branch', 'LIKE', "%{$request->branch}%");
        }

        if ($request->has('branch_id') && !empty($request->branch_id)) {
            $query->byBranch($request->branch_id);
        }

        return $query->with(['createdBy', 'branch'])->latest()->paginate(10);
    }

    public function store($request)
    {
        try {
            $row = new $this->model;
            $row->name = $request->name;

            // Handle both old branch text and new branch_id
            if ($request->has('branch_id') && !empty($request->branch_id)) {
                $row->branch_id = $request->branch_id;
                // Also set branch text for backward compatibility (until old column is removed)
                $branch = Branch::find($request->branch_id);
                $row->branch = $branch ? $branch->name : null;
            } else if ($request->has('branch')) {
                $row->branch = $request->branch;
            }

            $row->description = $request->description;
            $row->status = $request->status ?? 'active';
            $row->school_id = $request->school_id ?? Auth::user()->school_id;
            $row->created_by = Auth::id();
            $row->save();

            return $this->responseWithSuccess(___('alert.created_successfully'), $row);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function show($id)
    {
        return $this->model->with(['createdBy', 'school', 'branch'])->find($id);
    }

    public function update($request, $id)
    {
        try {
            $row = $this->model->findOrFail($id);
            $row->name = $request->name;

            // Handle both old branch text and new branch_id
            if ($request->has('branch_id') && !empty($request->branch_id)) {
                $row->branch_id = $request->branch_id;
                // Also set branch text for backward compatibility (until old column is removed)
                $branch = Branch::find($request->branch_id);
                $row->branch = $branch ? $branch->name : $row->branch;
            } else if ($request->has('branch')) {
                $row->branch = $request->branch;
            }

            $row->description = $request->description;
            $row->status = $request->status ?? $row->status;
            $row->save();

            return $this->responseWithSuccess(___('alert.updated_successfully'), $row);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        try {
            $row = $this->model->find($id);

            // Check if journal is inactive (closed)
            if ($row->status === 'inactive') {
                return $this->responseWithError(___('alert.cannot_delete_closed_journal'), []);
            }

            // Check if journal has associated fee collections
            if ($row->feesCollects()->count() > 0) {
                return $this->responseWithError(___('alert.cannot_delete_journal_with_transactions'), []);
            }

            $row->delete();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function close($id)
    {
        try {
            $row = $this->model->findOrFail($id);

            // Check if journal is already inactive
            if ($row->status === 'inactive') {
                return $this->responseWithError(___('alert.journal_already_closed'), []);
            }

            // Update status to inactive
            $row->status = 'inactive';
            $row->save();

            // Create audit log
            JournalAuditLog::create([
                'journal_id' => $row->id,
                'action' => 'closed',
                'performed_by' => Auth::id(),
                'performed_at' => now(),
                'notes' => null,
            ]);

            return $this->responseWithSuccess(___('alert.journal_closed_successfully'), $row);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function open($id)
    {
        try {
            $row = $this->model->findOrFail($id);

            // Check if journal is already active
            if ($row->status === 'active') {
                return $this->responseWithError(___('alert.journal_already_open'), []);
            }

            // Update status to active
            $row->status = 'active';
            $row->save();

            // Create audit log
            JournalAuditLog::create([
                'journal_id' => $row->id,
                'action' => 'opened',
                'performed_by' => Auth::id(),
                'performed_at' => now(),
                'notes' => null,
            ]);

            return $this->responseWithSuccess(___('alert.journal_opened_successfully'), $row);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function getJournalsForDropdown($schoolId = null, $branchId = null)
    {
        $query = $this->model->active();

        if ($schoolId) {
            $query->forSchool($schoolId);
        } else if (Auth::user() && Auth::user()->school_id) {
            $query->forSchool(Auth::user()->school_id);
        }

        $effectiveBranchId = $branchId ?? (Auth::user()->branch_id ?? null);

        if ($effectiveBranchId && Schema::hasColumn($this->model->getTable(), 'branch_id')) {
            $query->byBranch($effectiveBranchId);
        }

        return $query->with('branch')->select('id', 'name', 'branch', 'branch_id')->get()->map(function ($journal) {
            return [
                'id' => $journal->id,
                'text' => $journal->display_name,
                'name' => $journal->name,
                'branch' => $journal->branch,
                'branch_id' => $journal->branch_id
            ];
        });
    }

    /**
     * Get all active branches for dropdown
     */
    public function getBranchesForDropdown()
    {
        return Branch::active()->select('id', 'name')->get()->map(function ($branch) {
            return [
                'id' => $branch->id,
                'text' => $branch->name,
                'name' => $branch->name
            ];
        });
    }
}
