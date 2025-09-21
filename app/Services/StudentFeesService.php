<?php

namespace App\Services;

use App\Traits\CommonHelperTrait;
use App\Traits\ApiReturnFormatTrait;
use App\Models\Fees\FeesAssignChildren;
use App\Models\Fees\FeesCollect;
use App\Models\Fees\FeesType;
use App\Models\StudentInfo\Student;
use App\Models\StudentService;
use Modules\MultiBranch\Entities\Branch;
use Srmklive\PayPal\Services\ExpressCheckout;
use App\Repositories\Fees\FeesCollectRepository;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentFeesService
{
    use CommonHelperTrait;
    use ApiReturnFormatTrait;
    private $feesCollectRepository;

    function __construct(FeesCollectRepository $feesCollectRepository)
    { 
        $this->feesCollectRepository = $feesCollectRepository; 
    }

    public function payPalPaymentSuccess($request, $success_url, $cancel_url)
    {
        loadPayPalCredentials();
        
        try {
            $provider   = new ExpressCheckout;
            $token      = $request->token;
            $PayerID    = $request->PayerID;
            $response   = $provider->getExpressCheckoutDetails($token);

            $invoiceID  = $response['INVNUM'] ?? uniqid();
            $data       = $this->feesCollectRepository->paypalOrderData($invoiceID, $success_url, $cancel_url);
            $response   = $provider->doExpressCheckoutPayment($data, $token, $PayerID);

            $feesAssignChildren = optional(FeesAssignChildren::with('feesMaster')->where('id', session()->get('FeesAssignChildrenID'))->first());

            if ($feesAssignChildren && $response['PAYMENTINFO_0_TRANSACTIONID']) {
                $this->feesCollectRepository->feeCollectStoreByPaypal($response, $feesAssignChildren);
            }

            session()->forget('FeesAssignChildrenID');

            return true;

        } catch (\Throwable $th) {
            return false;
        }
    }

    /**
     * Get active students for branch with optional filters
     */
    public function getActiveStudentsForBranch(int $branchId, array $filters = []): Collection
    {
        $query = Student::active()
            ->where('branch_id', $branchId)
            ->with(['sessionStudentDetails.class', 'sessionStudentDetails.section', 'gender']);

        // Apply class filter
        if (isset($filters['class_id'])) {
            $query->whereHas('sessionStudentDetails', function($q) use ($filters) {
                $q->where('class_id', $filters['class_id']);
            });
        }

        // Apply section filter
        if (isset($filters['section_id'])) {
            $query->whereHas('sessionStudentDetails', function($q) use ($filters) {
                $q->where('section_id', $filters['section_id']);
            });
        }

        // Apply gender filter
        if (isset($filters['gender_id'])) {
            $query->where('gender_id', $filters['gender_id']);
        }

        // Apply grade filter
        if (isset($filters['grade'])) {
            $query->byGrade($filters['grade']);
        }

        // Apply academic level filter
        if (isset($filters['academic_level'])) {
            $query->byAcademicLevel($filters['academic_level']);
        }

        return $query->get();
    }

    /**
     * Create fee service for student with branch context
     */
    public function createStudentFeeService(
        int $studentId,
        int $feeTypeId,
        int $academicYearId,
        array $options = []
    ): StudentService {
        $student = Student::with('branch')->findOrFail($studentId);
        $feeType = FeesType::findOrFail($feeTypeId);

        // Validate branch is active
        if (!$student->branch?->isActive()) {
            throw new \Exception("Student's branch is not active");
        }

        // Validate fee type is applicable for student's academic level
        if (!$feeType->isApplicableFor($student->getAcademicLevel())) {
            throw new \Exception("Fee type is not applicable for student's academic level");
        }

        return StudentService::create([
            'student_id' => $studentId,
            'fee_type_id' => $feeTypeId,
            'academic_year_id' => $academicYearId,
            'amount' => $feeType->amount,
            'discount_type' => $options['discount_type'] ?? 'none',
            'discount_value' => $options['discount_value'] ?? 0,
            'final_amount' => $this->calculateFinalAmount($feeType->amount, $options),
            'subscription_date' => now(),
            'is_active' => true,
            'notes' => $options['notes'] ?? null,
            'created_by' => auth()->id(),
        ]);
    }

    /**
     * Get student fee services with branch context
     */
    public function getStudentFeeServices(int $studentId, int $academicYearId = null): Collection
    {
        $student = Student::with('branch')->findOrFail($studentId);

        if (!$student->branch?->isActive()) {
            throw new \Exception("Student's branch is not active");
        }

        return $student->activeServices($academicYearId)->get();
    }

    /**
     * Get students with outstanding fees by branch
     */
    public function getStudentsWithOutstandingFees(int $branchId, int $academicYearId = null): Collection
    {
        $academicYearId = $academicYearId ?? session('academic_year_id');

        return Student::active()
            ->where('branch_id', $branchId)
            ->whereHas('feesPayments', function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId)
                  ->whereNull('payment_method');
            })
            ->with(['feesPayments' => function($q) use ($academicYearId) {
                $q->where('academic_year_id', $academicYearId)
                  ->whereNull('payment_method');
            }])
            ->get();
    }

    /**
     * Generate bulk fees for branch students
     */
    public function generateBulkFeesForBranch(
        int $branchId,
        array $feeTypeIds,
        int $academicYearId,
        array $studentFilters = [],
        string $batchId = null
    ): array {
        $branch = Branch::findOrFail($branchId);

        if (!$branch->isActive()) {
            throw new \Exception("Branch is not active");
        }

        $students = $this->getActiveStudentsForBranch($branchId, $studentFilters);
        $batchId = $batchId ?: 'BATCH_' . time() . '_' . $branchId;

        $results = [
            'batch_id' => $batchId,
            'branch_id' => $branchId,
            'branch_name' => $branch->name,
            'total_students' => $students->count(),
            'successful' => 0,
            'failed' => 0,
            'total_amount' => 0,
            'details' => []
        ];

        foreach ($students as $student) {
            try {
                $studentAmount = $this->generateFeesForStudent(
                    $student,
                    $feeTypeIds,
                    $academicYearId,
                    $batchId
                );

                $results['successful']++;
                $results['total_amount'] += $studentAmount;
                $results['details'][] = [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'status' => 'success',
                    'amount' => $studentAmount,
                ];

            } catch (\Exception $e) {
                $results['failed']++;
                $results['details'][] = [
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'status' => 'failed',
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Generate fees for individual student
     */
    private function generateFeesForStudent(
        Student $student,
        array $feeTypeIds,
        int $academicYearId,
        string $batchId
    ): float {
        $totalAmount = 0;

        foreach ($feeTypeIds as $feeTypeId) {
            $feeType = FeesType::findOrFail($feeTypeId);

            // Check if fee is applicable for student's academic level
            if (!$feeType->isApplicableFor($student->getAcademicLevel())) {
                continue;
            }

            // Check if student already has this fee for this academic year
            $existingFee = FeesCollect::where('student_id', $student->id)
                ->where('fee_type_id', $feeTypeId)
                ->where('academic_year_id', $academicYearId)
                ->first();

            if ($existingFee) {
                continue; // Skip if already exists
            }

            // Create fee collect record
            FeesCollect::create([
                'student_id' => $student->id,
                'fee_type_id' => $feeTypeId,
                'academic_year_id' => $academicYearId,
                'branch_id' => $student->branch_id,
                'amount' => $feeType->amount,
                'final_amount' => $feeType->amount,
                'generation_batch_id' => $batchId,
                'generation_method' => 'bulk',
                'due_date' => $this->calculateDueDate($feeType),
                'billing_period' => now()->format('Y-m'),
                'billing_year' => now()->year,
                'billing_month' => now()->month,
                'date' => now(),
            ]);

            $totalAmount += $feeType->amount;
        }

        return $totalAmount;
    }

    /**
     * Calculate final amount with discounts
     */
    private function calculateFinalAmount(float $amount, array $options): float
    {
        $discountType = $options['discount_type'] ?? 'none';
        $discountValue = $options['discount_value'] ?? 0;

        return match($discountType) {
            'percentage' => $amount * (1 - ($discountValue / 100)),
            'fixed' => max(0, $amount - $discountValue),
            'override' => $discountValue,
            default => $amount
        };
    }

    /**
     * Calculate due date based on fee type
     */
    private function calculateDueDate(FeesType $feeType): Carbon
    {
        return match($feeType->fee_frequency) {
            'monthly' => now()->endOfMonth(),
            'semester' => now()->addMonths(6)->endOfMonth(),
            'annual' => now()->addYear()->endOfMonth(),
            'one_time' => now()->addDays(30),
            default => now()->endOfMonth()
        };
    }

    /**
     * Get branch fee collection summary
     */
    public function getBranchFeesSummary(int $branchId, int $academicYearId = null): array
    {
        $academicYearId = $academicYearId ?? session('academic_year_id');

        $fees = FeesCollect::byBranch($branchId)
            ->byAcademicYear($academicYearId)
            ->get();

        return [
            'branch_id' => $branchId,
            'academic_year_id' => $academicYearId,
            'total_fees' => $fees->count(),
            'paid_count' => $fees->where('payment_method', '!=', null)->count(),
            'unpaid_count' => $fees->where('payment_method', null)->count(),
            'total_amount' => $fees->sum('amount'),
            'paid_amount' => $fees->where('payment_method', '!=', null)->sum('amount'),
            'outstanding_amount' => $fees->where('payment_method', null)->sum('amount'),
            'overdue_count' => $fees->filter(fn($fee) => $fee->isOverdue())->count(),
            'collection_rate' => $fees->count() > 0 ?
                ($fees->where('payment_method', '!=', null)->count() / $fees->count()) * 100 : 0,
        ];
    }

    /**
     * Validate branch access for student operations
     */
    public function validateBranchAccess(int $branchId): bool
    {
        $branch = Branch::find($branchId);
        return $branch && $branch->isActive();
    }
}
