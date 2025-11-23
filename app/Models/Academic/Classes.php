<?php

namespace App\Models\Academic;

use App\Models\Academic\ClassSetup;
use App\Models\BaseModel;
use App\Models\ClassTranslate;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classes extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'name','status','academic_level'
    ];

    protected $appends = ['class_tran'];

    public function getClassTranAttribute()
    {
        $translation = $this->defaultTranslate()->first();
        return $translation->name ?? $this->name;

    }

    public function scopeActive($query)
    {
        return $query->where('status', \App\Enums\Status::ACTIVE);
    }
    public function classSetup()
    {
        return $this->hasOne(ClassSetup::class);
    }

    public function defaultTranslate()
    {
        $relation = $this->hasOne(ClassTranslate::class, 'class_id')->where('locale', request()->locale ?? config('app.locale'));
        if ($relation->exists()) {
            return $relation;
        } else {
            return $this->hasOne(ClassTranslate::class, 'class_id')->where('locale', 'en');
        }
    }


    public function translations()
    {
        return $this->hasMany(ClassTranslate::class, 'class_id', 'id');
    }

    // Academic level related methods
    public function scopeByAcademicLevel($query, string $level)
    {
        return $query->where('academic_level', $level);
    }

    public function scopeWithoutAcademicLevel($query)
    {
        return $query->whereNull('academic_level');
    }

    public function getFormattedAcademicLevelAttribute(): string
    {
        return match($this->academic_level) {
            'kg' => 'Kindergarten',
            'primary' => 'Primary School',
            'secondary' => 'Secondary School',
            'high_school' => 'High School',
            default => 'Not Assigned'
        };
    }

    public function hasAcademicLevel(): bool
    {
        return !is_null($this->academic_level);
    }

    public function getAcademicLevelColor(): string
    {
        return match($this->academic_level) {
            'kg' => 'success',
            'primary' => 'primary', 
            'secondary' => 'warning',
            'high_school' => 'info',
            default => 'secondary'
        };
    }

    public static function getAcademicLevelOptions(): array
    {
        return [
            'kg' => 'Kindergarten (KG-1 to KG-3)',
            'primary' => 'Primary School (Grade 1 to 8)',
            'secondary' => 'Secondary School (Form 1 to 4)',
        ];
    }

    public static function getAcademicLevelCounts(): array
    {
        $counts = self::active()
            ->selectRaw('academic_level, COUNT(*) as count')
            ->groupBy('academic_level')
            ->pluck('count', 'academic_level')
            ->toArray();

        // Include zero counts for all levels
        $allLevels = ['kg', 'primary', 'secondary', 'high_school', null];
        $result = [];
        
        foreach ($allLevels as $level) {
            $key = $level ?? 'unassigned';
            $result[$key] = $counts[$level] ?? 0;
        }

        return $result;
    }

    // Intelligent academic level suggestion based on class name
    public function suggestAcademicLevel(): ?string
    {
        $name = strtolower($this->name);
        
        // KG patterns
        if (preg_match('/\b(kg|kindergarten|nursery|pre-?k|pre-?school)\b/i', $name)) {
            return 'kg';
        }
        
        // Form patterns (secondary)
        if (preg_match('/\bform\s*[1-4]\b/i', $name)) {
            return 'secondary';
        }
        
        // Grade/Class number patterns
        if (preg_match('/\b(?:grade|class)?\s*(\d+)\b/i', $name, $matches)) {
            $number = (int) $matches[1];
            
            return match(true) {
                $number >= 1 && $number <= 8 => 'primary',
                $number >= 9 && $number <= 10 => 'secondary',
                $number >= 11 && $number <= 12 => 'high_school',
                $number < 1 => 'kg',
                default => null
            };
        }
        
        // Subject-based patterns (usually secondary or high school)
        if (preg_match('/\b(advanced|algebra|calculus|physics|chemistry|biology)\b/i', $name)) {
            return 'secondary';
        }
        
        return null;
    }
}
