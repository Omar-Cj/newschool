<?php

namespace App\Models\Examination;

use App\Models\BaseModel;
use App\Models\Session;
use App\Models\Academic\Classes;
use App\Models\Academic\Section;
use App\Models\Academic\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamEntry extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'session_id',
        'term_id',
        'grade',
        'class_id',
        'section_id',
        'branch_id',
        'exam_type_id',
        'subject_id',
        'is_all_subjects',
        'entry_method',
        'upload_file_path',
        'total_marks',
        'status',
        'created_by',
        'published_at',
    ];

    protected $casts = [
        'is_all_subjects' => 'boolean',
        'total_marks' => 'float',
        'published_at' => 'datetime',
    ];

    /**
     * Get the session
     */
    public function session()
    {
        return $this->belongsTo(Session::class);
    }

    /**
     * Get the term
     */
    public function term()
    {
        return $this->belongsTo(Term::class);
    }

    /**
     * Get the class
     */
    public function class()
    {
        return $this->belongsTo(Classes::class, 'class_id');
    }

    /**
     * Get the section
     */
    public function section()
    {
        return $this->belongsTo(Section::class);
    }

    /**
     * Get the branch (multi-branch support)
     */
    public function branch()
    {
        if (!hasModule('MultiBranch')) {
            return null;
        }
        return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
    }

    /**
     * Get the exam type
     */
    public function examType()
    {
        return $this->belongsTo(ExamType::class);
    }

    /**
     * Get the subject (nullable for "all subjects")
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get all results for this exam entry
     */
    public function results()
    {
        return $this->hasMany(ExamEntryResult::class);
    }

    /**
     * Get the user who created this entry
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Scope for draft entries
     */
    public function scopeDraft($query)
    {
        return $query->where('status', 'draft');
    }

    /**
     * Scope for completed entries
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope for published entries
     */
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    /**
     * Scope by session
     */
    public function scopeBySession($query, $sessionId)
    {
        return $query->where('session_id', $sessionId);
    }

    /**
     * Scope by term
     */
    public function scopeByTerm($query, $termId)
    {
        return $query->where('term_id', $termId);
    }

    /**
     * Scope by class
     */
    public function scopeByClass($query, $classId)
    {
        return $query->where('class_id', $classId);
    }

    /**
     * Scope by exam type
     */
    public function scopeByExamType($query, $examTypeId)
    {
        return $query->where('exam_type_id', $examTypeId);
    }

    /**
     * Scope by branch (multi-branch support)
     */
    public function scopeByBranch($query, $branchId)
    {
        return $query->where('branch_id', $branchId);
    }

    /**
     * Scope by grade
     */
    public function scopeByGrade($query, $grade)
    {
        return $query->where('grade', $grade);
    }
}
