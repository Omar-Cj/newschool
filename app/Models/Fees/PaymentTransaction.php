<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use App\Models\StudentInfo\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PaymentTransaction extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'fees_collect_id',
        'student_id',
        'school_id', // Multi-tenant scoping
        'transaction_number',
        'payment_date',
        'amount',
        'payment_method',
        'payment_gateway',
        'transaction_reference',
        'payment_session_id', // Groups related transactions
        'receipt_id', // Links to consolidated receipt
        'payment_notes',
        'journal_id',
        'collected_by',
        'branch_id',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'amount' => 'decimal:2',
        'payment_method' => 'integer',
    ];

    // Relationships
    public function feesCollect(): BelongsTo
    {
        return $this->belongsTo(FeesCollect::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function journal(): BelongsTo
    {
        return $this->belongsTo(\Modules\Journals\Entities\Journal::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    public function receipt(): BelongsTo
    {
        return $this->belongsTo(Receipt::class);
    }

    // Scopes
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByFee($query, $feeCollectId)
    {
        return $query->where('fees_collect_id', $feeCollectId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('payment_date', '>=', now()->subDays($days));
    }

    // Helper methods
    public function getPaymentMethodName(): string
    {
        return match($this->payment_method) {
            1 => 'Cash',
            2 => 'Stripe',
            3 => 'Zaad',
            4 => 'Edahab',
            5 => 'PayPal',
            default => 'Unknown'
        };
    }

    public function getFormattedAmount(): string
    {
        return '$' . number_format($this->amount, 2);
    }

    public function getCollectorName(): string
    {
        return $this->collector?->name ?? 'Unknown';
    }

    public function getBranchName(): string
    {
        return $this->branch?->name ?? 'Unknown Branch';
    }

    public function getJournalName(): string
    {
        return $this->journal?->display_name ?? 'No Journal';
    }

    /**
     * Generate unique transaction number (school-scoped for multi-tenancy)
     *
     * CRITICAL: Each school has independent transaction number sequences
     * Format: PAY-YYYY-XXXXXX (e.g., PAY-2025-000001)
     *
     * @return string The generated transaction number
     * @throws \Exception If school context is unavailable
     */
    public static function generateTransactionNumber(): string
    {
        return \DB::transaction(function () {
            // Get school ID from authenticated user
            $schoolId = auth()->user()->school_id ?? null;

            if (!$schoolId) {
                \Log::error('Cannot generate transaction number without school context', [
                    'user_id' => auth()->id(),
                    'authenticated' => auth()->check()
                ]);
                throw new \Exception('School context required for transaction number generation');
            }

            $prefix = 'PAY-' . date('Y') . '-';

            // Get maximum sequence number from existing transaction_numbers for this school
            // Use lockForUpdate to ensure thread safety
            $maxSequenceNum = static::where('school_id', $schoolId)
                ->lockForUpdate()
                ->get()
                ->map(function ($transaction) use ($prefix) {
                    // Extract numeric part from transaction_number
                    // e.g., "PAY-2025-000005" -> 5
                    $numberPart = str_replace($prefix, '', $transaction->transaction_number);
                    return (int) $numberPart;
                })
                ->max();

            // If no records exist for this school, start from 1
            $nextSequence = ($maxSequenceNum ?? 0) + 1;

            // Generate the transaction number in the requested format
            $transactionNumber = $prefix . str_pad($nextSequence, 6, '0', STR_PAD_LEFT);

            \Log::info('Generated new transaction number (school-scoped)', [
                'transaction_number' => $transactionNumber,
                'sequence' => $nextSequence,
                'school_id' => $schoolId,
                'max_existing_sequence' => $maxSequenceNum,
                'user_id' => auth()->id()
            ]);

            return $transactionNumber;
        });
    }

    // Boot method to auto-generate transaction number
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->transaction_number)) {
                $transaction->transaction_number = static::generateTransactionNumber();
            }
        });
    }

    // Audit information
    public function getAuditInformation(): array
    {
        return [
            'id' => $this->id,
            'transaction_number' => $this->transaction_number,
            'student_name' => $this->student->full_name ?? 'Unknown',
            'fee_name' => $this->feesCollect->getFeeName(),
            'amount' => $this->amount,
            'payment_method' => $this->getPaymentMethodName(),
            'payment_date' => $this->payment_date->format('Y-m-d'),
            'collected_by' => $this->getCollectorName(),
            'branch' => $this->getBranchName(),
            'journal' => $this->getJournalName(),
            'reference' => $this->transaction_reference,
            'notes' => $this->payment_notes,
            'created_at' => $this->created_at->toISOString(),
        ];
    }
}