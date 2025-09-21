<?php

namespace App\Models\StudentInfo;

use App\Models\User;
use Faker\Core\Blood;
use App\Models\Gender;
use App\Models\Upload;
use App\Models\Religion;
use App\Models\BaseModel;
use App\Models\BloodGroup;
use App\Models\Staff\Staff;
use App\Models\Academic\Shift;
use App\Models\Fees\FeesMaster;
use App\Models\Staff\Department;
use Modules\LiveChat\Entities\Message;
use App\Models\Fees\FeesAssignChildren;
use Illuminate\Database\Eloquent\Model;
use App\Models\Academic\SubjectAssignChildren;
use App\Models\AssignFeesDiscount;
use App\Models\Fees\FeesCollect;
use App\Models\StudentService;
use Modules\VehicleTracker\Entities\EnrollmentReport;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\VehicleTracker\Entities\StudentRouteEnrollment;
use PhpParser\Node\Expr\Assign;
use Illuminate\Support\Collection;

class Student extends BaseModel
{
    use HasFactory;

    protected $appends = ['full_name'];

    protected $fillable = [
        'user_id',
        'first_name',
        'last_name',
        'mobile',
        'email',
        'dob',
        'admission_date',
        'student_category_id',
        'grade', // New required grade field
        'gender_id',
        'category_id',
        'image_id',
        'parent_guardian_id',
        'upload_documents',
        'status',
        'siblings_discount',
        'previous_school',
        'previous_school_info',
        'previous_school_image_id',
        'place_of_birth',
        'residance_address'
    ];

    protected $casts = [
        'upload_documents' => 'array',
    ];

    public function routeEnroll()
    {
        return $this->hasOne(StudentRouteEnrollment::class, 'student_id', 'id');
    }

    public function staffs()
    {
        return $this->hasManyThrough(Staff::class, SubjectAssignChildren::class, 'student_id', 'staff_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function upload()
    {
        return $this->belongsTo(Upload::class, 'image_id', 'id');
    }

    public function scopeActive($query)
    {
        return $query->where('status', \App\Enums\Status::ACTIVE);
    }

    public function session_class_student()
    {
        return $this->belongsTo(SessionClassStudent::class, 'id', 'student_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }


    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }


    public function gender()
    {
        return $this->belongsTo(Gender::class, 'gender_id', 'id');
    }


    public function parent()
    {
        return $this->belongsTo(ParentGuardian::class, 'parent_guardian_id', 'id');
    }

    public function sessionStudentDetails()
    {
        return $this->belongsTo(SessionClassStudent::class, 'id', 'student_id');
    }

    public function studentCategory()
    {
        return $this->belongsTo(StudentCategory::class, 'student_category_id', 'id');
    }

    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'sender_id', 'user_id')->latest();
    }

    public function unreadMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'user_id')->where('is_seen', 0);
    }

    public function route()
    {
        return $this->hasOne(StudentRouteEnrollment::class, 'student_id', 'id');
    }

    public function pickupReport()
    {
        return $this->hasOne(EnrollmentReport::class, 'student_id', 'id')->where('type', 'pickup');
    }

    public function dropReport()
    {
        return $this->hasOne(EnrollmentReport::class, 'student_id', 'id')->where('type', 'drop');
    }


    public function feesAssignChild()
    {
        return $this->hasMany(FeesAssignChildren::class);
    }




    public function feesPayments()
    {
        return $this->hasMany(FeesCollect::class);
    }

    public function feesMasters()
    {
        return $this->hasManyThrough(
            FeesMaster::class,
            FeesAssignChildren::class,
            'student_id',       // Foreign key on FeesAssignChildren
            'id',               // Local key on feesMaster
            'id',               // Local key on Student
            'fees_master_id'    // Foreign key on FeesAssignChildren
        );
    }

    public function feesDiscounts()
    {
        return $this->hasManyThrough(
            AssignFeesDiscount::class,
            FeesAssignChildren::class,
            'student_id',       // Foreign key on FeesAssignChildren
            'fees_assign_children_id',               // Local key on feesMaster
            'id',               // Local key on Student
            'id'    // Foreign key on FeesAssignChildren
        );
    }

    // Enhanced Fee Processing System Relationships and Methods

    public function studentServices()
    {
        return $this->hasMany(StudentService::class, 'student_id');
    }

    public function activeServices(int $academicYearId = null)
    {
        $query = $this->studentServices()->active()->with('feeType');
        
        if ($academicYearId) {
            $query->forAcademicYear($academicYearId);
        } else {
            $query->forAcademicYear(session('academic_year_id'));
        }
        
        return $query;
    }

    public function getAcademicLevel(): string
    {
        // PRIORITY 1: Use grade field if available (new approach)
        if ($this->grade) {
            return $this->getAcademicLevelFromGrade();
        }

        // PRIORITY 2: Use explicit academic level from class
        if ($this->sessionStudentDetails?->class?->academic_level) {
            return $this->sessionStudentDetails->class->academic_level;
        }

        // PRIORITY 3: Fallback to legacy detection for backward compatibility
        $className = $this->sessionStudentDetails?->class?->name ?? '';

        // Try AcademicLevelConfig detection first
        if ($className) {
            $detectedLevel = \App\Models\AcademicLevelConfig::detectAcademicLevel($className);
            if ($detectedLevel) {
                return $detectedLevel;
            }
        }

        // Final fallback: Enhanced detection with form-based and numeric parsing
        // Priority 1: Check for Form-based classes (Form 1-4 = secondary)
        if (preg_match('/form\s*(\d+)/i', $className, $matches)) {
            $formNumber = (int) $matches[1];
            return match(true) {
                $formNumber >= 1 && $formNumber <= 4 => 'secondary',
                $formNumber >= 5 && $formNumber <= 6 => 'high_school',
                default => 'primary'
            };
        }

        // Priority 2: Check for numeric classes (1-8 = primary)
        if (preg_match('/(\d+)/', $className, $matches)) {
            $classNumber = (int) $matches[1];
            return match(true) {
                $classNumber >= 1 && $classNumber <= 8 => 'primary',  // Classes 1-8 = Primary
                $classNumber >= 9 && $classNumber <= 10 => 'secondary',
                $classNumber >= 11 && $classNumber <= 12 => 'high_school',
                $classNumber < 1 => 'kg',
                default => 'primary'
            };
        }

        // Priority 3: Safe default
        return 'primary';
    }

    /**
     * Get academic level directly from grade field
     */
    public function getAcademicLevelFromGrade(): string
    {
        if (!$this->grade) {
            return 'primary'; // Default fallback
        }

        return match($this->grade) {
            'KG-1', 'KG-2' => 'kg',
            'Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8' => 'primary',
            'Form1', 'Form2', 'Form3', 'Form4' => 'secondary',
            default => 'primary'
        };
    }

    /**
     * Get all available grade options grouped by academic level
     */
    public static function getGradeOptions(): array
    {
        return [
            'kg' => [
                'KG-1' => 'KG-1 (Kindergarten 1)',
                'KG-2' => 'KG-2 (Kindergarten 2)',
            ],
            'primary' => [
                'Grade1' => 'Grade 1',
                'Grade2' => 'Grade 2',
                'Grade3' => 'Grade 3',
                'Grade4' => 'Grade 4',
                'Grade5' => 'Grade 5',
                'Grade6' => 'Grade 6',
                'Grade7' => 'Grade 7',
                'Grade8' => 'Grade 8',
            ],
            'secondary' => [
                'Form1' => 'Form 1',
                'Form2' => 'Form 2',
                'Form3' => 'Form 3',
                'Form4' => 'Form 4',
            ],
        ];
    }

    /**
     * Get flat array of all grade options
     */
    public static function getAllGrades(): array
    {
        return [
            'KG-1', 'KG-2', 'Grade1', 'Grade2', 'Grade3', 'Grade4',
            'Grade5', 'Grade6', 'Grade7', 'Grade8', 'Form1', 'Form2', 'Form3', 'Form4'
        ];
    }

    /**
     * Scope to filter students by grade
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }

    /**
     * Scope to filter students by multiple grades
     */
    public function scopeByGrades($query, array $grades)
    {
        return $query->whereIn('grade', $grades);
    }

    /**
     * Scope to filter students by academic level using grade
     */
    public function scopeByAcademicLevel($query, string $academicLevel)
    {
        $gradesByLevel = [
            'kg' => ['KG-1', 'KG-2'],
            'primary' => ['Grade1', 'Grade2', 'Grade3', 'Grade4', 'Grade5', 'Grade6', 'Grade7', 'Grade8'],
            'secondary' => ['Form1', 'Form2', 'Form3', 'Form4']
        ];

        if (!isset($gradesByLevel[$academicLevel])) {
            return $query->whereRaw('1 = 0'); // Return empty result
        }

        return $query->whereIn('grade', $gradesByLevel[$academicLevel]);
    }

    public function getOutstandingFees(int $academicYearId = null): Collection
    {
        $academicYearId = $academicYearId ?? session('academic_year_id');
        
        // Get all active services for the academic year
        $services = $this->activeServices($academicYearId)->get();
        
        // Filter out services that have been fully paid
        return $services->filter(function ($service) use ($academicYearId) {
            $paidAmount = $this->feesPayments()
                ->where('fee_type_id', $service->fee_type_id)
                ->where('academic_year_id', $academicYearId)
                ->whereNotNull('payment_method')
                ->sum('amount');
                
            return $paidAmount < $service->final_amount;
        });
    }

    public function getTotalServiceFees(int $academicYearId = null): float
    {
        return $this->activeServices($academicYearId)->get()->sum('final_amount');
    }

    public function getTotalOriginalFees(int $academicYearId = null): float
    {
        return $this->activeServices($academicYearId)->get()->sum('amount');
    }

    public function getDiscountedAmount(int $academicYearId = null): float
    {
        $services = $this->activeServices($academicYearId)->get();
        
        return $services->sum(function ($service) {
            return $service->amount - $service->final_amount;
        });
    }

    public function getOutstandingAmount(int $academicYearId = null): float
    {
        return $this->getOutstandingFees($academicYearId)->sum('final_amount');
    }

    public function hasActiveServices(int $academicYearId = null): bool
    {
        return $this->activeServices($academicYearId)->exists();
    }

    public function getServicesByCategory(string $category, int $academicYearId = null): Collection
    {
        return $this->activeServices($academicYearId)
                    ->byCategory($category)
                    ->get();
    }

    public function hasServiceType(int $feeTypeId, int $academicYearId = null): bool
    {
        return $this->activeServices($academicYearId)
                    ->where('fee_type_id', $feeTypeId)
                    ->exists();
    }

    public function getServicesSummary(int $academicYearId = null): array
    {
        $services = $this->activeServices($academicYearId)->get();

        return [
            'total_services' => $services->count(),
            'total_original_amount' => $services->sum('amount'),
            'total_final_amount' => $services->sum('final_amount'),
            'total_discount' => $services->sum(function ($service) {
                return $service->amount - $service->final_amount;
            }),
            'services_by_category' => $services->groupBy('feeType.category')->map(function ($categoryServices) {
                return [
                    'count' => $categoryServices->count(),
                    'total_amount' => $categoryServices->sum('final_amount'),
                    'services' => $categoryServices->pluck('feeType.name')->toArray()
                ];
            })->toArray()
        ];
    }
}
