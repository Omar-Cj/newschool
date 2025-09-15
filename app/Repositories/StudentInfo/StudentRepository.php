<?php

namespace App\Repositories\StudentInfo;

use App\Models\Role;
use App\Models\SiblingFeesDiscount;
use App\Models\User;
use App\Enums\Settings;
use App\Models\Session;
use App\Enums\ApiStatus;
use Illuminate\Support\Str;
use App\Traits\CommonHelperTrait;
use App\Traits\ReturnFormatTrait;
use Illuminate\Support\Facades\DB;
use App\Models\StudentInfo\Student;
use App\Services\StudentServiceManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Builder;
use App\Models\StudentInfo\SessionClassStudent;
use App\Interfaces\StudentInfo\StudentInterface;

class StudentRepository implements StudentInterface
{
    use ReturnFormatTrait;
    use CommonHelperTrait;

    private $model;
    private $serviceManager;

    public function __construct(Student $model, StudentServiceManager $serviceManager)
    {
        $this->model = $model;
        $this->serviceManager = $serviceManager;
    }

    public function all()
    {
        return $this->model->active()->get();
    }

    public function getStudents($request)
    {
        return  SessionClassStudent::query()
            ->where('session_id', setting('session'))
            ->where('classes_id', $request->class)
            ->where('section_id', $request->section)
            ->when(request()->filled('gender'), function ($q) use ($request) {
                $q->whereHas('student', fn($q) => $q->where('gender_id', $request->gender));
            })
            ->with(['student.upload', 'student.user'])
            ->get();
    }


    public function getPaginateAll()
    {
        return SessionClassStudent::whereHas('student')->where('session_id', setting('session'))->latest()->with(['student.upload', 'student.user'])->paginate(Settings::PAGINATE);
    }
    public function getSessionStudent($id)
    {
        return SessionClassStudent::where('id', $id)->first();
    }


    public function searchStudents($request)
    {
        $students = SessionClassStudent::query();
        $students = $students->where('session_id', setting('session'));

        if ($request->class != "") {
            $students = $students->where('classes_id', $request->class);
        }
        if ($request->section != "") {
            $students = $students->where('section_id', $request->section);
        }
        if ($request->keyword != "") {
            $students = $students->whereHas('student', function ($query) use ($request) {
                $query->where('first_name', 'LIKE', "%{$request->keyword}%")
                    ->orWhere('last_name', 'LIKE', "%{$request->keyword}%")
                    ->orWhere('dob', 'LIKE', "%{$request->keyword}%");
            });
        }

        return $students->with(['student.upload', 'student.user'])->paginate(Settings::PAGINATE);
    }

    public function store($request)
    {
        DB::beginTransaction();
        try {
            // if($this->model->count() >= setting('student_limit'))
            if ($this->model->count() >= activeSubscriptionStudentLimit() && env('APP_SAAS'))
                return $this->responseWithError(___('alert.Student limit is over.'), []);

            $role                     = Role::find(6); // student role id 6

            $user                    = new User();
            $user->name              = $request->first_name . ' ' . $request->last_name;
            $user->email             = $request->email  != "" ? $request->email :  NULL;
            $user->phone             = $request->mobile != "" ? $request->mobile :  NULL;
            $user->password          = $request->password_type == 'default' ? Hash::make('123456') : Hash::make($request->password);
            $user->email_verified_at = now();
            $user->role_id           = $role->id;
            $user->permissions       = $role->permissions;
            $user->date_of_birth     = $request->date_of_birth;
            $user->username          = $request->username;
            $user->upload_id         = $this->UploadImageCreate($request->image, 'backend/uploads/students');
            $user->uuid              = Str::uuid();
            $user->save();

            $row                       = new $this->model;
            $row->user_id              = $user->id;
            $row->first_name           = $request->first_name;
            $row->last_name            = $request->last_name;
            $row->mobile               = $request->mobile;
            $row->image_id             = $user->upload_id;
            $row->email                = $request->email;
            $row->dob                  = $request->date_of_birth;
            $row->religion_id          = $request->religion != "" ? $request->religion :  NULL;
            $row->gender_id            = $request->gender != "" ? $request->gender :  NULL;
            $row->blood_group_id       = $request->blood != "" ? $request->blood :  NULL;
            $row->admission_date       = $request->admission_date;
            $row->parent_guardian_id   = $request->parent != "" ? $request->parent :  NULL;
            $row->student_category_id  = $request->category != "" ? $request->category :  NULL;

            $row->previous_school = $request->previous_school ?? 0;
            $row->previous_school_info = $request->previous_school_info;
            $row->previous_school_image_id = $this->UploadImageCreate($request->previous_school_image, 'backend/uploads/students');
            $row->place_of_birth = $request->place_of_birth;
            $row->nationality = $request->nationality;
            $row->cpr_no = $request->cpr_no;
            $row->spoken_lang_at_home = $request->spoken_lang_at_home;
            $row->residance_address = $request->residance_address;

            $row->status               = $request->status;
            $row->siblings_discount   = $request->siblings_discount;
            $row->upload_documents     = $this->uploadDocuments($request);
            $row->place_of_birth = $request->place_of_birth;
            $row->nationality = $request->nationality;

            $row->health_status = $request->health_status;
            $row->rank_in_family = !empty($request->rank_in_family) ? $request->rank_in_family : 0;
            $row->siblings = !empty($request->siblings) ? $request->siblings : 0;

            $row->cpr_no = $request->cpr_no;
            $row->spoken_lang_at_home = $request->spoken_lang_at_home;
            $row->residance_address = $request->residance_address;
            $row->department_id = $request->department_id;
            $row->save();

            $session_class                      = new SessionClassStudent();
            $session_class->session_id          = setting('session');
            $session_class->classes_id          = $request->class;
            $session_class->section_id          = $request->section != "" ? $request->section :  NULL;
            $session_class->shift_id            = $request->shift != "" ? $request->shift :  NULL;
            $session_class->student_id          = $row->id;
            $session_class->roll                = NULL; // Roll number field removed
            $session_class->save();

            // Auto-subscribe to mandatory services for enhanced fee processing system
            try {
                $student = $this->model->find($row->id);
                $subscriptions = $this->serviceManager->autoSubscribeMandatoryServices(
                    $student, 
                    setting('session')
                );

                Log::info('Student registered with service auto-subscription', [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'academic_level' => $student->getAcademicLevel(),
                    'mandatory_services_count' => $subscriptions->count(),
                    'total_fees' => $subscriptions->sum('final_amount')
                ]);

            } catch (\Exception $e) {
                // Log the error but don't fail the registration
                Log::warning('Failed to auto-subscribe student to mandatory services during registration', [
                    'student_id' => $row->id,
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString()
                ]);
                
                // We don't throw the exception to avoid failing student registration
                // Services can be manually assigned later
            }

            // Handle manually added services from the form
            if ($request->has('services') && is_array($request->services)) {
                $this->createStudentServices($row, $request->services);
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.created_successfully'), ['student_id' => $row->id]);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }




    public function show($id)
    {
        return $this->model->with(['upload', 'user'])->find($id);
    }

    public function update($request, $id)
    {
        DB::beginTransaction();
        try {
            $row = $this->model->find($id);
            
            if (!$row) {
                DB::rollback();
                return $this->responseWithError('Student record not found.', []);
            }

            $user = User::where('id', $row->user_id)->first();
            
            if (!$user) {
                DB::rollback();
                return $this->responseWithError('Student user account not found.', []);
            }

            $role = Role::find($user->role_id);
            
            if (!$role) {
                DB::rollback();
                return $this->responseWithError('User role not found.', []);
            }

            $user->name               = $request->first_name . ' ' . $request->last_name;
            $user->email              = $request->email != "" ? $request->email :  NULL;
            $user->phone              = $request->mobile != "" ? $request->mobile :  NULL;
            $user->date_of_birth      = $request->date_of_birth;
            $user->upload_id          = $this->UploadImageUpdate($request->image, 'backend/uploads/students', $user->upload_id);
            $user->permissions        = $role->permissions;
            $user->username          = $request->username;
            $user->save();

            $row->first_name           = $request->first_name;
            $row->last_name            = $request->last_name;
            $row->mobile               = $request->mobile;
            $row->image_id             = $user->upload_id;
            $row->email                = $request->email;
            $row->dob                  = $request->date_of_birth;
            $row->religion_id          = $request->religion != "" ? $request->religion :  NULL;
            $row->gender_id            = $request->gender != "" ? $request->gender :  NULL;
            $row->blood_group_id       = $request->blood != "" ? $request->blood :  NULL;
            $row->admission_date       = $request->admission_date;
            $row->parent_guardian_id   = $request->parent != "" ? $request->parent :  NULL;
            $row->student_category_id  = $request->category != "" ? $request->category :  NULL;

            $row->previous_school = $request->previous_school ?? 0;
            $row->previous_school_info = $request->previous_school ? $request->previous_school_info : null;
            $row->previous_school_image_id = $request->previous_school ? $this->UploadImageCreate($request->previous_school_image, 'backend/uploads/students') : null;
            $row->place_of_birth = $request->place_of_birth;
            $row->nationality = $request->nationality;
            $row->cpr_no = $request->cpr_no;
            $row->spoken_lang_at_home = $request->spoken_lang_at_home;
            $row->residance_address = $request->residance_address;

            $row->health_status = $request->health_status;
            $row->rank_in_family = !empty($request->rank_in_family) ? $request->rank_in_family : 0;
            $row->siblings = !empty($request->siblings) ? $request->siblings : 0;

            $row->status               = $request->status;
            $row->upload_documents     = $row->upload_documents ?? $this->uploadDocuments($request, $row->upload_documents);
            $row->department_id        = $request->department_id;
            $row->save();

            $session_class = SessionClassStudent::where('session_id', setting('session'))->where('student_id', $row->id)->first();
            
            if (!$session_class) {
                DB::rollback();
                return $this->responseWithError('Student class assignment not found for current session.', []);
            }
            
            $session_class->classes_id          = $request->class;
            $session_class->section_id          = $request->section != "" ? $request->section :  NULL;
            $session_class->shift_id            = $request->shift != "" ? $request->shift :  NULL;
            $session_class->student_id          = $row->id;
            $session_class->roll                = NULL; // Roll number field removed
            $session_class->save();

            // Handle student services update
            if ($request->has('services') && is_array($request->services)) {
                $this->updateStudentServices($row, $request->services);
            }

            DB::commit();
            return $this->responseWithSuccess(___('alert.updated_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollback();
            // Log the actual error for debugging
            \Log::error('Student update failed: ' . $th->getMessage(), [
                'student_id' => $id,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);
            return $this->responseWithError('Update failed: ' . $th->getMessage(), []);
        }
    }

    public function destroy($id)
    {
        DB::beginTransaction();
        try {
            $row  = $this->model->find($id);
            $user = User::find($row->user_id);
            if ($user) {
                $this->UploadImageDelete($user->upload_id);
                foreach ($row->upload_documents ?? [] as $doc) {
                    $this->UploadImageDelete($doc['file']);
                }
                $user->delete(); // when user delete auto delete student, session class student table's row
            }
            SessionClassStudent::where('student_id', $row->id)->delete();
            $row->delete();

            DB::commit();
            return $this->responseWithSuccess(___('alert.deleted_successfully'), []);
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->responseWithError(___('alert.something_went_wrong_please_try_again'), []);
        }
    }

    public function getSiblingsDiscount($parentId)
    {
        $students =  Student::select('id')
            ->where('parent_guardian_id', $parentId)
            ->get();

        $siblingsCount =  $students->count();
        return SiblingFeesDiscount::where('siblings_number', '<=', $siblingsCount)
            ->orderByDesc('siblings_number')
            ->first();
    }

    public function getStudentsByClass($class)
    {
        return SessionClassStudent::query()
            ->where('session_id', setting('session'))
            ->where('classes_id', $class)
            ->with('student')
            ->get();
    }

    /**
     * Update student services
     */
    private function updateStudentServices(Student $student, array $servicesData)
    {
        $currentAcademicYear = session('academic_year_id') ?? \App\Models\Session::active()->value('id');
        
        // Get existing services for this academic year
        $existingServices = $student->studentServices()
            ->where('academic_year_id', $currentAcademicYear)
            ->get()
            ->keyBy('id');

        $processedServiceIds = [];

        foreach ($servicesData as $serviceData) {
            // Skip empty service data
            if (empty($serviceData['fee_type_id'])) {
                continue;
            }

            $serviceId = $serviceData['id'] ?? null;
            $processedServiceIds[] = $serviceId;

            // Prepare service data
            $data = [
                'student_id' => $student->id,
                'fee_type_id' => $serviceData['fee_type_id'],
                'academic_year_id' => $currentAcademicYear,
                'amount' => $serviceData['amount'] ?? 0,
                'due_date' => !empty($serviceData['due_date']) ? $serviceData['due_date'] : null,
                'discount_type' => $serviceData['discount_type'] ?? 'none',
                'discount_value' => $serviceData['discount_value'] ?? 0,
                'is_active' => $serviceData['is_active'] ?? true,
                'subscription_date' => now(),
                'created_by' => auth()->id() ?? 1,
            ];

            // Calculate final amount after discount
            $finalAmount = $data['amount'];
            if ($data['discount_type'] === 'percentage' && $data['discount_value'] > 0) {
                $finalAmount = $data['amount'] - ($data['amount'] * $data['discount_value'] / 100);
            } elseif ($data['discount_type'] === 'fixed' && $data['discount_value'] > 0) {
                $finalAmount = max(0, $data['amount'] - $data['discount_value']);
            }
            $data['final_amount'] = $finalAmount;

            if ($serviceId && $existingServices->has($serviceId)) {
                // Update existing service
                $existingServices[$serviceId]->update($data);
            } else {
                // Create new service
                \App\Models\StudentService::create($data);
            }
        }

        // Delete services that were removed (not in the processed list)
        $servicesToDelete = $existingServices->keys()->diff(array_filter($processedServiceIds));
        if ($servicesToDelete->count() > 0) {
            \App\Models\StudentService::whereIn('id', $servicesToDelete)->delete();
        }
    }

    /**
     * Create student services for new student
     */
    private function createStudentServices(Student $student, array $servicesData)
    {
        $currentAcademicYear = session('academic_year_id') ?? \App\Models\Session::active()->value('id');

        foreach ($servicesData as $serviceData) {
            // Skip empty service data
            if (empty($serviceData['fee_type_id'])) {
                continue;
            }

            // Prepare service data
            $data = [
                'student_id' => $student->id,
                'fee_type_id' => $serviceData['fee_type_id'],
                'academic_year_id' => $currentAcademicYear,
                'amount' => $serviceData['amount'] ?? 0,
                'due_date' => !empty($serviceData['due_date']) ? $serviceData['due_date'] : null,
                'discount_type' => $serviceData['discount_type'] ?? 'none',
                'discount_value' => $serviceData['discount_value'] ?? 0,
                'is_active' => $serviceData['is_active'] ?? true,
                'subscription_date' => now(),
                'created_by' => auth()->id() ?? 1,
            ];

            // Calculate final amount after discount
            $finalAmount = $data['amount'];
            if ($data['discount_type'] === 'percentage' && $data['discount_value'] > 0) {
                $finalAmount = $data['amount'] - ($data['amount'] * $data['discount_value'] / 100);
            } elseif ($data['discount_type'] === 'fixed' && $data['discount_value'] > 0) {
                $finalAmount = max(0, $data['amount'] - $data['discount_value']);
            }
            $data['final_amount'] = $finalAmount;

            // Create service
            \App\Models\StudentService::create($data);
        }
    }
}
