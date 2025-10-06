<?php

namespace App\Models\Examination;

use App\Models\BaseModel;
use App\Models\Academic\Subject;
use App\Models\StudentInfo\Student;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ExamEntryResult extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'exam_entry_id',
        'student_id',
        'subject_id',
        'obtained_marks',
        'grade',
        'remarks',
        'is_absent',
        'entry_source',
        'entered_by',
    ];

    protected $casts = [
        'obtained_marks' => 'float',
        'is_absent' => 'boolean',
    ];

    /**
     * Get the exam entry
     */
    public function examEntry()
    {
        return $this->belongsTo(ExamEntry::class);
    }

    /**
     * Get the student
     */
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the subject
     */
    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    /**
     * Get the user who entered this result
     */
    public function enteredBy()
    {
        return $this->belongsTo(User::class, 'entered_by');
    }

    /**
     * Scope for manual entry results
     */
    public function scopeManualEntry($query)
    {
        return $query->where('entry_source', 'manual');
    }

    /**
     * Scope for excel uploaded results
     */
    public function scopeExcelEntry($query)
    {
        return $query->where('entry_source', 'excel');
    }

    /**
     * Scope for absent students
     */
    public function scopeAbsent($query)
    {
        return $query->where('is_absent', true);
    }

    /**
     * Scope for present students
     */
    public function scopePresent($query)
    {
        return $query->where('is_absent', false);
    }

    /**
     * Scope by student
     */
    public function scopeByStudent($query, $studentId)
    {
        return $query->where('student_id', $studentId);
    }

    /**
     * Scope by subject
     */
    public function scopeBySubject($query, $subjectId)
    {
        return $query->where('subject_id', $subjectId);
    }
}
