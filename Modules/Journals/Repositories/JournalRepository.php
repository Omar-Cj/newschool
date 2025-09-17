<?php

namespace Modules\Journals\Repositories;

use Modules\Journals\Interfaces\JournalInterface;
use Modules\Journals\Entities\Journal;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\Auth;

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
        return $this->model::with(['createdBy'])->latest()->paginate(10);
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

        if ($request->has('branch') && !empty($request->branch)) {
            $query->where('branch', 'LIKE', "%{$request->branch}%");
        }

        return $query->with(['createdBy'])->latest()->paginate(10);
    }

    public function store($request)
    {
        try {
            $row = new $this->model;
            $row->name = $request->name;
            $row->branch = $request->branch;
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
        return $this->model->with(['createdBy', 'school'])->find($id);
    }

    public function update($request, $id)
    {
        try {
            $row = $this->model->findOrFail($id);
            $row->name = $request->name;
            $row->branch = $request->branch;
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

    public function getJournalsForDropdown($schoolId = null)
    {
        $query = $this->model->active();

        if ($schoolId) {
            $query->forSchool($schoolId);
        } else if (Auth::user() && Auth::user()->school_id) {
            $query->forSchool(Auth::user()->school_id);
        }

        return $query->select('id', 'name', 'branch')->get()->map(function ($journal) {
            return [
                'id' => $journal->id,
                'text' => $journal->display_name,
                'name' => $journal->name,
                'branch' => $journal->branch
            ];
        });
    }
}