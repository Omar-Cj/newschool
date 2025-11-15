<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory;

    protected $guarded = ["id"];

    protected $casts = [
        'keywords' => 'array',
    ];

    /**
     * Backwards compatibility accessor for 'name' attribute.
     * Maps to the 'attribute' column.
     *
     * @return string
     */
    public function getNameAttribute(): string
    {
        return $this->attributes['attribute'] ?? '';
    }
}
