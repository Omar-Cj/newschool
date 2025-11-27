<?php

namespace Modules\MainApp\Http\Repositories;

use App\Enums\Settings;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use Modules\MainApp\Entities\Package;
use Modules\MainApp\Enums\PackagePaymentType;
use Modules\MainApp\Http\Interfaces\PackageInterface;

class PackageRepository implements PackageInterface
{
    use ReturnFormatTrait;
    private $model;

    public function __construct(Package $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->active()->get();
    }

    public function getAll()
    {
        return $this->model->latest()->paginate(Settings::PAGINATE);
    }

    public function store($request)
    {
        try {
            DB::beginTransaction();

            $row                 = new $this->model;
            $row->name           = $request->name;
            $row->payment_type           = $request->payment_type;

            if($request->payment_type == PackagePaymentType::POSTPAID) {
                $row->per_student_price          = $request->per_student_price;
                $row->price                      = 0;
                $row->duration       = 1;
                $row->duration_number= 30;
            } else {

                $row->per_student_price          = 0;

                $row->price          = $request->price;
                $row->student_limit  = $request->student_limit;
                $row->staff_limit    = $request->staff_limit;
                $row->duration       = $request->duration;
                $row->duration_number= $request->duration_number;

            }

            $row->description    = $request->description;
            $row->popular        = $request->popular;
            $row->status         = $request->status;
            $row->save();

            DB::commit();
            return $this->responseWithSuccess(___('alert.created_successfully'), $row);
        } catch (\Throwable $th) {
            DB::rollBack();
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
            DB::beginTransaction();
            
            $row                 = $this->model->findOrfail($id);
            $row->name           = $request->name;

            $row->payment_type           = $request->payment_type;

            if($request->payment_type == PackagePaymentType::POSTPAID) {
                $row->per_student_price          = $request->per_student_price;
                $row->price                      = 0;
                $row->student_limit  = null;
                $row->staff_limit    = null;

                $row->duration        = 1;
                $row->duration_number = 30;
            } else {

                $row->per_student_price          = 0;

                $row->price          = $request->price;
                $row->student_limit  = $request->student_limit;
                $row->staff_limit    = $request->staff_limit;
                $row->duration       = $request->duration;
                $row->duration_number= $request->duration_number;

            }

            $row->description    = $request->description;
            $row->popular        = $request->popular;
            $row->status         = $request->status;
            $row->save();

            DB::commit();
            return $this->responseWithSuccess(___('alert.updated_successfully'), $row);
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        try {
            $row = $this->model->find($id);

            if (!$row) {
                return $this->responseWithError(___('alert.package_not_found'), []);
            }

            // Check for related data before deletion
            $schoolCount = \Modules\MainApp\Entities\School::where('package_id', $row->id)->count();
            $subscriptionCount = \Modules\MainApp\Entities\Subscription::where('package_id', $row->id)->count();

            // Build error message if there are related records
            $relatedData = [];

            if ($schoolCount > 0) {
                $relatedData[] = "{$schoolCount} school(s)";
            }

            if ($subscriptionCount > 0) {
                $relatedData[] = "{$subscriptionCount} subscription(s)";
            }

            if (!empty($relatedData)) {
                $errorMessage = "Cannot delete this package. It is currently used by " . implode(' and ', $relatedData) . ". Please reassign these records to another package first.";
                return $this->responseWithError($errorMessage, []);
            }

            $row->delete();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }
}
