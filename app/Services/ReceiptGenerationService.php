<?php

namespace App\Services;

use App\Models\Fees\Receipt;
use App\Models\Fees\PaymentTransaction;
use App\Models\Fees\FeesCollect;
use App\Models\StudentInfo\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Exception;

/**
 * Service for generating consolidated receipts from payment transactions
 * Handles grouping of family payments and discount aggregation
 */
class ReceiptGenerationService
{
    protected $receiptNumberingService;

    public function __construct(ReceiptNumberingService $receiptNumberingService)
    {
        $this->receiptNumberingService = $receiptNumberingService;
    }

    /**
     * Generate individual receipts for family payment session
     * Creates separate receipt for each student with their specific amounts
     *
     * @param string $sessionId Payment session ID grouping related transactions
     * @param array $transactionIds Array of PaymentTransaction IDs to include
     * @return array Array of Receipt objects
     * @throws Exception
     */
    public function generateFamilyReceipts(string $sessionId, array $transactionIds): array
    {
        return DB::transaction(function () use ($sessionId, $transactionIds) {
            // Load all transactions for this session
            $transactions = PaymentTransaction::with([
                'student.parent',
                'feesCollect.feeType',
                'feesCollect.session',
                'collector',
                'branch'
            ])->whereIn('id', $transactionIds)->get();

            if ($transactions->isEmpty()) {
                throw new Exception('No transactions found for session: ' . $sessionId);
            }

            // Group transactions by student
            $transactionsByStudent = $transactions->groupBy('student_id');

            $receipts = [];

            // Generate individual receipt for each student
            foreach ($transactionsByStudent as $studentId => $studentTransactions) {
                $student = $studentTransactions->first()->student;
                $collector = $studentTransactions->first()->collector;
                $paymentDate = $studentTransactions->first()->payment_date;
                $paymentMethod = $studentTransactions->first()->payment_method;
                $branchId = $studentTransactions->first()->branch_id;

                // Calculate student-specific totals
                $studentAmount = $studentTransactions->sum('amount');
                $studentDiscount = $this->calculateTotalDiscounts($studentTransactions);

                // Build receipt data for this student only
                $receiptData = $this->buildStudentReceiptData($student, $studentTransactions);

                // Get class/section from fees_collect session context (historical enrollment at time of payment)
                $feeCollect = $studentTransactions->first()->feesCollect;
                $sessionClassStudent = \App\Models\StudentInfo\SessionClassStudent::where('student_id', $student->id)
                    ->where('session_id', $feeCollect->session_id)
                    ->first();

                // Generate unique receipt number
                $receiptNumber = $this->receiptNumberingService->generateReceiptNumber($paymentDate);

                // Create receipt record
                $receipt = Receipt::create([
                    'receipt_number' => $receiptNumber,
                    'payment_session_id' => $sessionId,
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'class' => $sessionClassStudent->class->name ?? null,
                    'section' => $sessionClassStudent->section->name ?? null,
                    'guardian_name' => $student->parent->guardian_name ?? $student->parent->father_name ?? null,
                    'payment_date' => $paymentDate,
                    'total_amount' => $studentAmount,
                    'discount_amount' => $studentDiscount,
                    'payment_method' => $paymentMethod,
                    'payment_gateway' => $studentTransactions->first()->payment_gateway,
                    'transaction_reference' => $this->generateTransactionReference($sessionId),
                    'collected_by' => $collector->id,
                    'receipt_type' => 'payment',
                    'payment_status' => 'completed',
                    'receipt_data' => $receiptData,
                    'source_type' => PaymentTransaction::class,
                    'source_id' => $studentTransactions->first()->id,
                    'branch_id' => $branchId,
                    'academic_year_id' => activeAcademicYear(),
                    'session_id' => activeAcademicYear(),  // session_id and academic_year_id reference same sessions table
                ]);

                // Confirm receipt number reservation
                $this->receiptNumberingService->confirmReceiptNumber($receiptNumber);

                // Link this student's transactions to their receipt
                PaymentTransaction::whereIn('id', $studentTransactions->pluck('id')->toArray())
                    ->update(['receipt_id' => $receipt->id]);

                $receipts[] = $receipt;

                Log::info('Individual receipt generated for family payment', [
                    'receipt_number' => $receiptNumber,
                    'session_id' => $sessionId,
                    'student_id' => $student->id,
                    'student_name' => $student->full_name,
                    'amount' => $studentAmount,
                    'discount' => $studentDiscount,
                ]);
            }

            Log::info('Family payment receipts completed', [
                'session_id' => $sessionId,
                'total_receipts' => count($receipts),
                'total_students' => $transactionsByStudent->count(),
            ]);

            return $receipts;
        });
    }

    /**
     * Generate receipt for a single payment transaction
     * Used for individual (non-family) payments
     *
     * @param PaymentTransaction $transaction
     * @return Receipt
     */
    public function generateSingleReceipt(PaymentTransaction $transaction): Receipt
    {
        return DB::transaction(function () use ($transaction) {
            $transaction->load([
                'student.parent',
                'feesCollect.feeType',
                'feesCollect.session',
                'collector',
                'branch'
            ]);

            $student = $transaction->student;
            $feeCollect = $transaction->feesCollect;

            // Calculate discount from fees_collect record
            $discount = $feeCollect->discount_amount ?? 0;

            // Get class/section from fees_collect session context (historical enrollment at time of payment)
            $sessionClassStudent = \App\Models\StudentInfo\SessionClassStudent::where('student_id', $student->id)
                ->where('session_id', $feeCollect->session_id)
                ->first();

            // Generate receipt number
            $receiptNumber = $this->receiptNumberingService->generateReceiptNumber($transaction->payment_date);

            // Build simple receipt data for single payment
            $receiptData = [
                'students' => [[
                    'id' => $student->id,
                    'name' => $student->full_name,
                    'fees' => [[
                        'name' => $feeCollect->feeType->name ?? 'Fee',
                        'amount' => (float) $transaction->amount,
                        'discount' => (float) $discount,
                    ]],
                    'total_amount' => (float) $transaction->amount,
                    'total_discount' => (float) $discount,
                ]],
                'fee_breakdown' => [
                    $feeCollect->feeType->name ?? 'Fee' => (float) $transaction->amount
                ],
            ];

            $receipt = Receipt::create([
                'receipt_number' => $receiptNumber,
                'student_id' => $student->id,
                'student_name' => $student->full_name,
                'class' => $sessionClassStudent->class->name ?? null,
                'section' => $sessionClassStudent->section->name ?? null,
                'guardian_name' => $student->parent->guardian_name ?? $student->parent->father_name ?? null,
                'payment_date' => $transaction->payment_date,
                'total_amount' => $transaction->amount,
                'discount_amount' => $discount,
                'payment_method' => $transaction->payment_method,
                'payment_gateway' => $transaction->payment_gateway,
                'transaction_reference' => $transaction->transaction_reference,
                'collected_by' => $transaction->collected_by,
                'receipt_type' => 'payment',
                'payment_status' => 'completed',
                'receipt_data' => $receiptData,
                'source_type' => PaymentTransaction::class,
                'source_id' => $transaction->id,
                'branch_id' => $transaction->branch_id,
                'academic_year_id' => activeAcademicYear(),
                'session_id' => activeAcademicYear(),  // session_id and academic_year_id reference same sessions table
            ]);

            // Confirm receipt number
            $this->receiptNumberingService->confirmReceiptNumber($receiptNumber);

            // Link transaction to receipt
            $transaction->update(['receipt_id' => $receipt->id]);

            return $receipt;
        });
    }

    /**
     * Calculate total discounts from all fee collect records
     *
     * @param Collection $transactions Collection of PaymentTransaction models
     * @return float Total discount amount
     */
    protected function calculateTotalDiscounts(Collection $transactions): float
    {
        $totalDiscount = 0;

        foreach ($transactions as $transaction) {
            $feeCollect = $transaction->feesCollect;
            if ($feeCollect) {
                $totalDiscount += $feeCollect->discount_amount ?? 0;
            }
        }

        return round($totalDiscount, 2);
    }

    /**
     * Build receipt data for individual student
     * Simplified structure for single student receipts
     *
     * @param Student $student
     * @param Collection $transactions
     * @return array Structured receipt data
     */
    protected function buildStudentReceiptData($student, Collection $transactions): array
    {
        $fees = [];
        $feeBreakdown = [];
        $totalAmount = 0;
        $totalDiscount = 0;

        foreach ($transactions as $transaction) {
            $feeCollect = $transaction->feesCollect;
            $feeName = $feeCollect->feeType->name ?? 'Fee';
            $discount = $feeCollect->discount_amount ?? 0;

            $fees[] = [
                'name' => $feeName,
                'amount' => (float) $transaction->amount,
                'discount' => (float) $discount,
            ];

            $totalAmount += $transaction->amount;
            $totalDiscount += $discount;

            // Aggregate fee breakdown
            if (!isset($feeBreakdown[$feeName])) {
                $feeBreakdown[$feeName] = 0;
            }
            $feeBreakdown[$feeName] += $transaction->amount;
        }

        // Get class/section from fees_collect session context (historical enrollment at time of payment)
        $feeCollect = $transactions->first()->feesCollect;
        $sessionClassStudent = \App\Models\StudentInfo\SessionClassStudent::where('student_id', $student->id)
            ->where('session_id', $feeCollect->session_id)
            ->first();

        return [
            'student' => [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_no' => $student->admission_no,
                'class' => $sessionClassStudent->class->name ?? 'N/A',
                'section' => $sessionClassStudent->section->name ?? 'N/A',
            ],
            'fees' => $fees,
            'fee_breakdown' => $feeBreakdown,
            'total_amount' => (float) $totalAmount,
            'total_discount' => (float) $totalDiscount,
        ];
    }

    /**
     * Build detailed receipt data structure (legacy for consolidated receipts)
     *
     * @param Collection $transactions
     * @return array Structured receipt data
     */
    protected function buildReceiptData(Collection $transactions): array
    {
        $studentData = [];
        $feeBreakdown = [];

        // Group transactions by student
        $transactionsByStudent = $transactions->groupBy('student_id');

        foreach ($transactionsByStudent as $studentId => $studentTransactions) {
            $student = $studentTransactions->first()->student;
            $studentFees = [];
            $studentTotal = 0;
            $studentDiscount = 0;

            foreach ($studentTransactions as $transaction) {
                $feeCollect = $transaction->feesCollect;
                $feeName = $feeCollect->feeType->name ?? 'Fee';
                $discount = $feeCollect->discount_amount ?? 0;

                $studentFees[] = [
                    'name' => $feeName,
                    'amount' => (float) $transaction->amount,
                    'discount' => (float) $discount,
                ];

                $studentTotal += $transaction->amount;
                $studentDiscount += $discount;

                // Aggregate fee breakdown across all students
                if (!isset($feeBreakdown[$feeName])) {
                    $feeBreakdown[$feeName] = 0;
                }
                $feeBreakdown[$feeName] += $transaction->amount;
            }

            // Get class/section from fees_collect session context (historical enrollment at time of payment)
            $feeCollect = $studentTransactions->first()->feesCollect;
            $sessionClassStudent = \App\Models\StudentInfo\SessionClassStudent::where('student_id', $student->id)
                ->where('session_id', $feeCollect->session_id)
                ->first();

            $studentData[] = [
                'id' => $student->id,
                'name' => $student->full_name,
                'admission_no' => $student->admission_no,
                'class' => $sessionClassStudent->class->name ?? 'N/A',
                'section' => $sessionClassStudent->section->name ?? 'N/A',
                'fees' => $studentFees,
                'total_amount' => (float) $studentTotal,
                'total_discount' => (float) $studentDiscount,
            ];
        }

        return [
            'students' => $studentData,
            'fee_breakdown' => $feeBreakdown,
            'total_students' => count($studentData),
            'is_family_payment' => count($studentData) > 1,
        ];
    }

    /**
     * Generate transaction reference from session ID
     *
     * @param string $sessionId
     * @return string
     */
    protected function generateTransactionReference(string $sessionId): string
    {
        return 'RCPT_' . strtoupper(substr($sessionId, 0, 20));
    }
}
