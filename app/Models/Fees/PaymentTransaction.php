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
        'transaction_number',
        'payment_date',
        'amount',
        'payment_method',
        'payment_gateway',
        'transaction_reference',
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

    // Generate unique transaction number
    public static function generateTransactionNumber(): string
    {
        $prefix = 'PAY-' . date('Y') . '-';
        $lastTransaction = static::where('transaction_number', 'like', $prefix . '%')
            ->orderBy('id', 'desc')
            ->first();

        if ($lastTransaction) {
            $lastNumber = (int) substr($lastTransaction->transaction_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            $newNumber = 1;
        }

        return $prefix . str_pad($newNumber, 6, '0', STR_PAD_LEFT);
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