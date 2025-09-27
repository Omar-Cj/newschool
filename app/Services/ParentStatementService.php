<?php

namespace App\Services;

use App\Models\ParentDeposit\ParentDepositTransaction;
use App\Models\StudentInfo\ParentGuardian;
use App\Models\StudentInfo\Student;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use PDF;

class ParentStatementService
{
    /**
     * Generate comprehensive statement for a parent
     */
    public function generateStatement(ParentGuardian $parent, ?Student $student, Carbon $startDate, Carbon $endDate): array
    {
        $cacheKey = "parent_statement_{$parent->id}_" . ($student?->id ?? 'all') . "_{$startDate->format('Y-m-d')}_{$endDate->format('Y-m-d')}";

        return Cache::remember($cacheKey, 1800, function () use ($parent, $student, $startDate, $endDate) {
            // Get transactions for the period
            $transactions = $this->getTransactionHistory($parent, $student, [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            // Get balance summary
            $balanceSummary = app(ParentDepositService::class)->getBalanceSummary($parent);

            // Calculate period statistics
            $statistics = $this->calculatePeriodStatistics($transactions);

            // Get opening and closing balances
            $openingBalance = $this->getBalanceAtDate($parent, $student, $startDate->copy()->subDay());
            $closingBalance = $this->getBalanceAtDate($parent, $student, $endDate);

            return [
                'parent' => $parent,
                'student' => $student,
                'period' => [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'duration_days' => $startDate->diffInDays($endDate) + 1,
                ],
                'transactions' => $transactions,
                'statistics' => $statistics,
                'balance_summary' => $balanceSummary,
                'opening_balance' => $openingBalance,
                'closing_balance' => $closingBalance,
                'generated_at' => now(),
            ];
        });
    }

    /**
     * Get transaction history with filters
     */
    public function getTransactionHistory(ParentGuardian $parent, ?Student $student, array $filters): Collection
    {
        $query = ParentDepositTransaction::with(['parentDeposit', 'student', 'feesCollect', 'creator'])
            ->where('parent_guardian_id', $parent->id);

        // Filter by student if specified
        if ($student) {
            $query->where('student_id', $student->id);
        }

        // Apply filters
        if (isset($filters['start_date'])) {
            $query->whereDate('transaction_date', '>=', $filters['start_date']);
        }

        if (isset($filters['end_date'])) {
            $query->whereDate('transaction_date', '<=', $filters['end_date']);
        }

        if (isset($filters['transaction_type'])) {
            $query->where('transaction_type', $filters['transaction_type']);
        }

        if (isset($filters['amount_min'])) {
            $query->where('amount', '>=', $filters['amount_min']);
        }

        if (isset($filters['amount_max'])) {
            $query->where('amount', '<=', $filters['amount_max']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('description', 'like', '%' . $filters['search'] . '%')
                  ->orWhere('reference_number', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('transaction_date', 'desc')
                    ->orderBy('id', 'desc')
                    ->get();
    }

    /**
     * Get balance summary for statement display
     */
    public function getBalanceSummary(ParentGuardian $parent): array
    {
        return app(ParentDepositService::class)->getBalanceSummary($parent);
    }

    /**
     * Export statement to PDF
     */
    public function exportStatementToPDF(ParentGuardian $parent, array $filters): string
    {
        $student = isset($filters['student_id']) ? Student::find($filters['student_id']) : null;
        $startDate = Carbon::parse($filters['start_date'] ?? now()->subMonth());
        $endDate = Carbon::parse($filters['end_date'] ?? now());

        $statementData = $this->generateStatement($parent, $student, $startDate, $endDate);

        $pdf = PDF::loadView('backend.parent-deposits.statement-pdf', [
            'data' => $statementData
        ]);

        $fileName = 'parent_statement_' . $parent->id . '_' . now()->format('Y_m_d_H_i_s') . '.pdf';

        return $pdf->download($fileName);
    }

    /**
     * Get monthly statements for a year
     */
    public function getMonthlyStatements(ParentGuardian $parent, int $year): array
    {
        $monthlyStatements = [];

        for ($month = 1; $month <= 12; $month++) {
            $startDate = Carbon::create($year, $month, 1);
            $endDate = $startDate->copy()->endOfMonth();

            $transactions = $this->getTransactionHistory($parent, null, [
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $statistics = $this->calculatePeriodStatistics($transactions);

            $monthlyStatements[] = [
                'month' => $month,
                'month_name' => $startDate->format('F'),
                'year' => $year,
                'transaction_count' => $transactions->count(),
                'total_deposits' => $statistics['total_deposits'],
                'total_withdrawals' => $statistics['total_withdrawals'],
                'net_change' => $statistics['net_change'],
                'formatted_deposits' => '$' . number_format($statistics['total_deposits'], 2),
                'formatted_withdrawals' => '$' . number_format($statistics['total_withdrawals'], 2),
                'formatted_net_change' => '$' . number_format($statistics['net_change'], 2),
            ];
        }

        return $monthlyStatements;
    }

    /**
     * Calculate statistics for a period
     */
    protected function calculatePeriodStatistics(Collection $transactions): array
    {
        $statistics = [
            'total_transactions' => $transactions->count(),
            'total_deposits' => 0,
            'total_withdrawals' => 0,
            'total_allocations' => 0,
            'total_refunds' => 0,
            'net_change' => 0,
            'by_type' => [],
            'by_payment_method' => [],
            'average_transaction' => 0,
            'largest_transaction' => 0,
            'smallest_transaction' => 0,
        ];

        if ($transactions->isEmpty()) {
            return $statistics;
        }

        // Group by transaction type
        $byType = $transactions->groupBy('transaction_type');
        foreach ($byType as $type => $typeTransactions) {
            $total = $typeTransactions->sum('amount');
            $statistics['by_type'][$type] = [
                'count' => $typeTransactions->count(),
                'total' => $total,
                'formatted_total' => '$' . number_format($total, 2),
            ];

            switch ($type) {
                case 'deposit':
                    $statistics['total_deposits'] += $total;
                    break;
                case 'withdrawal':
                    $statistics['total_withdrawals'] += $total;
                    break;
                case 'allocation':
                    $statistics['total_allocations'] += $total;
                    break;
                case 'refund':
                    $statistics['total_refunds'] += $total;
                    break;
            }
        }

        // Calculate net change
        $statistics['net_change'] = $statistics['total_deposits'] + $statistics['total_refunds'] -
                                   $statistics['total_withdrawals'] - $statistics['total_allocations'];

        // Group by payment method (from related deposits)
        $depositsWithPaymentMethod = $transactions->filter(function ($transaction) {
            return $transaction->parentDeposit && $transaction->transaction_type === 'deposit';
        });

        $byPaymentMethod = $depositsWithPaymentMethod->groupBy(function ($transaction) {
            return $transaction->parentDeposit->payment_method;
        });

        foreach ($byPaymentMethod as $method => $methodTransactions) {
            $total = $methodTransactions->sum('amount');
            $methodName = match((int)$method) {
                1 => 'Cash',
                3 => 'Zaad',
                4 => 'Edahab',
                default => 'Unknown'
            };

            $statistics['by_payment_method'][$methodName] = [
                'count' => $methodTransactions->count(),
                'total' => $total,
                'formatted_total' => '$' . number_format($total, 2),
            ];
        }

        // Calculate averages and extremes
        $amounts = $transactions->pluck('amount');
        $statistics['average_transaction'] = $amounts->avg();
        $statistics['largest_transaction'] = $amounts->max();
        $statistics['smallest_transaction'] = $amounts->min();

        return $statistics;
    }

    /**
     * Get balance at a specific date
     */
    protected function getBalanceAtDate(ParentGuardian $parent, ?Student $student, Carbon $date): float
    {
        $transactions = ParentDepositTransaction::where('parent_guardian_id', $parent->id)
            ->when($student, function ($query) use ($student) {
                return $query->where('student_id', $student->id);
            })
            ->whereDate('transaction_date', '<=', $date)
            ->orderBy('transaction_date')
            ->orderBy('id')
            ->get();

        $balance = 0;
        foreach ($transactions as $transaction) {
            if (in_array($transaction->transaction_type, ['deposit', 'refund'])) {
                $balance += $transaction->amount;
            } else {
                $balance -= $transaction->amount;
            }
        }

        return $balance;
    }

    /**
     * Get transaction summary by date range
     */
    public function getTransactionSummary(ParentGuardian $parent, Carbon $startDate, Carbon $endDate): array
    {
        $transactions = $this->getTransactionHistory($parent, null, [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        return [
            'total_transactions' => $transactions->count(),
            'total_amount' => $transactions->sum('amount'),
            'deposits' => $transactions->where('transaction_type', 'deposit')->sum('amount'),
            'withdrawals' => $transactions->where('transaction_type', 'withdrawal')->sum('amount'),
            'allocations' => $transactions->where('transaction_type', 'allocation')->sum('amount'),
            'refunds' => $transactions->where('transaction_type', 'refund')->sum('amount'),
            'formatted_total' => '$' . number_format($transactions->sum('amount'), 2),
        ];
    }

    /**
     * Get daily balance trend
     */
    public function getDailyBalanceTrend(ParentGuardian $parent, ?Student $student, Carbon $startDate, Carbon $endDate): array
    {
        $trend = [];
        $currentDate = $startDate->copy();
        $currentBalance = $this->getBalanceAtDate($parent, $student, $startDate->copy()->subDay());

        while ($currentDate->lte($endDate)) {
            $dayTransactions = $this->getTransactionHistory($parent, $student, [
                'start_date' => $currentDate,
                'end_date' => $currentDate,
            ]);

            $dayChange = 0;
            foreach ($dayTransactions as $transaction) {
                if (in_array($transaction->transaction_type, ['deposit', 'refund'])) {
                    $dayChange += $transaction->amount;
                } else {
                    $dayChange -= $transaction->amount;
                }
            }

            $currentBalance += $dayChange;

            $trend[] = [
                'date' => $currentDate->format('Y-m-d'),
                'formatted_date' => $currentDate->format('M d'),
                'balance' => $currentBalance,
                'change' => $dayChange,
                'transaction_count' => $dayTransactions->count(),
                'formatted_balance' => '$' . number_format($currentBalance, 2),
                'formatted_change' => ($dayChange >= 0 ? '+' : '') . '$' . number_format($dayChange, 2),
            ];

            $currentDate->addDay();
        }

        return $trend;
    }

    /**
     * Clear statement cache
     */
    public function clearStatementCache(int $parentId, ?int $studentId = null): void
    {
        $pattern = "parent_statement_{$parentId}_" . ($studentId ?? '*');

        // Clear all related cache entries
        Cache::flush(); // For simplicity, or implement more specific cache clearing
    }
}