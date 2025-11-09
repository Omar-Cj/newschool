<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * Upload Model - School-Isolated File/Media Management
 *
 * IMPORTANT: Extends BaseModel to enable automatic school_id filtering via SchoolScope.
 * All uploads (logos, images, documents) are automatically isolated by school.
 *
 * School Context Behavior:
 * - School Users (school_id NOT NULL): See ONLY their school's uploads
 * - System Admin (school_id NULL): See uploads from ALL schools
 *
 * This ensures that:
 * - School logos are properly isolated
 * - Each school sees only their uploaded files
 * - Settings with file paths show correct school-specific files
 *
 * After migration 2025_11_09_000001, the uploads table will have school_id column
 * populated from the branch relationship for proper multi-tenant isolation.
 */
class Upload extends BaseModel
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'school_id',  // Added for multi-tenant file isolation
        'branch_id',  // Existing branch association
        'path',       // File path
    ];

    /**
     * Get the school this upload belongs to
     */
    public function school()
    {
        return $this->belongsTo(\Modules\MainApp\Entities\School::class);
    }

    /**
     * Get the branch this upload belongs to (if MultiBranch enabled)
     */
    public function branch()
    {
        if (!hasModule('MultiBranch')) {
            return null;
        }
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }
}
