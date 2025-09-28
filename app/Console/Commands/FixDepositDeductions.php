<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Fees\PaymentTransaction;
use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\ParentGuardian;
use App\Services\ParentDepositService;
use Illuminate\Support\Facades\DB;

class FixDepositDeductions extends Command
{
    protected $signature = 'deposits:fix-deductions {--student-id= : Specific student ID to fix} {--dry-run : Show what would be done without making changes}';
    protected $description = 'Fix missing deposit deductions for existing payments';

    public function handle()
    {
        $studentId = $this->option('student-id');
        $dryRun = $this->option('dry-run');
        
        if ($dryRun) {
            $this->info('🔍 DRY RUN MODE - No changes will be made');
        }

        $query = PaymentTransaction::where('payment_gateway', '!=', 'deposit')
            ->whereHas('student.parent')
            ->with(['student.parent']);

        if ($studentId) {
            $query->where('student_id', $studentId);
        }

        $payments = $query->get();
        
        if ($payments->isEmpty()) {
            $this->info('No payments found that need deposit deduction fixes.');
            return;
        }

        $this->info("Found {$payments->count()} payments to process...");

        $totalFixed = 0;
        $totalDepositUsed = 0;

        foreach ($payments as $payment) {
            $student = $payment->student;
            $parent = $student->parent;

            if (!$parent) {
                $this->warn("Skipping payment {$payment->id} - no parent found for student {$student->id}");
                continue;
            }

            // Check if deposit deduction already exists for this payment
            $existingDeduction = DB::table('parent_deposit_transactions')
                ->where('fees_collect_id', $payment->fees_collect_id)
                ->where('transaction_type', 'allocation')
                ->exists();

            if ($existingDeduction) {
                $this->info("Payment {$payment->id} already has deposit deduction - skipping");
                continue;
            }

            // Check available deposit balance (both general and student-specific)
            $generalBalance = $parent->getAvailableBalance();
            $studentBalance = $parent->getAvailableBalance($student);
            $availableBalance = $generalBalance + $studentBalance;
            
            if ($availableBalance <= 0) {
                $this->warn("Payment {$payment->id} - no available deposit balance (\${$availableBalance})");
                continue;
            }

            $amountToDeduct = min($payment->amount, $availableBalance);

            if ($amountToDeduct <= 0) {
                $this->warn("Payment {$payment->id} - no amount to deduct");
                continue;
            }

            $this->info("Processing payment {$payment->id}: \${$payment->amount} - will deduct \${$amountToDeduct} from deposit");

            if (!$dryRun) {
                try {
                    DB::transaction(function () use ($parent, $student, $amountToDeduct, $payment) {
                        // Get or create balance record
                        $balance = $parent->balances()
                            ->where('academic_year_id', activeAcademicYear())
                            ->whereNull('student_id')
                            ->first();

                        if (!$balance) {
                            throw new \Exception('No balance record found');
                        }

                        // Deduct from balance
                        if (!$balance->deductWithdrawal($amountToDeduct)) {
                            throw new \Exception('Failed to deduct from balance');
                        }

                        // Create transaction record
                        DB::table('parent_deposit_transactions')->insert([
                            'parent_guardian_id' => $parent->id,
                            'student_id' => $student->id,
                            'transaction_type' => 'allocation',
                            'amount' => $amountToDeduct,
                            'balance_before' => $balance->available_balance + $amountToDeduct,
                            'balance_after' => $balance->available_balance,
                            'transaction_date' => $payment->created_at,
                            'description' => "Retroactive deduction for payment #{$payment->id}",
                            'fees_collect_id' => $payment->fees_collect_id,
                            'created_by' => 1, // System user
                            'branch_id' => activeBranch(),
                            'reference_number' => 'RETRO-' . time() . '-' . $payment->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                    });

                    $totalFixed++;
                    $totalDepositUsed += $amountToDeduct;
                    $this->info("✅ Fixed payment {$payment->id}");

                } catch (\Exception $e) {
                    $this->error("❌ Failed to fix payment {$payment->id}: " . $e->getMessage());
                }
            } else {
                $totalFixed++;
                $totalDepositUsed += $amountToDeduct;
            }
        }

        if ($dryRun) {
            $this->info("🔍 DRY RUN COMPLETE:");
            $this->info("- Would fix {$totalFixed} payments");
            $this->info("- Would deduct \${$totalDepositUsed} from deposits");
        } else {
            $this->info("✅ FIX COMPLETE:");
            $this->info("- Fixed {$totalFixed} payments");
            $this->info("- Deducted \${$totalDepositUsed} from deposits");
        }
    }
}
