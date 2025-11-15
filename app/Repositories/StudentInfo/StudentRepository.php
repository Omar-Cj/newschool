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
    private $parentGuardianRepository;

    public function __construct(
        Student $model,
        StudentServiceManager $serviceManager,
        ParentGuardianRepository $parentGuardianRepository
    ) {
        $this->model = $model;
        $this->serviceManager = $serviceManager;
        $this->parentGuardianRepository = $parentGuardianRepository;
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

            // Handle parent creation if creating inline
            $parentGuardianId = null;
            $parentCreationMode = $request->input('parent_creation_mode', 'existing');

            if ($parentCreationMode === 'new') {
                // LOG CHECKPOINT: Starting inline parent creation
                Log::info('CHECKPOINT: Starting inline parent creation', [
                    'parent_name' => $request->new_parent_name,
                    'parent_mobile' => $request->new_parent_mobile,
                    'parent_relation' => $request->new_parent_relation,
                    'student_name' => $request->first_name . ' ' . $request->last_name
                ]);

                // Create new parent guardian within the same transaction
                $parentData = new \Illuminate\Http\Request([
                    'guardian_name' => $request->new_parent_name,
                    'guardian_mobile' => $request->new_parent_mobile,
                    'guardian_relation' => $request->new_parent_relation,
                    'guardian_email' => null,
                    'guardian_profession' => null,
                    'guardian_address' => null,
                    'guardian_place_of_work' => null,
                    'guardian_position' => null,
                    'status' => 1, // Active status
                    'username' => null,
                    'password_type' => 'default',
                    'password' => null,
                    'guardian_image' => null,
                ]);

                $parentResult = $this->parentGuardianRepository->store($parentData);

                if (!$parentResult['status']) {
                    throw new \Exception($parentResult['message']);
                }

                // Get the newly created parent ID
                // The store method returns success but doesn't include the ID in response
                // We need to fetch the last created parent for this mobile
                $newParent = \App\Models\StudentInfo\ParentGuardian::where('guardian_mobile', $request->new_parent_mobile)
                    ->orderBy('id', 'desc')
                    ->first();

                if (!$newParent) {
                    throw new \Exception('Failed to retrieve newly created parent guardian.');
                }

                $parentGuardianId = $newParent->id;

                // LOG CHECKPOINT: Parent created successfully
                Log::info('CHECKPOINT: Parent created successfully', [
                    'parent_id' => $parentGuardianId,
                    'parent_name' => $request->new_parent_name,
                    'parent_mobile' => $request->new_parent_mobile,
                ]);
            } else {
                // Use existing parent
                $parentGuardianId = $request->parent != "" ? $request->parent : NULL;

                Log::info('CHECKPOINT: Using existing parent', [
                    'parent_id' => $parentGuardianId
                ]);
            }

            // LOG CHECKPOINT: Starting student user creation
            Log::info('CHECKPOINT: Creating student user', [
                'student_name' => $request->first_name . ' ' . $request->last_name,
                'parent_id' => $parentGuardianId,
                'grade' => $request->grade,
                'class' => $request->class
            ]);

            $role                     = Role::find(6); // student role id 6

            $user                    = new User();
            $user->name              = $request->first_name . ' ' . $request->last_name;
            $user->email             = $request->email  != "" ? $request->email :  NULL;
            $user->phone             = $request->mobile != "" ? $request->mobile :  NULL;
            $user->password          = $request->password_type == 'default' ? Hash::make('123456') : Hash::make($request->password);
            $user->email_verified_at = now();
            $user->role_id           = $role->id;
            $user->permissions       = $role->permissions;
            $user->date_of_birth     = $request->date_of_birth != "" ? $request->date_of_birth : NULL;
            $user->username          = $request->username ?? $request->email;

            // Add school_id from authenticated user
            $user->school_id         = auth()->user()->school_id ?? null;

            // Add branch_id from authenticated user
            $user->branch_id         = auth()->user()->branch_id ?? 1;

            $user->upload_id         = $this->UploadImageCreate($request->image, 'backend/uploads/students');
            $user->uuid              = Str::uuid();
            $user->save();

            // LOG CHECKPOINT: Student user created successfully
            Log::info('CHECKPOINT: Student user created', [
                'user_id' => $user->id,
                'user_name' => $user->name
            ]);

            $row                       = new $this->model;
            $row->user_id              = $user->id;
            $row->first_name           = $request->first_name;
            $row->last_name            = $request->last_name;
            $row->mobile               = $request->mobile;
            $row->image_id             = $user->upload_id;
            $row->email                = $request->email;
            $row->dob                  = $request->date_of_birth != "" ? $request->date_of_birth : NULL;
            $row->gender_id            = $request->gender != "" ? $request->gender :  NULL;
            $row->admission_date       = $request->admission_date;
            $row->parent_guardian_id   = $parentGuardianId;
            $row->student_category_id  = $request->category != "" ? $request->category :  NULL;

            $row->previous_school = $request->previous_school ?? 0;
            $row->previous_school_info = $request->previous_school_info;
            $row->previous_school_image_id = $this->UploadImageCreate($request->previous_school_image, 'backend/uploads/students');
            $row->place_of_birth = $request->place_of_birth;
            $row->residance_address = $request->residance_address;
            $row->grade = $request->grade; // Add the grade field assignment

            $row->status               = $request->status;
            $row->siblings_discount   = $request->siblings_discount;
            $row->upload_documents     = $this->uploadDocuments($request);
            $row->save();

            // LOG CHECKPOINT: Student model saved successfully
            Log::info('CHECKPOINT: Student model saved', [
                'student_id' => $row->id,
                'student_name' => $row->first_name . ' ' . $row->last_name,
                'parent_guardian_id' => $row->parent_guardian_id
            ]);

            $session_class                      = new SessionClassStudent();
            $session_class->session_id          = setting('session');
            $session_class->classes_id          = $request->class;
            $session_class->section_id          = $request->section != "" ? $request->section :  NULL;
            $session_class->shift_id            = $request->shift != "" ? $request->shift :  NULL;
            $session_class->student_id          = $row->id;
            $session_class->roll                = NULL; // Roll number field removed
            $session_class->save();

            // LOG CHECKPOINT: Session class created successfully
            Log::info('CHECKPOINT: Session class student created', [
                'session_class_id' => $session_class->id,
                'student_id' => $row->id,
                'class_id' => $request->class,
                'section_id' => $request->section
            ]);

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

            // COMPREHENSIVE ERROR LOGGING
            Log::error('========== STUDENT STORE OPERATION FAILED ==========', [
                'error_message' => $th->getMessage(),
                'error_type' => get_class($th),
                'error_file' => $th->getFile(),
                'error_line' => $th->getLine(),
                'parent_creation_mode' => $request->input('parent_creation_mode', 'existing'),
                'parent_guardian_id' => $parentGuardianId ?? null,
                'request_data' => [
                    'student_info' => [
                        'first_name' => $request->first_name ?? null,
                        'last_name' => $request->last_name ?? null,
                        'mobile' => $request->mobile ?? null,
                        'email' => $request->email ?? null,
                        'grade' => $request->grade ?? null,
                        'class' => $request->class ?? null,
                        'section' => $request->section ?? null,
                        'admission_date' => $request->admission_date ?? null,
                        'date_of_birth' => $request->date_of_birth ?? null,
                        'status' => $request->status ?? null,
                    ],
                    'parent_info' => [
                        'mode' => $request->parent_creation_mode ?? 'existing',
                        'existing_parent_id' => $request->parent ?? null,
                        'new_parent_name' => $request->new_parent_name ?? null,
                        'new_parent_mobile' => $request->new_parent_mobile ?? null,
                        'new_parent_relation' => $request->new_parent_relation ?? null,
                    ],
                ],
                'stack_trace' => $th->getTraceAsString()
            ]);

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
            $user->date_of_birth      = $request->date_of_birth != "" ? $request->date_of_birth : NULL;
            $user->upload_id          = $this->UploadImageUpdate($request->image, 'backend/uploads/students', $user->upload_id);
            $user->permissions        = $role->permissions;
            $user->username           = $request->username != "" ? $request->username : NULL;
            $user->save();

            $row->first_name           = $request->first_name;
            $row->last_name            = $request->last_name;
            $row->mobile               = $request->mobile;
            $row->image_id             = $user->upload_id;
            $row->email                = $request->email;
            $row->dob                  = $request->date_of_birth != "" ? $request->date_of_birth : NULL;
            $row->gender_id            = $request->gender != "" ? $request->gender :  NULL;
            $row->admission_date       = $request->admission_date != "" ? $request->admission_date : NULL;
            $row->parent_guardian_id   = $request->parent != "" ? $request->parent :  NULL;
            $row->student_category_id  = $request->category != "" ? $request->category :  NULL;

            $row->previous_school = $request->previous_school ?? 0;
            $row->previous_school_info = $request->previous_school ? $request->previous_school_info : null;
            $row->previous_school_image_id = $request->previous_school ? $this->UploadImageCreate($request->previous_school_image, 'backend/uploads/students') : null;
            $row->place_of_birth = $request->place_of_birth;
            $row->residance_address = $request->residance_address;
            $row->grade = $request->grade; // Update the grade field

            $row->status               = $request->status;
            $row->upload_documents     = $row->upload_documents ?? $this->uploadDocuments($request, $row->upload_documents);
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

    /**
     * Get students filtered by grades
     */
    public function getStudentsByGrades(array $grades, array $options = [])
    {
        $query = SessionClassStudent::query()
            ->where('session_id', setting('session'))
            ->whereHas('student', function($q) use ($grades) {
                $q->whereIn('grade', $grades)
                  ->where('status', \App\Enums\Status::ACTIVE);
            })
            ->with(['student.upload', 'student.user', 'class', 'section']);

        // Apply additional filters if provided
        if (!empty($options['classes'])) {
            $query->whereIn('classes_id', $options['classes']);
        }

        if (!empty($options['sections'])) {
            $query->whereIn('section_id', $options['sections']);
        }

        if (!empty($options['gender'])) {
            $query->whereHas('student', function($q) use ($options) {
                $q->where('gender_id', $options['gender']);
            });
        }

        if (!empty($options['keyword'])) {
            $query->whereHas('student', function($q) use ($options) {
                $q->where('first_name', 'LIKE', "%{$options['keyword']}%")
                  ->orWhere('last_name', 'LIKE', "%{$options['keyword']}%");
            });
        }

        return $query->latest()->paginate($options['per_page'] ?? Settings::PAGINATE);
    }

    /**
     * Search students with grade-based filtering
     */
    public function searchStudentsWithGrades($request)
    {
        $query = SessionClassStudent::query()
            ->where('session_id', setting('session'));

        // Filter by class
        if ($request->class != "") {
            $query->where('classes_id', $request->class);
        }

        // Filter by section
        if ($request->section != "") {
            $query->where('section_id', $request->section);
        }

        // Filter by grades
        if (!empty($request->grades)) {
            $query->whereHas('student', function($q) use ($request) {
                $q->whereIn('grade', $request->grades);
            });
        }

        // Filter by academic level (derived from grade)
        if (!empty($request->academic_level)) {
            $gradesByLevel = [
                'kg' => ['KG-1', 'KG-2'],
                'primary' => ['Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8'],
                'secondary' => ['Form1', 'Form2', 'Form3', 'Form4']
            ];

            if (isset($gradesByLevel[$request->academic_level])) {
                $query->whereHas('student', function($q) use ($gradesByLevel, $request) {
                    $q->whereIn('grade', $gradesByLevel[$request->academic_level]);
                });
            }
        }

        // Filter by keyword
        if ($request->keyword != "") {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('first_name', 'LIKE', "%{$request->keyword}%")
                  ->orWhere('last_name', 'LIKE', "%{$request->keyword}%")
                  ->orWhere('dob', 'LIKE', "%{$request->keyword}%");
            });
        }

        // Filter by gender
        if (!empty($request->gender)) {
            $query->whereHas('student', function($q) use ($request) {
                $q->where('gender_id', $request->gender);
            });
        }

        return $query->with(['student.upload', 'student.user', 'class', 'section'])
                     ->paginate(Settings::PAGINATE);
    }

    /**
     * Get grade distribution statistics
     */
    public function getGradeDistribution(): array
    {
        $currentSession = setting('session');

        if (!$currentSession) {
            return [];
        }

        $distribution = SessionClassStudent::where('session_id', $currentSession)
            ->whereHas('student', function($query) {
                $query->where('status', \App\Enums\Status::ACTIVE);
            })
            ->with('student')
            ->get()
            ->pluck('student')
            ->filter()
            ->groupBy('grade')
            ->map(function($students, $grade) {
                $gradeLabel = $grade ?: 'Not Set';
                $academicLevel = 'primary'; // default

                if ($grade) {
                    $student = new Student(['grade' => $grade]);
                    $academicLevel = $student->getAcademicLevelFromGrade();
                }

                return [
                    'grade' => $gradeLabel,
                    'academic_level' => $academicLevel,
                    'count' => $students->count(),
                    'percentage' => 0, // Will be calculated after getting totals
                ];
            })
            ->values()
            ->toArray();

        // Calculate percentages
        $totalStudents = array_sum(array_column($distribution, 'count'));
        if ($totalStudents > 0) {
            $distribution = array_map(function($item) use ($totalStudents) {
                $item['percentage'] = round(($item['count'] / $totalStudents) * 100, 2);
                return $item;
            }, $distribution);
        }

        return $distribution;
    }

    /**
     * Get students count by specific grade
     */
    public function getStudentCountByGrade(string $grade): int
    {
        return SessionClassStudent::where('session_id', setting('session'))
            ->whereHas('student', function($query) use ($grade) {
                $query->where('grade', $grade)
                      ->where('status', \App\Enums\Status::ACTIVE);
            })
            ->count();
    }

    /**
     * Get students by academic level using grade
     */
    public function getStudentsByAcademicLevel(string $academicLevel, array $options = [])
    {
        $gradesByLevel = [
            'kg' => ['KG-1', 'KG-2'],
            'primary' => ['Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8'],
            'secondary' => ['Form1', 'Form2', 'Form3', 'Form4']
        ];

        if (!isset($gradesByLevel[$academicLevel])) {
            return collect();
        }

        return $this->getStudentsByGrades($gradesByLevel[$academicLevel], $options);
    }

    /**
     * Get students who don't have a grade assigned
     */
    public function getStudentsWithoutGrade(array $options = [])
    {
        $query = SessionClassStudent::query()
            ->where('session_id', setting('session'))
            ->whereHas('student', function($q) {
                $q->where('status', \App\Enums\Status::ACTIVE)
                  ->where(function($subQ) {
                      $subQ->whereNull('grade')->orWhere('grade', '');
                  });
            })
            ->with(['student.upload', 'student.user', 'class', 'section']);

        return $query->latest()->paginate($options['per_page'] ?? Settings::PAGINATE);
    }

    /**
     * Bulk update grades for students
     */
    public function bulkUpdateGrades(array $studentGrades): array
    {
        $results = ['success' => [], 'errors' => []];

        DB::beginTransaction();
        try {
            foreach ($studentGrades as $studentId => $grade) {
                $student = $this->model->find($studentId);

                if (!$student) {
                    $results['errors'][] = "Student with ID {$studentId} not found";
                    continue;
                }

                // Validate grade
                if (!in_array($grade, Student::getAllGrades())) {
                    $results['errors'][] = "Invalid grade '{$grade}' for student {$student->full_name}";
                    continue;
                }

                $student->update(['grade' => $grade]);
                $results['success'][] = "Updated grade for {$student->full_name} to {$grade}";
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            $results['errors'][] = "Transaction failed: " . $e->getMessage();
        }

        return $results;
    }

    /**
     * Get grade options for forms
     */
    public function getGradeOptions(): array
    {
        return Student::getGradeOptions();
    }

    /**
     * Get AJAX data for DataTables server-side processing
     */
    public function getAjaxData($request)
    {
        // DataTables parameters
        $draw = intval($request->input('draw'));
        $start = intval($request->input('start'));
        $length = intval($request->input('length'));
        $searchValue = $request->input('search.value');
        $orderColumn = $request->input('order.0.column');
        $orderDir = $request->input('order.0.dir', 'asc');

        // Custom filter parameters
        $classFilter = $request->input('class_id');
        $sectionFilter = $request->input('section_id');
        $keywordFilter = $request->input('keyword');

        // Base query
        $query = SessionClassStudent::query()
            ->where('session_id', setting('session'))
            ->with([
                'student.upload',
                'student.user',
                'student.gender',
                'student.parent',
                'class',
                'section',
                'student.feesPayments' => function($query) {
                    $academicYearId = session('academic_year_id') ?? \App\Models\Session::active()->value('id');
                    $query->where('academic_year_id', $academicYearId);
                },
                'student.studentServices.feeType'
            ]);

        // Apply filters
        if (!empty($classFilter)) {
            $query->where('classes_id', $classFilter);
        }

        if (!empty($sectionFilter)) {
            $query->where('section_id', $sectionFilter);
        }

        if (!empty($keywordFilter)) {
            $query->whereHas('student', function($q) use ($keywordFilter) {
                $q->where('first_name', 'LIKE', "%{$keywordFilter}%")
                  ->orWhere('last_name', 'LIKE', "%{$keywordFilter}%")
                  ->orWhere('dob', 'LIKE', "%{$keywordFilter}%");
            });
        }

        // Apply DataTables global search
        if (!empty($searchValue)) {
            $query->whereHas('student', function($q) use ($searchValue) {
                $q->where('first_name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('last_name', 'LIKE', "%{$searchValue}%")
                  ->orWhere('mobile', 'LIKE', "%{$searchValue}%")
                  ->orWhere('email', 'LIKE', "%{$searchValue}%");
            });
        }

        // Count records before pagination
        $totalRecords = SessionClassStudent::where('session_id', setting('session'))->count();
        $filteredRecords = $query->count();

        // Apply ordering
        $columns = ['id', 'student.first_name', 'student.grade', 'class.name', 'student.parent.guardian_name', 'student.dob', 'student.gender.name', 'student.mobile', 'outstanding_amount', 'student.status'];
        if (isset($columns[$orderColumn])) {
            $orderField = $columns[$orderColumn];
            if (strpos($orderField, '.') !== false) {
                // Handle relationship ordering
                $parts = explode('.', $orderField);
                if (count($parts) == 2) {
                    $query->orderByRaw("(SELECT {$parts[1]} FROM " . $this->getTableName($parts[0]) . " WHERE " . $this->getForeignKey($parts[0]) . " = session_class_students.student_id LIMIT 1) {$orderDir}");
                }
            } else {
                $query->orderBy($orderField, $orderDir);
            }
        } else {
            $query->latest();
        }

        // Apply pagination
        $students = $query->offset($start)->limit($length)->get();

        // Calculate outstanding amounts
        $this->calculateOutstandingAmountsForAjax($students);

        // Format data for DataTables
        $data = [];
        $key = $start + 1;

        foreach ($students as $row) {
            $student = $row->student;

            if (!$student) continue;

            // Generate avatar HTML
            $avatarHtml = '';
            if ($student->upload) {
                $avatarHtml = '<img src="' . asset($student->upload->path) . '" alt="' . $student->first_name . ' ' . $student->last_name . '" style="width: 32px; height: 32px; object-fit: cover; border-radius: 50%;">';
            } else {
                $avatarHtml = generateStudentAvatar($student->first_name, $student->last_name, '32px');
            }

            // Generate student name with avatar
            $studentNameHtml = '<a href="' . route('student.show', $student->id) . '">
                <div class="user-card">
                    <div class="user-avatar">' . $avatarHtml . '</div>
                    <div class="user-info">' . $student->first_name . ' ' . $student->last_name . '</div>
                </div>
            </a>';

            // Generate outstanding amount HTML
            $outstandingHtml = '';
            if (isset($row->outstanding_amount) && $row->outstanding_amount > 0) {
                $outstandingHtml = '<span class="text-danger fw-bold">' . setting('currency_symbol') . ' ' . number_format($row->outstanding_amount, 2) . '</span>';
            } else {
                $outstandingHtml = '<span class="text-success small">' . ___('fees.no_dues') . '</span>';
            }

            // Generate status HTML
            $statusHtml = '';
            if ($student->status == \App\Enums\Status::ACTIVE) {
                $statusHtml = '<span class="badge-basic-success-text">' . ___('common.active') . '</span>';
            } else {
                $statusHtml = '<span class="badge-basic-danger-text">' . ___('common.inactive') . '</span>';
            }

            // Generate actions HTML
            $actionsHtml = '';
            if (hasPermission('student_update') || hasPermission('student_delete')) {
                $actionsHtml = '<div class="dropdown dropdown-action">
                    <button type="button" class="btn-dropdown" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="fa-solid fa-ellipsis"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">';

                if (hasPermission('student_update')) {
                    $actionsHtml .= '<li><a class="dropdown-item" href="' . route('student.edit', $row->id) . '"><span class="icon mr-8"><i class="fa-solid fa-pen-to-square"></i></span>' . ___('common.edit') . '</a></li>';
                }

                if (isset($row->outstanding_amount) && $row->outstanding_amount > 0 && hasPermission('fees_collect_update')) {
                    $actionsHtml .= '<li><a class="dropdown-item" href="javascript:void(0);" data-bs-toggle="modal" data-bs-target="#modalCustomizeWidth" onclick="openFeeCollectionModal(' . $student->id . ', \'' . $student->first_name . ' ' . $student->last_name . '\')"><span class="icon mr-8"><i class="fa-solid fa-credit-card text-success"></i></span>' . ___('common.pay') . '</a></li>';
                }

                if (hasPermission('student_delete')) {
                    $actionsHtml .= '<li><a class="dropdown-item" href="javascript:void(0);" onclick="delete_row(\'student/delete\', ' . $row->student_id . ')"><span class="icon mr-8"><i class="fa-solid fa-trash-can"></i></span><span>' . ___('common.delete') . '</span></a></li>';
                }

                $actionsHtml .= '</ul></div>';
            }

            $data[] = [
                $key++,
                $studentNameHtml,
                $student->grade ?? 'Not Set',
                ($row->class->name ?? '') . ' (' . ($row->section->name ?? '') . ')',
                $student->parent->guardian_name ?? '',
                dateFormat($student->dob),
                $student->gender->name ?? '',
                $student->mobile ?? '',
                $outstandingHtml,
                $statusHtml,
                $actionsHtml
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
     * Calculate outstanding amounts for AJAX data
     */
    private function calculateOutstandingAmountsForAjax($students)
    {
        $academicYearId = session('academic_year_id');

        if (!$academicYearId) {
            $academicYearId = \App\Models\Session::active()->value('id');
        }

        if (!$academicYearId) {
            return;
        }

        foreach ($students as $row) {
            $student = $row->student;

            if (!$student) {
                $row->outstanding_amount = 0;
                continue;
            }

            try {
                if ($student->hasActiveServices($academicYearId)) {
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
                    $row->outstanding_amount = 0;
                }
            } catch (\Exception $e) {
                \Log::warning('Error calculating outstanding amount for student in AJAX', [
                    'student_id' => $student->id,
                    'error' => $e->getMessage()
                ]);
                $row->outstanding_amount = 0;
            }
        }
    }

    /**
     * Helper methods for ordering relationships
     */
    private function getTableName($relation)
    {
        switch ($relation) {
            case 'student':
                return 'students';
            case 'class':
                return 'classes';
            case 'section':
                return 'sections';
            default:
                return $relation . 's';
        }
    }

    private function getForeignKey($relation)
    {
        switch ($relation) {
            case 'student':
                return 'id';
            case 'class':
                return 'classes_id';
            case 'section':
                return 'section_id';
            default:
                return $relation . '_id';
        }
    }

    /**
     * Get grade-based statistics for dashboard
     */
    public function getGradeStatistics(): array
    {
        $distribution = $this->getGradeDistribution();
        $totalStudents = array_sum(array_column($distribution, 'count'));

        $stats = [
            'total_students' => $totalStudents,
            'by_academic_level' => [],
            'by_grade' => $distribution,
        ];

        // Group by academic level
        $byLevel = [];
        foreach ($distribution as $item) {
            $level = $item['academic_level'];
            if (!isset($byLevel[$level])) {
                $byLevel[$level] = ['count' => 0, 'grades' => []];
            }
            $byLevel[$level]['count'] += $item['count'];
            $byLevel[$level]['grades'][] = $item['grade'];
        }

        foreach ($byLevel as $level => $data) {
            $stats['by_academic_level'][$level] = [
                'count' => $data['count'],
                'percentage' => $totalStudents > 0 ? round(($data['count'] / $totalStudents) * 100, 2) : 0,
                'grades' => $data['grades']
            ];
        }

        return $stats;
    }
}
