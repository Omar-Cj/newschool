<?php

namespace App\Repositories\Academic;

use App\Enums\ApiStatus;
use App\Traits\ReturnFormatTrait;
use App\Models\Academic\ClassSetup;
use App\Interfaces\Academic\ClassSetupInterface;
use App\Models\Academic\ClassSetupChildren;
use Illuminate\Support\Facades\DB;

class ClassSetupRepository implements ClassSetupInterface
{
    use ReturnFormatTrait;

    private $model;

    public function __construct(ClassSetup $model)
    {
        $this->model = $model;
    }

    public function getSections($id) // class id
    {

        $result = $this->model->active()->where('classes_id', $id)->where('session_id', setting('session'))->first();
        return ClassSetupChildren::with('section')->where('class_setup_id', @$result->id)->select('section_id')->get()->unique('section_id');
    }

    public function getSectionsByClasses($classIds) // multiple class ids
    {
        if (empty($classIds)) {
            return collect();
        }

        $classSetups = $this->model->active()
            ->whereIn('classes_id', $classIds)
            ->where('session_id', setting('session'))
            ->get();

        $classSetupIds = $classSetups->pluck('id');

        return ClassSetupChildren::with('section')
            ->whereIn('class_setup_id', $classSetupIds)
            ->select('section_id', 'class_setup_id')
            ->get()
            ->unique('section_id')
            ->map(function($item) {
                return [
                    'id' => $item->section->id,
                    'name' => $item->section->name
                ];
            });
    }
    public function promoteClasses($id) // session id
    {
        return $this->model->active()->where('session_id', $id)->get();
    }
    public function promoteSections($session_id, $classes_id) //session id, class id
    {
        $result = $this->model->active()->where('classes_id', $classes_id)->where('session_id', $session_id)->first();
        return ClassSetupChildren::with('section')->where('class_setup_id', @$result->id)->select('section_id')->get();
    }

    public function all()
    {
        return $this->model->where('session_id', setting('session'))->active()->get();
    }

    public function getPaginateAll()
    {
        return $this->model::latest()->where('session_id', setting('session'))->paginate(10);
    }

    public function store($request)
    {
        // Log incoming request data
        \Log::info('ClassSetup store called', [
            'request_data' => $request->all(),
            'user' => auth()->check() ? auth()->user()->only(['id', 'name', 'school_id', 'branch_id']) : 'Not authenticated',
            'session_setting' => setting('session')
        ]);

        // dd('sfdsf');
        DB::beginTransaction();
        try {

            if($this->model::where('session_id', setting('session'))->where('classes_id', $request->classes)->first()) {
                \Log::info('ClassSetup duplicate check failed', [
                    'session_id' => setting('session'),
                    'classes_id' => $request->classes
                ]);
                return $this->responseWithError(___('alert.there_is_already_a_class_for_this_session'), []);
            }

            \Log::info('ClassSetup duplicate check passed, creating new setup', [
                'session_id' => setting('session'),
                'classes_id' => $request->classes,
                'auth_school_id' => auth()->check() ? auth()->user()->school_id : null
            ]);

            $setup              = new $this->model;
            $setup->session_id  = setting('session');
            $setup->classes_id    = $request->classes;

            \Log::info('Saving ClassSetup', [
                'setup_data' => $setup->toArray()
            ]);

            $setup->save();

            \Log::info('ClassSetup saved successfully', [
                'setup_id' => $setup->id,
                'setup_school_id' => $setup->school_id ?? 'not set'
            ]);

            foreach ($request->sections ?? [] as $key => $item) {
                \Log::info('Creating ClassSetupChildren', [
                    'iteration' => $key,
                    'class_setup_id' => $setup->id,
                    'section_id' => $item
                ]);

                $row = new ClassSetupChildren();
                $row->class_setup_id = $setup->id;
                $row->section_id     = $item;
                $row->save();

                \Log::info('ClassSetupChildren saved', [
                    'child_id' => $row->id,
                    'child_school_id' => $row->school_id ?? 'not set'
                ]);
            }

            DB::commit();
            \Log::info('ClassSetup transaction committed successfully');
            return $this->responseWithSuccess(___('alert.created_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollback();

            // Log full exception details
            \Log::error('ClassSetup store failed - FULL EXCEPTION DETAILS', [
                'exception_class' => get_class($th),
                'message' => $th->getMessage(),
                'code' => $th->getCode(),
                'file' => $th->getFile(),
                'line' => $th->getLine(),
                'sql' => $th instanceof \Illuminate\Database\QueryException ? $th->getSql() : null,
                'bindings' => $th instanceof \Illuminate\Database\QueryException ? $th->getBindings() : null,
                'trace' => $th->getTraceAsString(),
                'request_data' => $request->all(),
                'user' => auth()->check() ? auth()->user()->only(['id', 'school_id', 'branch_id']) : null
            ]);

            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function show($id)
    {
        return $this->model->find($id);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {

            if($this->model::where('session_id', setting('session'))->where('classes_id', $request->classes)->where('id', '!=', $id)->first()) {
                return $this->responseWithError(___('alert.there_is_already_a_class_for_this_session'), []);
            }


            $setup              = $this->model->findOrfail($id);
            $setup->classes_id    = $request->classes;
            $setup->save();

            ClassSetupChildren::where('class_setup_id', $setup->id)->delete();

            foreach ($request->sections ?? [] as $key => $item) {
                $row = new ClassSetupChildren();
                $row->class_setup_id = $setup->id;
                $row->section_id     = $item;
                $row->save();
            }
            DB::commit();
            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function destroy($id)
    {
        try {
            $row = $this->model->find($id);
            $row->delete();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }
}
