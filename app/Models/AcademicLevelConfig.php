<?php

namespace App\Models;

use App\Models\BaseModel;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Collection;

class AcademicLevelConfig extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'academic_level',
        'display_name',
        'description',
        'class_identifiers',
        'numeric_range',
        'sort_order',
        'is_active',
        'auto_assign_mandatory_services',
        'created_by',
        'updated_by'
    ];

    protected $casts = [
        'class_identifiers' => 'array',
        'numeric_range' => 'array',
        'sort_order' => 'integer',
        'is_active' => 'boolean',
        'auto_assign_mandatory_services' => 'boolean'
    ];

    // Relationships
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function updater(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('display_name');
    }

    public function scopeByLevel($query, string $level)
    {
        return $query->where('academic_level', $level);
    }

    public function scopeWithAutoAssign($query)
    {
        return $query->where('auto_assign_mandatory_services', true);
    }

    // Helper methods for class identification
    public function matchesClassName(string $className): bool
    {
        if (!$this->class_identifiers) {
            return false;
        }

        // Direct string match
        if (in_array($className, $this->class_identifiers)) {
            return true;
        }

        // Case-insensitive match
        $lowerClassName = strtolower($className);
        $lowerIdentifiers = array_map('strtolower', $this->class_identifiers);
        if (in_array($lowerClassName, $lowerIdentifiers)) {
            return true;
        }

        // Numeric range check if available
        if ($this->numeric_range && is_numeric($className)) {
            $classNumber = (int) $className;
            $min = $this->numeric_range['min'] ?? null;
            $max = $this->numeric_range['max'] ?? null;
            
            if ($min !== null && $max !== null) {
                return $classNumber >= $min && $classNumber <= $max;
            }
        }

        // Pattern matching for common formats
        foreach ($this->class_identifiers as $identifier) {
            // Match patterns like "Class 1", "Grade 2", etc.
            if (preg_match('/^(class|grade)\s*(\d+)$/i', $identifier, $matches)) {
                $identifierNumber = (int) $matches[2];
                if (preg_match('/^(class|grade)?\s*' . $identifierNumber . '$/i', $className)) {
                    return true;
                }
            }
        }

        return false;
    }

    public function matchesClassNumber(int $classNumber): bool
    {
        // Check numeric range first
        if ($this->numeric_range) {
            $min = $this->numeric_range['min'] ?? null;
            $max = $this->numeric_range['max'] ?? null;
            
            if ($min !== null && $max !== null) {
                return $classNumber >= $min && $classNumber <= $max;
            }
        }

        // Check string identifiers
        return $this->matchesClassName((string) $classNumber);
    }

    public function addClassIdentifier(string $identifier): void
    {
        $identifiers = $this->class_identifiers ?? [];
        
        if (!in_array($identifier, $identifiers)) {
            $identifiers[] = $identifier;
            $this->update([
                'class_identifiers' => $identifiers,
                'updated_by' => auth()->id()
            ]);
        }
    }

    public function removeClassIdentifier(string $identifier): void
    {
        $identifiers = $this->class_identifiers ?? [];
        
        $filteredIdentifiers = array_filter($identifiers, function($item) use ($identifier) {
            return $item !== $identifier;
        });

        if (count($filteredIdentifiers) !== count($identifiers)) {
            $this->update([
                'class_identifiers' => array_values($filteredIdentifiers),
                'updated_by' => auth()->id()
            ]);
        }
    }

    public function updateNumericRange(int $min, int $max): void
    {
        $this->update([
            'numeric_range' => ['min' => $min, 'max' => $max],
            'updated_by' => auth()->id()
        ]);
    }

    // Static helper methods - Enhanced for Form-based detection
    public static function detectAcademicLevel(string $className): ?string
    {
        $className = trim($className);

        // Priority 1: Form-based detection (Form 1-4 = secondary)
        if (preg_match('/form\s*(\d+)/i', $className, $matches)) {
            $formNumber = (int) $matches[1];
            return match(true) {
                $formNumber >= 1 && $formNumber <= 4 => 'secondary',
                $formNumber >= 5 && $formNumber <= 6 => 'high_school',
                default => null
            };
        }

        // Priority 2: Enhanced numeric detection (Classes 1-8 = primary)
        if (preg_match('/(\d+)/', $className, $matches)) {
            $classNumber = (int) $matches[1];
            return match(true) {
                $classNumber >= 1 && $classNumber <= 8 => 'primary',
                $classNumber >= 9 && $classNumber <= 10 => 'secondary',
                $classNumber >= 11 && $classNumber <= 12 => 'high_school',
                $classNumber < 1 => 'kg',
                default => null
            };
        }

        // Priority 3: Config-based detection (for custom configurations)
        $configs = self::active()->ordered()->get();
        foreach ($configs as $config) {
            if ($config->matchesClassName($className)) {
                return $config->academic_level;
            }
        }

        return null;
    }

    public static function detectAcademicLevelFromNumber(int $classNumber): ?string
    {
        $configs = self::active()->ordered()->get();
        
        foreach ($configs as $config) {
            if ($config->matchesClassNumber($classNumber)) {
                return $config->academic_level;
            }
        }

        return null;
    }

    public static function getAvailableLevels(): Collection
    {
        return self::active()
            ->ordered()
            ->get()
            ->pluck('display_name', 'academic_level');
    }

    public static function getLevelConfig(string $academicLevel): ?self
    {
        return self::active()
            ->where('academic_level', $academicLevel)
            ->first();
    }

    public static function getDefaultConfiguration(): array
    {
        return [
            'kg' => [
                'display_name' => 'Kindergarten',
                'class_identifiers' => ['KG', 'PreK', 'Pre-K', 'Nursery', 'Pre-School'],
                'numeric_range' => ['min' => 0, 'max' => 0],
                'sort_order' => 1
            ],
            'primary' => [
                'display_name' => 'Primary School',
                'class_identifiers' => ['1', '2', '3', '4', '5', '6', '7', '8', 'Class 1', 'Class 2', 'Class 3', 'Class 4', 'Class 5', 'Class 6', 'Class 7', 'Class 8'],
                'numeric_range' => ['min' => 1, 'max' => 8],  // Updated: Classes 1-8 = Primary
                'sort_order' => 2
            ],
            'secondary' => [
                'display_name' => 'Secondary School',
                'class_identifiers' => ['Form 1', 'Form 2', 'Form 3', 'Form 4', 'Form I', 'Form II', 'Form III', 'Form IV', '9', '10', 'Class 9', 'Class 10'],
                'numeric_range' => ['min' => 9, 'max' => 10],  // Updated: Form 1-4 or Grades 9-10
                'sort_order' => 3
            ],
            'high_school' => [
                'display_name' => 'High School',
                'class_identifiers' => ['11', '12', 'Class 11', 'Class 12', 'Grade 11', 'Grade 12'],
                'numeric_range' => ['min' => 11, 'max' => 12],
                'sort_order' => 4
            ]
        ];
    }

    // Validation helpers
    public function isValidConfiguration(): array
    {
        $errors = [];

        if (!$this->academic_level) {
            $errors[] = 'Academic level is required';
        }

        if (!$this->display_name) {
            $errors[] = 'Display name is required';
        }

        if (!$this->class_identifiers || empty($this->class_identifiers)) {
            $errors[] = 'At least one class identifier is required';
        }

        if ($this->numeric_range) {
            $min = $this->numeric_range['min'] ?? null;
            $max = $this->numeric_range['max'] ?? null;
            
            if ($min !== null && $max !== null && $min > $max) {
                $errors[] = 'Numeric range minimum cannot be greater than maximum';
            }
        }

        return $errors;
    }

    // Accessor for formatted information
    public function getFormattedClassIdentifiersAttribute(): string
    {
        if (!$this->class_identifiers) {
            return 'None specified';
        }

        return implode(', ', $this->class_identifiers);
    }

    public function getFormattedNumericRangeAttribute(): string
    {
        if (!$this->numeric_range) {
            return 'Not specified';
        }

        $min = $this->numeric_range['min'] ?? '';
        $max = $this->numeric_range['max'] ?? '';

        if ($min && $max) {
            return "{$min} - {$max}";
        }

        return 'Invalid range';
    }

    public function getStatusBadgeAttribute(): string
    {
        return $this->is_active ? 'Active' : 'Inactive';
    }
}