<?php

namespace App\Models\StudentInfo;

use App\Models\User;
use App\Models\BaseModel;
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
}
