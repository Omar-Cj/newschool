<?php

namespace App\Repositories\Fees;

use App\Interfaces\Fees\FeesTypeInterface;
use App\Models\Fees\FeesType;
use App\Traits\ReturnFormatTrait;

class FeesTypeRepository implements FeesTypeInterface
{
    use ReturnFormatTrait;

    private $model;

    public function __construct(FeesType $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->active()->get();
    }

    public function getPaginateAll()
    {
        return $this->model::latest()->paginate(10);
    }

    public function store($request)
    {
        try {
            $row                            = new $this->model;
            $row->name                      = $request->name;
            $row->code                      = $request->code;
            $row->description               = $request->description;
            $row->academic_level            = $request->academic_level ?? 'all';
            $row->category                  = $request->category ?? 'academic';
            $row->amount                    = $request->amount ?? 0;
            $row->due_date_offset           = $request->due_date_offset ?? 30;
            $row->is_mandatory_for_level    = $request->has('is_mandatory_for_level') ? true : false;
            $row->status                    = $request->status;
            $row->save();
            return $this->responseWithSuccess(___('alert.created_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);

        }
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function update($request, $id)
    {
        try {
            // Verify fee type belongs to current school (global scope handles this)
            $row                            = $this->model->findOrFail($id);
            $row->name                      = $request->name;
            $row->code                      = $request->code;
            $row->description               = $request->description;
            $row->academic_level            = $request->academic_level ?? 'all';
            $row->category                  = $request->category ?? 'academic';
            $row->amount                    = $request->amount ?? 0;
            $row->due_date_offset           = $request->due_date_offset ?? 30;
            $row->is_mandatory_for_level    = $request->has('is_mandatory_for_level') ? true : false;
            $row->status                    = $request->status;
            $row->save();
            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        try {
            $row = $this->model->find($id);

            // Check if any students are subscribed to this fee type via Student Services
            $subscribedStudentsCount = $row->studentServices()->count();

            if ($subscribedStudentsCount > 0) {
                return $this->responseWithError(
                    "Cannot delete this fee type. {$subscribedStudentsCount} student(s) are currently subscribed to this fee.",
                    []
                );
            }

            $row->delete();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }
}
