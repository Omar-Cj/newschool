<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Updates report_parameters queries to add school_id filtering for multi-tenant data isolation.
     * Pattern: AND (:p_school_id IS NULL OR table.school_id = :p_school_id)
     * This allows System Admins (school_id=NULL) to see all data, while school users see only their data.
     *
     * @return void
     */
    public function up(): void
    {
        // ========================================
        // 1. SESSIONS (p_session_id)
        // ========================================

        // ID 28: p_session_id
        DB::table('report_parameters')->where('id', 28)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM sessions WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY id DESC',
                'source' => 'query'
            ])
        ]);

        // ID 34: p_session_id
        DB::table('report_parameters')->where('id', 34)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM sessions WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY id DESC',
                'source' => 'query'
            ])
        ]);

        // ID 39: p_session_id
        DB::table('report_parameters')->where('id', 39)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM sessions WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY id DESC',
                'source' => 'query'
            ])
        ]);

        // ========================================
        // 2. CLASSES (p_class_id)
        // ========================================

        $classIds = [18, 25, 31, 36, 41, 50, 58, 63, 68];
        foreach ($classIds as $id) {
            DB::table('report_parameters')->where('id', $id)->update([
                'values' => json_encode([
                    'query' => 'SELECT id AS value, name AS label FROM classes WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name',
                    'source' => 'query'
                ])
            ]);
        }

        // ========================================
        // 3. SHIFTS (p_shift_id)
        // ========================================

        // ID 21: p_shift_id
        DB::table('report_parameters')->where('id', 21)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM shifts WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name',
                'source' => 'query'
            ])
        ]);

        // ID 43: p_shift_id
        DB::table('report_parameters')->where('id', 43)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM shifts WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name',
                'source' => 'query'
            ])
        ]);

        // ID 52: p_shift_id
        DB::table('report_parameters')->where('id', 52)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM shifts WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name',
                'source' => 'query'
            ])
        ]);

        // ========================================
        // 4. EXAM TYPES (p_exam_type_id)
        // ========================================

        // ID 30: p_exam_type_id
        DB::table('report_parameters')->where('id', 30)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM exam_types WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name',
                'source' => 'query'
            ])
        ]);

        // ========================================
        // 5. STUDENT CATEGORIES (p_student_category_id)
        // ========================================

        // ID 44: p_category_id (student_categories)
        DB::table('report_parameters')->where('id', 44)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM student_categories WHERE (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name',
                'source' => 'query'
            ])
        ]);

        // ========================================
        // 6. SECTIONS (p_section_id) - Complex Joins
        // ========================================

        // These queries have joins with class_setups. Need to add school_id filter to sections table.
        // Pattern: Add filter to sections (s) table in WHERE clause

        $sectionQuery = 'SELECT s.id AS value, s.name AS label FROM sections s INNER JOIN class_setup_childrens csc ON s.id = csc.section_id INNER JOIN class_setups cs ON csc.class_setup_id = cs.id WHERE (:p_class_id IS NULL OR cs.classes_id = :p_class_id) AND s.status = 1 AND csc.status = 1 AND cs.status = 1 AND (:p_school_id IS NULL OR s.school_id = :p_school_id) ORDER BY s.name';

        $sectionIds = [19, 26, 32, 37, 42, 51, 59, 64, 69];
        foreach ($sectionIds as $id) {
            DB::table('report_parameters')->where('id', $id)->update([
                'values' => json_encode([
                    'source' => 'query',
                    'query' => $sectionQuery,
                    'depends_on' => 'p_class_id'
                ])
            ]);
        }

        // ========================================
        // 7. TERMS (p_term_id) - Complex Joins
        // ========================================

        // These queries have joins with term_definitions. Need to add school_id filter to terms table.

        $termQuery = 'SELECT t.id AS value, td.name AS label FROM terms t INNER JOIN term_definitions td ON t.term_definition_id = td.id WHERE (:p_session_id IS NULL OR t.session_id = :p_session_id) AND td.is_active = 1 AND (:p_school_id IS NULL OR t.school_id = :p_school_id) ORDER BY t.start_date DESC';

        // ID 29: p_term_id
        DB::table('report_parameters')->where('id', 29)->update([
            'values' => json_encode([
                'source' => 'query',
                'query' => $termQuery,
                'depends_on' => 'p_session_id'
            ])
        ]);

        // ID 35: p_term_id
        DB::table('report_parameters')->where('id', 35)->update([
            'values' => json_encode([
                'source' => 'query',
                'query' => $termQuery,
                'depends_on' => 'p_session_id'
            ])
        ]);

        // ========================================
        // 8. STUDENTS (p_student_id) - Complex Joins
        // ========================================

        // ID 33: p_student_id (filtered by class)
        DB::table('report_parameters')->where('id', 33)->update([
            'values' => json_encode([
                'source' => 'query',
                'query' => 'SELECT DISTINCT s.id AS value, CONCAT(s.first_name, \' \', s.last_name) AS label FROM students s INNER JOIN session_class_students scs   ON s.id = scs.student_id WHERE (:p_class_id IS NULL OR scs.classes_id = :p_class_id) AND s.status = 1 AND (:p_school_id IS NULL OR s.school_id = :p_school_id) ORDER BY label'
            ])
        ]);

        // ID 38: p_student_id (filtered by session, class, section)
        DB::table('report_parameters')->where('id', 38)->update([
            'values' => json_encode([
                'source' => 'query',
                'query' => 'SELECT DISTINCT s.id AS value, CONCAT(s.first_name, \' \', s.last_name) AS label FROM students s INNER JOIN session_class_students scs ON s.id = scs.student_id WHERE (:p_session_id IS NULL OR scs.session_id = :p_session_id) AND (:p_class_id IS NULL OR scs.classes_id = :p_class_id) AND (:p_section_id IS NULL OR scs.section_id = :p_section_id) AND s.status = 1 AND (:p_school_id IS NULL OR s.school_id = :p_school_id) ORDER BY label',
                'depends_on' => 'p_class_id'
            ])
        ]);

        // ========================================
        // 9. EXPENSE CATEGORIES (p_expense_category_id)
        // ========================================

        // ID 78: Already has branch filtering, add school_id filtering too
        DB::table('report_parameters')->where('id', 78)->update([
            'values' => json_encode([
                'source' => 'query',
                'query' => 'SELECT id AS value, name AS label FROM expense_categories WHERE status = 1 AND (:p_branch_id IS NULL OR branch_id = :p_branch_id) AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name',
                'depends_on' => 'p_branch_id'
            ])
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // ========================================
        // ROLLBACK: Remove school_id filtering
        // ========================================

        // 1. Sessions
        $sessionIds = [28, 34, 39];
        foreach ($sessionIds as $id) {
            DB::table('report_parameters')->where('id', $id)->update([
                'values' => json_encode([
                    'query' => 'SELECT id AS value, name AS label FROM sessions WHERE status = 1 ORDER BY id DESC',
                    'source' => 'query'
                ])
            ]);
        }

        // 2. Classes
        $classIds = [18, 25, 31, 36, 41, 50, 58, 63, 68];
        foreach ($classIds as $id) {
            DB::table('report_parameters')->where('id', $id)->update([
                'values' => json_encode([
                    'query' => 'SELECT id AS value, name AS label FROM classes WHERE status = 1 ORDER BY name',
                    'source' => 'query'
                ])
            ]);
        }

        // 3. Shifts
        $shiftIds = [21, 43, 52];
        foreach ($shiftIds as $id) {
            DB::table('report_parameters')->where('id', $id)->update([
                'values' => json_encode([
                    'query' => 'SELECT id AS value, name AS label FROM shifts WHERE status = 1 ORDER BY name',
                    'source' => 'query'
                ])
            ]);
        }

        // 4. Exam Types
        DB::table('report_parameters')->where('id', 30)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM exam_types WHERE status = 1 ORDER BY name',
                'source' => 'query'
            ])
        ]);

        // 5. Student Categories
        DB::table('report_parameters')->where('id', 44)->update([
            'values' => json_encode([
                'query' => 'SELECT id AS value, name AS label FROM student_categories ORDER BY name',
                'source' => 'query'
            ])
        ]);

        // 6. Sections (restore original with class dependency only)
        $sectionQuery = 'SELECT s.id AS value, s.name AS label FROM sections s INNER JOIN class_setup_childrens csc ON s.id = csc.section_id INNER JOIN class_setups cs ON csc.class_setup_id = cs.id WHERE (:p_class_id IS NULL OR cs.classes_id = :p_class_id) AND s.status = 1 AND csc.status = 1 AND cs.status = 1 ORDER BY s.name';

        $sectionIds = [19, 26, 32, 37, 42, 51, 59, 64, 69];
        foreach ($sectionIds as $id) {
            DB::table('report_parameters')->where('id', $id)->update([
                'values' => json_encode([
                    'source' => 'query',
                    'query' => $sectionQuery,
                    'depends_on' => 'p_class_id'
                ])
            ]);
        }

        // 7. Terms (restore original with session dependency only)
        $termQuery = 'SELECT t.id AS value, td.name AS label FROM terms t INNER JOIN term_definitions td ON t.term_definition_id = td.id WHERE (:p_session_id IS NULL OR t.session_id = :p_session_id) AND td.is_active = 1 ORDER BY t.start_date DESC';

        $termIds = [29, 35];
        foreach ($termIds as $id) {
            DB::table('report_parameters')->where('id', $id)->update([
                'values' => json_encode([
                    'source' => 'query',
                    'query' => $termQuery,
                    'depends_on' => 'p_session_id'
                ])
            ]);
        }

        // 8. Students (restore original)
        DB::table('report_parameters')->where('id', 33)->update([
            'values' => json_encode([
                'source' => 'query',
                'query' => 'SELECT DISTINCT s.id AS value, CONCAT(s.first_name, \' \', s.last_name) AS label FROM students s INNER JOIN session_class_students scs   ON s.id = scs.student_id WHERE (:p_class_id IS NULL OR scs.classes_id = :p_class_id) AND s.status = 1  ORDER BY label'
            ])
        ]);

        DB::table('report_parameters')->where('id', 38)->update([
            'values' => json_encode([
                'source' => 'query',
                'query' => 'SELECT DISTINCT s.id AS value, CONCAT(s.first_name, \' \', s.last_name) AS label FROM students s INNER JOIN session_class_students scs ON s.id = scs.student_id WHERE (:p_session_id IS NULL OR scs.session_id = :p_session_id) AND (:p_class_id IS NULL OR scs.classes_id = :p_class_id) AND (:p_section_id IS NULL OR scs.section_id = :p_section_id) AND s.status = 1 ORDER BY label',
                'depends_on' => 'p_class_id'
            ])
        ]);

        // 9. Expense Categories (restore original with branch only)
        DB::table('report_parameters')->where('id', 78)->update([
            'values' => json_encode([
                'source' => 'query',
                'query' => 'SELECT id AS value, name AS label FROM expense_categories WHERE status = 1 AND (:p_branch_id IS NULL OR branch_id = :p_branch_id) ORDER BY name',
                'depends_on' => 'p_branch_id'
            ])
        ]);
    }
};
