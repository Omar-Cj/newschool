<?php

namespace App\Models\StudentInfo;

use App\Models\Academic\SubjectAssignChildren;
use App\Models\BaseModel;
use App\Models\Session;
use App\Models\Academic\Shift;
use App\Models\HomeworkStudent;
use App\Models\Academic\Classes;
use App\Models\Academic\Section;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Log;

class SessionClassStudent extends BaseModel
{
    use HasFactory;

    protected $fillable = [
        'session_id',
        'student_id', 
        'classes_id',
        'section_id',
        'shift_id',
        'roll'
    ];

    /**
     * Boot the model and add event listeners
     */
    protected static function boot()
    {
        parent::boot();

        // Before creating a new session class student record
        static::creating(function ($model) {
            $class = Classes::find($model->classes_id);
            
            if ($class && !$class->hasAcademicLevel()) {
                $student = Student::find($model->student_id);
                $studentName = $student ? $student->full_name : 'Unknown Student';
                
                Log::warning('Student assigned to class without academic level', [
                    'student_id' => $model->student_id,
                    'student_name' => $studentName,
                    'class_id' => $model->classes_id,
                    'class_name' => $class->name,
                    'warning' => 'Fee assignment may be inconsistent without explicit academic level',
                    'recommendation' => 'Assign academic level to class using: php artisan classes:assign-academic-levels'
                ]);
                
                // Flash warning to session for immediate user feedback
                if (session()->exists()) {
                    session()->flash('warning', 
                        "Warning: Class '{$class->name}' does not have an academic level assigned. " .
                        "This may cause issues with fee assignment. Please assign an academic level to this class."
                    );
                }
            }
        });
    }

    public function subjectAssignChildren()
    {
        return $this->hasMany(SubjectAssignChildren::class, 'subject_assign_id', 'classes_id'); // Adjust as necessary
    }


    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id', 'id');
    }
    public function session()
    {
        return $this->belongsTo(Session::class, 'session_id', 'id');
    }
    public function class()
    {
        return $this->belongsTo(Classes::class, 'classes_id', 'id');
    }
    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
    public function shift()
    {
        return $this->belongsTo(Shift::class, 'shift_id', 'id');
    }

    public function homeworkStudent()
    {
        return $this->belongsTo(HomeworkStudent::class, 'student_id', 'student_id');
    }

}
