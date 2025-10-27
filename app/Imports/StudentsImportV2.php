<?php

namespace App\Imports;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Str;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToModel;
use App\Models\StudentInfo\ParentGuardian;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use App\Models\StudentInfo\SessionClassStudent;
use App\Services\StudentServiceManager;
use Illuminate\Support\Facades\DB;

class StudentsImportV2 implements ToModel, WithHeadingRow, WithValidation
{
    protected $grade;
    protected $class;
    protected $section;
    protected $serviceManager;
    protected $rowNumber = 1; // Start at 1 (header is row 0)

    public function __construct($grade, $class, $section)
    {
        $this->grade = $grade;
        $this->class = $class;
        $this->section = $section;
        $this->serviceManager = app(StudentServiceManager::class);
    }

    /**
     * Validation rules for import
     */
    public function rules(): array
    {
        return [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'parent_mobile' => 'required|string|max:255',

            // Optional fields
            'shift' => 'nullable|exists:shifts,id',
            'gender' => 'nullable|exists:genders,id',
            'category' => 'nullable|exists:student_categories,id',
            'mobile' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255|unique:users,email',
            'username' => 'nullable|string|max:255|unique:users,username',
            'date_of_birth' => 'nullable|date',
            'admission_date' => 'nullable|date',
            'parent_name' => 'nullable|string|max:255',
            'parent_relation' => 'nullable|in:Father,Mother,Guardian,Other',
            'fee_services' => 'nullable|string', // Comma-separated service IDs
        ];
    }

    /**
     * Custom validation messages
     */
    public function customValidationMessages()
    {
        return [
            'first_name.required' => 'First name is required (row :row)',
            'last_name.required' => 'Last name is required (row :row)',
            'parent_mobile.required' => 'Parent mobile is required (row :row)',
            'email.unique' => 'Email already exists in system (row :row)',
            'username.unique' => 'Username already exists in system (row :row)',
            'shift.exists' => 'Invalid shift ID (row :row)',
            'gender.exists' => 'Invalid gender ID (row :row)',
            'category.exists' => 'Invalid category ID (row :row)',
        ];
    }

    /**
     * Prepare data for validation - cast numeric values to strings
     * This handles cases where Excel converts numeric-looking strings to numbers
     */
    public function prepareForValidation($data, $index)
    {
        // Cast numeric fields to strings to handle Excel auto-conversion
        $prepared = [];

        // Always cast parent_mobile to string (required field)
        if (isset($data['parent_mobile'])) {
            $prepared['parent_mobile'] = (string) $data['parent_mobile'];
        }

        // Cast optional numeric fields to strings if present
        if (isset($data['mobile'])) {
            $prepared['mobile'] = (string) $data['mobile'];
        }

        if (isset($data['shift']) && $data['shift'] !== null && $data['shift'] !== '') {
            $prepared['shift'] = (string) $data['shift'];
        }

        if (isset($data['gender']) && $data['gender'] !== null && $data['gender'] !== '') {
            $prepared['gender'] = (string) $data['gender'];
        }

        if (isset($data['category']) && $data['category'] !== null && $data['category'] !== '') {
            $prepared['category'] = (string) $data['category'];
        }

        if (isset($data['fee_services']) && $data['fee_services'] !== null && $data['fee_services'] !== '') {
            $prepared['fee_services'] = (string) $data['fee_services'];
        }

        // Merge prepared data with original data
        return array_merge($data, $prepared);
    }

    /**
     * Process each row from the Excel file
     */
    public function model(array $row)
    {
        $this->rowNumber++;

        DB::beginTransaction();
        try {
            // 1. Handle parent creation or lookup
            $parent = $this->handleParent($row);

            // 2. Create user account for student
            $user = $this->createStudentUser($row);

            // 3. Create student record
            $student = $this->createStudent($row, $user->id, $parent->id);

            // 4. Create session class student record
            $this->createSessionClassStudent($student->id, $row);

            // 5. Auto-subscribe to mandatory services based on grade
            $this->autoSubscribeMandatoryServices($student);

            // 6. Handle optional fee services if provided
            $this->handleFeeServices($student, $row);

            DB::commit();

            Log::info('Student imported successfully', [
                'row' => $this->rowNumber,
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'grade' => $this->grade,
                'parent_id' => $parent->id
            ]);

            return $student;

        } catch (\Exception $e) {
            DB::rollback();

            Log::error('Student import failed', [
                'row' => $this->rowNumber,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw new \Exception("Row {$this->rowNumber}: " . $e->getMessage());
        }
    }

    /**
     * Handle parent creation or lookup by mobile number
     */
    protected function handleParent(array $row): ParentGuardian
    {
        // Lookup parent by mobile number
        $parent = ParentGuardian::where('guardian_mobile', $row['parent_mobile'])->first();

        if ($parent) {
            Log::info('Using existing parent', [
                'parent_id' => $parent->id,
                'mobile' => $row['parent_mobile']
            ]);
            return $parent;
        }

        // Create new parent if not found
        $parentRole = Role::find(7); // Parent role ID is 7

        // Create user account for parent
        $parentUser = new User();
        $parentUser->name = $row['parent_name'] ?? ($row['first_name'] . ' ' . $row['last_name'] . ' Guardian');
        $parentUser->email = $this->generateUniqueEmail('guardian');
        $parentUser->phone = $row['parent_mobile'];
        $parentUser->password = Hash::make('123456'); // Default password
        $parentUser->email_verified_at = now();
        $parentUser->role_id = $parentRole->id;
        $parentUser->permissions = $parentRole->permissions;
        $parentUser->username = $this->generateUniqueUsername('guardian');
        $parentUser->uuid = Str::uuid();
        $parentUser->save();

        // Create parent guardian record
        $parent = new ParentGuardian();
        $parent->user_id = $parentUser->id;
        $parent->guardian_name = $row['parent_name'] ?? ($row['first_name'] . ' ' . $row['last_name'] . ' Guardian');
        $parent->guardian_email = $parentUser->email;
        $parent->guardian_mobile = $row['parent_mobile'];
        $parent->guardian_relation = $row['parent_relation'] ?? 'Guardian';
        $parent->guardian_profession = null;
        $parent->guardian_address = null;
        $parent->status = 1;
        $parent->save();

        Log::info('Created new parent', [
            'parent_id' => $parent->id,
            'mobile' => $row['parent_mobile']
        ]);

        return $parent;
    }

    /**
     * Create user account for student
     */
    protected function createStudentUser(array $row): User
    {
        $studentRole = Role::find(6); // Student role ID is 6

        $user = new User();
        $user->name = $row['first_name'] . ' ' . $row['last_name'];
        $user->email = $row['email'] ?? null;
        $user->phone = $row['mobile'] ?? null;
        $user->password = Hash::make('123456'); // Default password
        $user->email_verified_at = now();
        $user->role_id = $studentRole->id;
        $user->permissions = $studentRole->permissions;
        $user->date_of_birth = $row['date_of_birth'] ?? null;
        $user->username = $row['username'] ?? $this->generateUniqueUsername('student');
        $user->uuid = Str::uuid();
        $user->save();

        return $user;
    }

    /**
     * Create student record
     */
    protected function createStudent(array $row, int $userId, int $parentId): Student
    {
        $student = new Student();
        $student->user_id = $userId;
        $student->first_name = $row['first_name'];
        $student->last_name = $row['last_name'];
        $student->mobile = $row['mobile'] ?? null;
        $student->email = $row['email'] ?? null;
        $student->dob = $row['date_of_birth'] ?? null;
        $student->gender_id = $row['gender'] ?? null;
        $student->admission_date = $row['admission_date'] ?? now();
        $student->parent_guardian_id = $parentId;
        $student->student_category_id = $row['category'] ?? null;
        $student->grade = $this->grade; // Use grade from form selection
        $student->status = 1; // Active status
        $student->save();

        return $student;
    }

    /**
     * Create session class student record
     */
    protected function createSessionClassStudent(int $studentId, array $row): void
    {
        $sessionClass = new SessionClassStudent();
        $sessionClass->session_id = setting('session');
        $sessionClass->classes_id = $this->class;
        $sessionClass->section_id = $this->section;
        $sessionClass->shift_id = $row['shift'] ?? null;
        $sessionClass->student_id = $studentId;
        $sessionClass->roll = null; // Roll number not used
        $sessionClass->save();
    }

    /**
     * Auto-subscribe to mandatory services based on grade
     */
    protected function autoSubscribeMandatoryServices(Student $student): void
    {
        try {
            $subscriptions = $this->serviceManager->autoSubscribeMandatoryServices(
                $student,
                setting('session')
            );

            Log::info('Auto-subscribed to mandatory services', [
                'student_id' => $student->id,
                'services_count' => $subscriptions->count()
            ]);
        } catch (\Exception $e) {
            // Log but don't fail import
            Log::warning('Failed to auto-subscribe to mandatory services', [
                'student_id' => $student->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle optional fee services if provided in Excel
     */
    protected function handleFeeServices(Student $student, array $row): void
    {
        if (empty($row['fee_services'])) {
            return;
        }

        // Parse comma-separated service IDs
        $serviceIds = array_filter(
            array_map('trim', explode(',', $row['fee_services']))
        );

        foreach ($serviceIds as $serviceId) {
            try {
                $feeType = \App\Models\Fees\FeesType::find($serviceId);

                if (!$feeType) {
                    Log::warning('Fee service not found', [
                        'service_id' => $serviceId,
                        'student_id' => $student->id
                    ]);
                    continue;
                }

                // Only allow optional services
                if ($feeType->is_mandatory_for_level) {
                    Log::warning('Cannot manually assign mandatory service', [
                        'service_id' => $serviceId,
                        'student_id' => $student->id
                    ]);
                    continue;
                }

                $this->serviceManager->subscribeToService($student, $feeType, [
                    'academic_year_id' => setting('session'),
                    'notes' => 'Assigned during bulk import'
                ]);

            } catch (\Exception $e) {
                Log::warning('Failed to assign optional service', [
                    'service_id' => $serviceId,
                    'student_id' => $student->id,
                    'error' => $e->getMessage()
                ]);
            }
        }
    }

    /**
     * Generate unique email address
     */
    protected function generateUniqueEmail(string $prefix): string
    {
        $timestamp = time();
        $random = rand(1000, 9999);
        return "{$prefix}_{$timestamp}_{$random}@school.local";
    }

    /**
     * Generate unique username
     */
    protected function generateUniqueUsername(string $prefix): string
    {
        do {
            $timestamp = time();
            $random = rand(1000, 9999);
            $username = "{$prefix}_{$timestamp}_{$random}";
        } while (User::where('username', $username)->exists());

        return $username;
    }
}
