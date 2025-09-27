<?php

namespace App\Models\Fees;

use App\Models\BaseModel;
use App\Models\StudentInfo\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * Unified Receipt model representing all payment receipts
 * Provides consistent interface regardless of underlying payment type
 */
class Receipt extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'receipt_number',
        'student_id',
        'payment_date',
        'total_amount',
        'payment_method',
        'payment_method_details',
        'transaction_reference',
        'collected_by',
        'receipt_type',
        'payment_status',
        'notes',
        'receipt_data', // JSON field for flexible receipt details
        'source_type', // Polymorphic relation to PaymentTransaction or FeesCollect
        'source_id',
        'branch_id',
        'academic_year_id',
        'session_id',
        'voided_at',
        'voided_by',
        'void_reason',
    ];

    protected $casts = [
        'payment_date' => 'date',
        'total_amount' => 'decimal:2',
        'receipt_data' => 'array',
        'payment_method_details' => 'array',
        'voided_at' => 'datetime',
    ];

    // Enums
    public const RECEIPT_TYPES = [
        'payment' => 'Payment Receipt',
        'partial_payment' => 'Partial Payment Receipt',
        'refund' => 'Refund Receipt',
        'adjustment' => 'Fee Adjustment Receipt',
    ];

    public const PAYMENT_STATUSES = [
        'completed' => 'Payment Completed',
        'partial' => 'Partial Payment',
        'refunded' => 'Refunded',
        'voided' => 'Voided',
    ];

    // Relationships
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function collector(): BelongsTo
    {
        return $this->belongsTo(User::class, 'collected_by');
    }

    public function source(): MorphTo
    {
        return $this->morphTo();
    }

    public function allocations(): HasMany
    {
        return $this->hasMany(ReceiptAllocation::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->whereNull('voided_at');
    }

    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('payment_date', [$startDate, $endDate]);
    }

    public function scopeByPaymentMethod($query, $method)
    {
        return $query->where('payment_method', $method);
    }

    public function scopeByCollector($query, $collectorId)
    {
        return $query->where('collected_by', $collectorId);
    }

    // Helper Methods
    public function getFormattedAmount(): string
    {
        return setting('currency_symbol') . number_format($this->total_amount, 2);
    }

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

    public function isVoided(): bool
    {
        return !is_null($this->voided_at);
    }

    public function isPartialPayment(): bool
    {
        return $this->payment_status === 'partial';
    }

    public function getReceiptTypeName(): string
    {
        return self::RECEIPT_TYPES[$this->receipt_type] ?? 'Unknown';
    }

    public function getPaymentStatusName(): string
    {
        return self::PAYMENT_STATUSES[$this->payment_status] ?? 'Unknown';
    }

    /**
     * Get receipt allocation summary for transparency
     */
    public function getAllocationSummary(): array
    {
        return $this->allocations->map(function ($allocation) {
            return [
                'fee_name' => $allocation->fee_name,
                'allocated_amount' => $allocation->allocated_amount,
                'fee_balance_before' => $allocation->fee_balance_before,
                'fee_balance_after' => $allocation->fee_balance_after,
                'is_full_payment' => $allocation->fee_balance_after <= 0,
            ];
        })->toArray();
    }

    /**
     * Void the receipt with proper audit trail
     */
    public function voidReceipt(string $reason, int $voidedBy): bool
    {
        $this->update([
            'payment_status' => 'voided',
            'voided_at' => now(),
            'voided_by' => $voidedBy,
            'void_reason' => $reason,
        ]);

        return true;
    }

    /**
     * Get comprehensive receipt data for display/printing
     */
    public function getComprehensiveReceiptData(): array
    {
        return [
            'receipt_info' => [
                'number' => $this->receipt_number,
                'date' => $this->payment_date->format('Y-m-d'),
                'type' => $this->getReceiptTypeName(),
                'status' => $this->getPaymentStatusName(),
            ],
            'payment_info' => [
                'total_amount' => $this->total_amount,
                'formatted_amount' => $this->getFormattedAmount(),
                'method' => $this->getPaymentMethodName(),
                'reference' => $this->transaction_reference,
                'collector' => $this->collector?->name,
            ],
            'student_info' => [
                'name' => $this->student?->full_name,
                'admission_no' => $this->student?->admission_no,
                'class' => $this->student?->current_class_name ?? 'N/A',
            ],
            'allocation_details' => $this->getAllocationSummary(),
            'additional_data' => $this->receipt_data ?? [],
        ];
    }
}