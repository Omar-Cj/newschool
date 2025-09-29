<?php

namespace App\Models\StudentInfo;

use App\Models\User;
use App\Models\BaseModel;
use App\Models\ParentDeposit\ParentDeposit;
use App\Models\ParentDeposit\ParentBalance;
use App\Models\ParentDeposit\ParentDepositTransaction;
use Modules\LiveChat\Entities\Message;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ParentGuardian extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'father_name',
        'father_mobile', 
        'father_profession',
        'father_nationality',
        'mother_name',
        'mother_mobile',
        'mother_profession',
        'guardian_name',
        'guardian_email',
        'guardian_mobile',
        'guardian_profession',
        'guardian_relation',
        'guardian_address',
        'guardian_place_of_work',
        'guardian_position',
        'status'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function lastMessage()
    {
        return $this->hasOne(Message::class, 'sender_id', 'user_id')->latest();
    }

    public function unreadMessages()
    {
        return $this->hasMany(Message::class, 'receiver_id', 'user_id')->where('is_seen', 0);
    }

    public function scopeActive($query)
    {
        return $query->where('status', \App\Enums\Status::ACTIVE);
    }

    public function children() : HasMany
    {
        return $this->hasMany(Student::class, "parent_guardian_id", "id");
    }

    // Deposit relationships
    public function deposits(): HasMany
    {
        return $this->hasMany(ParentDeposit::class);
    }

    public function depositTransactions(): HasMany
    {
        return $this->hasMany(ParentDepositTransaction::class);
    }

    public function balances(): HasMany
    {
        return $this->hasMany(ParentBalance::class);
    }

    public function currentBalance(): HasOne
    {
        return $this->hasOne(ParentBalance::class)
                    ->where('academic_year_id', activeAcademicYear())
                    ->whereNull('student_id');
    }

    // Deposit helper methods
    public function getAvailableBalance(?Student $student = null): float
    {
        $balance = $this->balances()
            ->where('academic_year_id', activeAcademicYear())
            ->when($student, function($query) use ($student) {
                return $query->where('student_id', $student->id);
            }, function($query) {
                return $query->whereNull('student_id');
            })
            ->first();

        return $balance?->available_balance ?? 0;
    }

    public function getTotalBalance(?Student $student = null): float
    {
        $balance = $this->balances()
            ->where('academic_year_id', activeAcademicYear())
            ->when($student, function($query) use ($student) {
                return $query->where('student_id', $student->id);
            }, function($query) {
                return $query->whereNull('student_id');
            })
            ->first();

        return $balance ? $balance->getTotalBalance() : 0;
    }

    public function hasAvailableBalance(?Student $student = null): bool
    {
        return $this->getAvailableBalance($student) > 0;
    }

    public function getFormattedAvailableBalance(?Student $student = null): string
    {
        return '$' . number_format($this->getAvailableBalance($student), 2);
    }

    public function getFormattedTotalBalance(?Student $student = null): string
    {
        return '$' . number_format($this->getTotalBalance($student), 2);
    }

    public function getAllBalances(): array
    {
        $balances = [];

        // General balance
        $generalBalance = $this->getAvailableBalance();
        if ($generalBalance > 0) {
            $balances['general'] = [
                'student_name' => 'General Account',
                'available_balance' => $generalBalance,
                'formatted_balance' => $this->getFormattedAvailableBalance()
            ];
        }

        // Student-specific balances
        foreach ($this->children as $child) {
            $studentBalance = $this->getAvailableBalance($child);
            if ($studentBalance > 0) {
                $balances['student_' . $child->id] = [
                    'student_name' => $child->full_name,
                    'available_balance' => $studentBalance,
                    'formatted_balance' => $this->getFormattedAvailableBalance($child)
                ];
            }
        }

        return $balances;
    }

    // Sibling fee collection methods
    public function childrenWithOutstandingFees(): HasMany
    {
        return $this->children()
            ->whereHas('feesCollects', function($query) {
                $query->where('academic_year_id', activeAcademicYear())
                      ->whereColumn('total_paid', '<', 'amount');
            });
    }

    public function getTotalFamilyOutstandingFees(): float
    {
        return $this->children()
            ->with(['feesCollects' => function($query) {
                $query->where('academic_year_id', activeAcademicYear())
                      ->whereColumn('total_paid', '<', 'amount');
            }])
            ->get()
            ->sum(function($child) {
                return $child->feesCollects->sum(function($fee) {
                    return $fee->amount - $fee->total_paid;
                });
            });
    }

    public function getFormattedTotalFamilyOutstandingFees(): string
    {
        return Setting('currency_symbol') . number_format($this->getTotalFamilyOutstandingFees(), 2);
    }

    public function canPayAllFeesWithDeposit(): bool
    {
        $totalOutstanding = $this->getTotalFamilyOutstandingFees();
        $totalAvailableDeposit = $this->getTotalAvailableDeposit();

        return $totalAvailableDeposit >= $totalOutstanding;
    }

    public function getTotalAvailableDeposit(): float
    {
        // Get general deposit balance
        $generalBalance = $this->getAvailableBalance();

        // Get all student-specific deposit balances
        $studentBalances = $this->children->sum(function($child) {
            return $this->getAvailableBalance($child);
        });

        return $generalBalance + $studentBalances;
    }

    public function getSiblingFeeSummary(): array
    {
        $summary = [
            'total_children' => $this->children->count(),
            'children_with_fees' => $this->childrenWithOutstandingFees()->count(),
            'total_outstanding' => $this->getTotalFamilyOutstandingFees(),
            'formatted_outstanding' => $this->getFormattedTotalFamilyOutstandingFees(),
            'total_available_deposit' => $this->getTotalAvailableDeposit(),
            'formatted_available_deposit' => Setting('currency_symbol') . number_format($this->getTotalAvailableDeposit(), 2),
            'can_pay_all_with_deposit' => $this->canPayAllFeesWithDeposit(),
        ];

        $summary['children_details'] = [];
        foreach ($this->children as $child) {
            $childOutstanding = $child->feesCollects()
                ->where('academic_year_id', activeAcademicYear())
                ->whereColumn('total_paid', '<', 'amount')
                ->get()
                ->sum(function($fee) {
                    return $fee->amount - $fee->total_paid;
                });

            if ($childOutstanding > 0) {
                $summary['children_details'][] = [
                    'id' => $child->id,
                    'name' => $child->full_name,
                    'outstanding_amount' => (float) $childOutstanding,
                    'formatted_outstanding' => Setting('currency_symbol') . number_format($childOutstanding, 2),
                    'class_section' => $child->session_class_student?->class?->name .
                                     ' - ' . $child->session_class_student?->section?->name,
                ];
            }
        }

        return $summary;
    }

    public function getOptimalPaymentDistribution(float $totalAmount): array
    {
        $childrenWithFees = $this->childrenWithOutstandingFees()
            ->with(['feesCollects' => function($query) {
                $query->where('academic_year_id', activeAcademicYear())
                      ->whereColumn('total_paid', '<', 'amount');
            }])
            ->get();

        if ($childrenWithFees->isEmpty()) {
            return [];
        }

        $totalOutstanding = 0;
        $childrenData = [];

        foreach ($childrenWithFees as $child) {
            $childOutstanding = $child->feesCollects->sum(function($fee) {
                return $fee->amount - $fee->total_paid;
            });

            $childrenData[] = [
                'student_id' => $child->id,
                'name' => $child->full_name,
                'outstanding_amount' => (float) $childOutstanding,
            ];

            $totalOutstanding += $childOutstanding;
        }

        // Calculate proportional distribution
        foreach ($childrenData as &$childData) {
            if ($totalOutstanding > 0) {
                $proportion = $childData['outstanding_amount'] / $totalOutstanding;
                $suggestedPayment = min($totalAmount * $proportion, $childData['outstanding_amount']);
                $childData['suggested_payment'] = (float) $suggestedPayment;
                $childData['formatted_suggested'] = Setting('currency_symbol') . number_format($suggestedPayment, 2);
            } else {
                $childData['suggested_payment'] = 0;
                $childData['formatted_suggested'] = Setting('currency_symbol') . '0.00';
            }
        }

        return $childrenData;
    }
}
