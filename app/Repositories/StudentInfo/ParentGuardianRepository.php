<?php

namespace App\Repositories\StudentInfo;

use App\Models\Role;
use App\Models\SiblingFeesDiscount;
use App\Models\StudentInfo\Student;
use App\Models\User;
use App\Enums\Settings;
use App\Enums\ApiStatus;
use Illuminate\Support\Str;
use App\Traits\CommonHelperTrait;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\StudentInfo\ParentGuardian;
use App\Interfaces\StudentInfo\ParentGuardianInterface;

class ParentGuardianRepository implements ParentGuardianInterface
{
    use ReturnFormatTrait;
    use CommonHelperTrait;

    private $model;

    public function __construct(ParentGuardian $model)
    {
        $this->model = $model;
    }

    public function all()
    {
        return $this->model->active()->pluck('guardian_name','id')->toArray();
    }

    public function get()
    {
        return $this->model->select("id", "guardian_name")->active()->get();
    }

    public function getPaginateAll()
    {
        // MySQL 8.0 removed query_cache - no need for cache bypass statement
        // Always fetch fresh data from database
        return $this->model::withCount('children')->latest()->paginate(Settings::PAGINATE);
    }

    public function searchParent($request)
    {
        // MySQL 8.0 removed query_cache - queries are always fresh
        return $this->model::withCount('children')
        ->where('guardian_name', 'LIKE', "%{$request->keyword}%")
        ->orWhere('guardian_email', 'LIKE', "%{$request->keyword}%")
        ->orWhere('guardian_mobile', 'LIKE', "%{$request->keyword}%")
        ->paginate(Settings::PAGINATE);
    }

    public function getParent($request)
    {
        return $this->model->where('guardian_name', 'like', '%' . $request->text . '%')->pluck('guardian_name','id')->toArray();
    }

    public function store($request)
    {
        // Check if already in a transaction to avoid nested transaction issues
        $wasInTransaction = DB::transactionLevel() > 0;

        // Enhanced connection reset for MySQL 8.0.42 (Error 1615 fix)
        // Only reset connection if not in a transaction
        if (!$wasInTransaction) {
            DB::disconnect('mysql');
            DB::reconnect('mysql');
            DB::beginTransaction();
        }

        try {
            $role                     = Role::find(7); // Guardian role id 7

            $user                    = new User();
            $user->name              = $request->guardian_name;
            $user->email             = $request->guardian_email;
            $user->phone             = $request->guardian_mobile;
            $user->password          = $request->password_type == 'default'? Hash::make('123456') : Hash::make($request->password);
            $user->email_verified_at = now();
            $user->role_id           = $role->id;
            $user->permissions       = $role->permissions;
            $user->username          = $request->username ?? $request->guardian_email;

            // Add school_id from authenticated user
            $user->school_id         = auth()->user()->school_id ?? null;

            // Add branch_id from authenticated user
            $user->branch_id         = auth()->user()->branch_id ?? 1;

            $user->upload_id         = $this->UploadImageCreate($request->guardian_image, 'backend/uploads/users');
            $user->uuid              = Str::uuid();
            $user->save();

            $row                      = new $this->model;
            $row->user_id             = $user->id;
            $row->guardian_profession = $request->guardian_profession;
            $row->guardian_address    = $request->guardian_address;
            $row->guardian_relation   = $request->guardian_relation;
            $row->guardian_name       = $request->guardian_name;
            $row->guardian_email      = $request->guardian_email;
            $row->guardian_mobile     = $request->guardian_mobile;
            $row->guardian_image      = $user->upload_id;
            $row->status              = $request->status;
            $row->guardian_place_of_work = $request->guardian_place_of_work;
            $row->guardian_position      = $request->guardian_position;

            $row->save();

            // Only commit if we started the transaction
            if (!$wasInTransaction) {
                DB::commit();
            }

            // Clear cached guardian listings
            \Cache::tags(['guardians'])->flush();

            return $this->responseWithSuccess(___('alert.created_successfully'), []);
        } catch (\Throwable $th) {
            // Only rollback if we started the transaction
            if (!$wasInTransaction) {
                DB::rollback();
            }

            // Enhanced error logging for debugging nested transactions
            \Log::error('ParentGuardian Store Failed', [
                'error_message' => $th->getMessage(),
                'error_type' => get_class($th),
                'was_nested_transaction' => $wasInTransaction,
                'transaction_level' => DB::transactionLevel(),
                'request_data' => $request->except(['password', 'guardian_image']),
                'stack_trace' => $th->getTraceAsString()
            ]);

            // Re-throw exception if in nested transaction so outer transaction can handle it
            if ($wasInTransaction) {
                throw $th;
            }

            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function update($request, $id)
    {
        // Check if already in a transaction to avoid nested transaction issues
        $wasInTransaction = DB::transactionLevel() > 0;

        // Enhanced connection reset for MySQL 8.0.42 (Error 1615 fix)
        // Only reset connection if not in a transaction
        if (!$wasInTransaction) {
            DB::disconnect('mysql');
            DB::reconnect('mysql');
            DB::beginTransaction();
        }

        try {
            // Use lockForUpdate to ensure fresh data and prevent race conditions
            $row = $this->model->lockForUpdate()->find($id);

            if (!$row) {
                throw new \Exception("Guardian not found with ID: {$id}");
            }

            $user = User::lockForUpdate()->find($row->user_id);

            if (!$user) {
                throw new \Exception("User not found with ID: {$row->user_id}");
            }

            $role = Role::find($user->role_id);

            $user->name               = $request->guardian_name;
            $user->email              = $request->guardian_email;
            $user->phone              = $request->guardian_mobile;
            $user->upload_id          = $this->UploadImageUpdate($request->guardian_image, 'backend/uploads/users',$user->upload_id);
            $user->permissions        = $role->permissions;
            $user->username           = $request->username;
            $user->save();

            $row->guardian_profession = $request->guardian_profession;
            $row->guardian_address    = $request->guardian_address;
            $row->guardian_relation   = $request->guardian_relation;
            $row->guardian_name       = $request->guardian_name;
            $row->guardian_email      = $request->guardian_email;
            $row->guardian_mobile     = $request->guardian_mobile;
            $row->guardian_image      = $user->upload_id;
            $row->status              = $request->status;
            $row->guardian_place_of_work = $request->guardian_place_of_work;
            $row->guardian_position      = $request->guardian_position;

            $row->save();

            // Only commit if we started the transaction
            if (!$wasInTransaction) {
                DB::commit();
            }

            // Clear any cached data for this guardian
            \Cache::forget("guardian_{$id}");
            \Cache::tags(['guardians'])->flush();

            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            // Only rollback if we started the transaction
            if (!$wasInTransaction) {
                DB::rollback();
            }

            // Comprehensive error logging with transaction context
            \Log::error('ParentGuardian Update Failed', [
                'guardian_id' => $id,
                'user_id' => $row->user_id ?? null,
                'error_message' => $th->getMessage(),
                'error_type' => get_class($th),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
                'was_nested_transaction' => $wasInTransaction,
                'transaction_level' => DB::transactionLevel(),
                'request_data' => $request->except(['password', 'guardian_image']),
                'stack_trace' => $th->getTraceAsString()
            ]);

            // Re-throw exception if in nested transaction so outer transaction can handle it
            if ($wasInTransaction) {
                throw $th;
            }

            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $row = $this->model->find($id);
            $row->delete();

            $user = User::find($row->user_id);
            $this->UploadImageDelete($user->upload_id);
            $user->delete();

            DB::commit();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function getStudentsByParent($parentId)
    {
        $students =  Student::select('id','admission_no', 'roll_no', 'first_name', 'last_name', 'mobile',
            'email', 'dob', 'admission_date')
            ->with('session_class_student.class')
            ->where('parent_guardian_id', $parentId)
            ->get();

        $siblingsCount =  $students->count();
        $siblingDiscount = SiblingFeesDiscount::where('siblings_number', '<=', $siblingsCount)
            ->orderByDesc('siblings_number')
            ->first();

        return [
            'children' => $students,
            'siblingsCount' => $siblingsCount,
            'siblingDiscount' => $siblingDiscount ? $siblingDiscount->discount_percentage : null,
            'isEligible' => (bool)$siblingDiscount,
        ];
    }
}
