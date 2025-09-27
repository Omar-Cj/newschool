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
}
