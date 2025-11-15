-- ========================================
-- Add school_id Filtering to Report Parameters
-- ========================================
-- Purpose: Add multi-tenant data isolation by filtering report parameter queries with school_id
-- Pattern: AND (:p_school_id IS NULL OR table.school_id = :p_school_id)
-- This allows System Admins (school_id=NULL) to see all data, while school users see only their data
--
-- Date: 2025-01-14
-- Author: Backend Architect
-- ========================================

-- ========================================
-- 1. SESSIONS (p_session_id)
-- IDs: 28, 34, 39
-- ========================================

UPDATE report_parameters
SET `values` = JSON_SET(
    `values`,
    '$.query',
    'SELECT id AS value, name AS label FROM sessions WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY id DESC'
)
WHERE id = 28;

UPDATE report_parameters
SET `values` = JSON_SET(
    `values`,
    '$.query',
    'SELECT id AS value, name AS label FROM sessions WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY id DESC'
)
WHERE id = 34;

UPDATE report_parameters
SET `values` = JSON_SET(
    `values`,
    '$.query',
    'SELECT id AS value, name AS label FROM sessions WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY id DESC'
)
WHERE id = 39;

-- ========================================
-- 2. CLASSES (p_class_id)
-- IDs: 18, 25, 31, 36, 41, 50, 58, 63, 68
-- ========================================

UPDATE report_parameters
SET `values` = JSON_SET(
    `values`,
    '$.query',
    'SELECT id AS value, name AS label FROM classes WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name'
)
WHERE id IN (18, 25, 31, 36, 41, 50, 58, 63, 68);

-- ========================================
-- 3. SHIFTS (p_shift_id)
-- IDs: 21, 43, 52
-- ========================================

UPDATE report_parameters
SET `values` = JSON_SET(
    `values`,
    '$.query',
    'SELECT id AS value, name AS label FROM shifts WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name'
)
WHERE id IN (21, 43, 52);

-- ========================================
-- 4. EXAM TYPES (p_exam_type_id)
-- ID: 30
-- ========================================

UPDATE report_parameters
SET `values` = JSON_SET(
    `values`,
    '$.query',
    'SELECT id AS value, name AS label FROM exam_types WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name'
)
WHERE id = 30;

-- ========================================
-- 5. STUDENT CATEGORIES (p_student_category_id)
-- ID: 44
-- ========================================

UPDATE report_parameters
SET `values` = JSON_SET(
    `values`,
    '$.query',
    'SELECT id AS value, name AS label FROM student_categories WHERE (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name'
)
WHERE id = 44;

-- ========================================
-- 6. SECTIONS (p_section_id) - Complex Joins
-- IDs: 19, 26, 32, 37, 42, 51, 59, 64, 69
-- Note: Added school_id filter to sections (s) table
-- ========================================

UPDATE report_parameters
SET `values` = JSON_SET(
    JSON_SET(
        JSON_SET(
            `values`,
            '$.query',
            'SELECT s.id AS value, s.name AS label FROM sections s INNER JOIN class_setup_childrens csc ON s.id = csc.section_id INNER JOIN class_setups cs ON csc.class_setup_id = cs.id WHERE (:p_class_id IS NULL OR cs.classes_id = :p_class_id) AND s.status = 1 AND csc.status = 1 AND cs.status = 1 AND (:p_school_id IS NULL OR s.school_id = :p_school_id) ORDER BY s.name'
        ),
        '$.source',
        'query'
    ),
    '$.depends_on',
    'p_class_id'
)
WHERE id IN (19, 26, 32, 37, 42, 51, 59, 64, 69);

-- ========================================
-- 7. TERMS (p_term_id) - Complex Joins
-- IDs: 29, 35
-- Note: Added school_id filter to terms (t) table
-- ========================================

UPDATE report_parameters
SET `values` = JSON_SET(
    JSON_SET(
        JSON_SET(
            `values`,
            '$.query',
            'SELECT t.id AS value, td.name AS label FROM terms t INNER JOIN term_definitions td ON t.term_definition_id = td.id WHERE (:p_session_id IS NULL OR t.session_id = :p_session_id) AND td.is_active = 1 AND (:p_school_id IS NULL OR t.school_id = :p_school_id) ORDER BY t.start_date DESC'
        ),
        '$.source',
        'query'
    ),
    '$.depends_on',
    'p_session_id'
)
WHERE id IN (29, 35);

-- ========================================
-- 8. STUDENTS (p_student_id) - Complex Joins
-- IDs: 33, 38
-- Note: Added school_id filter to students (s) table
-- ========================================

-- ID 33: Student filtered by class
UPDATE report_parameters
SET `values` = JSON_SET(
    JSON_SET(
        `values`,
        '$.query',
        'SELECT DISTINCT s.id AS value, CONCAT(s.first_name, \' \', s.last_name) AS label FROM students s INNER JOIN session_class_students scs   ON s.id = scs.student_id WHERE (:p_class_id IS NULL OR scs.classes_id = :p_class_id) AND s.status = 1 AND (:p_school_id IS NULL OR s.school_id = :p_school_id) ORDER BY label'
    ),
    '$.source',
    'query'
)
WHERE id = 33;

-- ID 38: Student filtered by session, class, section
UPDATE report_parameters
SET `values` = JSON_SET(
    JSON_SET(
        JSON_SET(
            `values`,
            '$.query',
            'SELECT DISTINCT s.id AS value, CONCAT(s.first_name, \' \', s.last_name) AS label FROM students s INNER JOIN session_class_students scs ON s.id = scs.student_id WHERE (:p_session_id IS NULL OR scs.session_id = :p_session_id) AND (:p_class_id IS NULL OR scs.classes_id = :p_class_id) AND (:p_section_id IS NULL OR scs.section_id = :p_section_id) AND s.status = 1 AND (:p_school_id IS NULL OR s.school_id = :p_school_id) ORDER BY label'
        ),
        '$.source',
        'query'
    ),
    '$.depends_on',
    'p_class_id'
)
WHERE id = 38;

-- ========================================
-- 9. EXPENSE CATEGORIES (p_expense_category_id)
-- ID: 78
-- Note: Added school_id filter alongside existing branch filter
-- ========================================

UPDATE report_parameters
SET `values` = JSON_SET(
    JSON_SET(
        JSON_SET(
            `values`,
            '$.query',
            'SELECT id AS value, name AS label FROM expense_categories WHERE status = 1 AND (:p_branch_id IS NULL OR branch_id = :p_branch_id) AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name'
        ),
        '$.source',
        'query'
    ),
    '$.depends_on',
    'p_branch_id'
)
WHERE id = 78;

-- ========================================
-- VERIFICATION QUERIES
-- Run these to verify the updates
-- ========================================

-- Check all updated parameters
SELECT
    id,
    name,
    JSON_UNQUOTE(JSON_EXTRACT(`values`, '$.query')) AS query
FROM report_parameters
WHERE id IN (
    -- Sessions
    28, 34, 39,
    -- Classes
    18, 25, 31, 36, 41, 50, 58, 63, 68,
    -- Shifts
    21, 43, 52,
    -- Exam Types
    30,
    -- Student Categories
    44,
    -- Sections
    19, 26, 32, 37, 42, 51, 59, 64, 69,
    -- Terms
    29, 35,
    -- Students
    33, 38,
    -- Expense Categories
    78
)
ORDER BY id;

-- Count of updated records
SELECT
    'Total Parameters Updated' AS description,
    COUNT(*) AS count
FROM report_parameters
WHERE id IN (28, 34, 39, 18, 25, 31, 36, 41, 50, 58, 63, 68, 21, 43, 52, 30, 44, 19, 26, 32, 37, 42, 51, 59, 64, 69, 29, 35, 33, 38, 78);
