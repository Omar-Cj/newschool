<?php

namespace App\Imports;

use App\Models\StudentInfo\Student;
use App\Models\StudentInfo\SessionClassStudent;
use App\Models\Academic\Subject;
use App\Models\Academic\SubjectAssign;
use App\Models\Academic\SubjectAssignChildren;
use Maatwebsite\Excel\Concerns\ToArray;
use Maatwebsite\Excel\Concerns\WithHeadingRow;

class ExamEntryImport implements ToArray, WithHeadingRow
{
    protected $params;
    protected $students = [];
    protected $subjects = [];
    protected $results = [];
    protected $errors = [];

    public function __construct($params)
    {
        $this->params = $params;

        // Load subjects for validation
        if ($params['subject_id'] === 'all') {
            $subjectAssign = SubjectAssign::where('session_id', $params['session_id'])
                ->where('classes_id', $params['class_id'])
                ->where('section_id', $params['section_id'])
                ->first();

            if ($subjectAssign) {
                $this->subjects = SubjectAssignChildren::where('subject_assign_id', $subjectAssign->id)
                    ->with('subject')
                    ->get()
                    ->pluck('subject')
                    ->map(function($subject) {
                        return [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'total_marks' => $this->params['total_marks'] ?? 100
                        ];
                    });
            }
        } else {
            $subject = Subject::find($params['subject_id']);
            if ($subject) {
                $this->subjects = collect([[
                    'id' => $subject->id,
                    'name' => $subject->name,
                    'total_marks' => $this->params['total_marks'] ?? 100
                ]]);
            }
        }
    }

    public function array(array $rows)
    {
        foreach ($rows as $rowIndex => $row) {
            try {
                // Skip empty rows
                if (empty($row['student_id'])) {
                    continue;
                }

                // Validate student ID
                $studentId = $row['student_id'];
                $student = Student::find($studentId);

                if (!$student) {
                    $this->errors[] = "Row " . ($rowIndex + 2) . ": Invalid student ID";
                    continue;
                }

                // Verify student belongs to the class and section through SessionClassStudent
                $sessionClassStudent = SessionClassStudent::where('session_id', $this->params['session_id'])
                    ->where('student_id', $studentId)
                    ->where('classes_id', $this->params['class_id'])
                    ->where('section_id', $this->params['section_id'])
                    ->first();

                if (!$sessionClassStudent) {
                    $this->errors[] = "Row " . ($rowIndex + 2) . ": Student does not belong to selected class/section for this session";
                    continue;
                }

                // Add student to list if not already added
                if (!in_array($studentId, array_column($this->students, 'id'))) {
                    $this->students[] = [
                        'id' => $student->id,
                        'name' => $student->full_name,
                    ];
                }

                // Process marks for each subject
                foreach ($this->subjects as $subject) {
                    $subjectName = strtolower(str_replace(' ', '_', $subject['name']));

                    // Try to find the marks column
                    $marks = null;
                    foreach ($row as $key => $value) {
                        if (strtolower(str_replace(' ', '_', $key)) === $subjectName) {
                            $marks = $value;
                            break;
                        }
                    }

                    // Validate marks
                    if ($marks !== null && $marks !== '') {
                        if (!is_numeric($marks)) {
                            $this->errors[] = "Row " . ($rowIndex + 2) . ": Invalid marks for " . $subject['name'];
                            continue;
                        }

                        $totalMarks = $subject['total_marks'];
                        if ($marks < 0 || $marks > $totalMarks) {
                            $this->errors[] = "Row " . ($rowIndex + 2) . ": Marks for " . $subject['name'] . " must be between 0 and " . $totalMarks;
                            continue;
                        }

                        // Add to results
                        $this->results[] = [
                            'student_id' => $studentId,
                            'subject_id' => $subject['id'],
                            'obtained_marks' => floatval($marks),
                            'is_absent' => false,
                            'entry_source' => 'excel'
                        ];
                    }
                }

            } catch (\Exception $e) {
                $this->errors[] = "Row " . ($rowIndex + 2) . ": " . $e->getMessage();
            }
        }

        // Throw exception if there are errors
        if (!empty($this->errors)) {
            throw new \Exception(implode("\n", $this->errors));
        }
    }

    public function getStudents()
    {
        return collect($this->students);
    }

    public function getSubjects()
    {
        return $this->subjects;
    }

    public function getResults()
    {
        return collect($this->results);
    }

    public function getErrors()
    {
        return $this->errors;
    }
}
