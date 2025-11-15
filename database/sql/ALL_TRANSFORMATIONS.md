# All Query Transformations - Report Parameters School Filter

Complete listing of all 31 parameter query transformations showing BEFORE and AFTER.

---

## 1. SESSIONS (p_session_id) - 3 Parameters

### ID 28: p_session_id

**BEFORE:**
```sql
SELECT id AS value, name AS label FROM sessions WHERE status = 1 ORDER BY id DESC
```

**AFTER:**
```sql
SELECT id AS value, name AS label FROM sessions WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY id DESC
```

---

### ID 34: p_session_id

**BEFORE:**
```sql
SELECT id AS value, name AS label FROM sessions WHERE status = 1 ORDER BY id DESC
```

**AFTER:**
```sql
SELECT id AS value, name AS label FROM sessions WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY id DESC
```

---

### ID 39: p_session_id

**BEFORE:**
```sql
SELECT id AS value, name AS label FROM sessions WHERE status = 1 ORDER BY id DESC
```

**AFTER:**
```sql
SELECT id AS value, name AS label FROM sessions WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY id DESC
```

---

## 2. CLASSES (p_class_id) - 9 Parameters

### IDs: 18, 25, 31, 36, 41, 50, 58, 63, 68

**BEFORE:**
```sql
SELECT id AS value, name AS label FROM classes WHERE status = 1 ORDER BY name
```

**AFTER:**
```sql
SELECT id AS value, name AS label FROM classes WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name
```

**Applied to**: All 9 class parameter IDs identically

---

## 3. SECTIONS (p_section_id) - 9 Parameters (Complex Joins)

### IDs: 19, 26, 32, 37, 42, 51, 59, 64, 69

**BEFORE:**
```sql
SELECT s.id AS value, s.name AS label
FROM sections s
INNER JOIN class_setup_childrens csc ON s.id = csc.section_id
INNER JOIN class_setups cs ON csc.class_setup_id = cs.id
WHERE (:p_class_id IS NULL OR cs.classes_id = :p_class_id)
AND s.status = 1
AND csc.status = 1
AND cs.status = 1
ORDER BY s.name
```

**AFTER:**
```sql
SELECT s.id AS value, s.name AS label
FROM sections s
INNER JOIN class_setup_childrens csc ON s.id = csc.section_id
INNER JOIN class_setups cs ON csc.class_setup_id = cs.id
WHERE (:p_class_id IS NULL OR cs.classes_id = :p_class_id)
AND s.status = 1
AND csc.status = 1
AND cs.status = 1
AND (:p_school_id IS NULL OR s.school_id = :p_school_id)
ORDER BY s.name
```

**Changes:**
- Added school_id filter to sections table (aliased as `s`)
- Preserves existing class dependency
- Maintains all JOIN logic

**JSON Structure:**
```json
{
    "source": "query",
    "query": "[QUERY_HERE]",
    "depends_on": "p_class_id"
}
```

---

## 4. SHIFTS (p_shift_id) - 3 Parameters

### ID 21: p_shift_id

**BEFORE:**
```sql
SELECT id AS value, name AS label FROM shifts WHERE status = 1 ORDER BY name
```

**AFTER:**
```sql
SELECT id AS value, name AS label FROM shifts WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name
```

---

### ID 43: p_shift_id

**BEFORE:**
```sql
SELECT id AS value, name AS label FROM shifts WHERE status = 1 ORDER BY name
```

**AFTER:**
```sql
SELECT id AS value, name AS label FROM shifts WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name
```

---

### ID 52: p_shift_id

**BEFORE:**
```sql
SELECT id AS value, name AS label FROM shifts WHERE status = 1 ORDER BY name
```

**AFTER:**
```sql
SELECT id AS value, name AS label FROM shifts WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name
```

---

## 5. TERMS (p_term_id) - 2 Parameters (Complex Joins)

### ID 29: p_term_id

**BEFORE:**
```sql
SELECT t.id AS value, td.name AS label
FROM terms t
INNER JOIN term_definitions td ON t.term_definition_id = td.id
WHERE (:p_session_id IS NULL OR t.session_id = :p_session_id)
AND td.is_active = 1
ORDER BY t.start_date DESC
```

**AFTER:**
```sql
SELECT t.id AS value, td.name AS label
FROM terms t
INNER JOIN term_definitions td ON t.term_definition_id = td.id
WHERE (:p_session_id IS NULL OR t.session_id = :p_session_id)
AND td.is_active = 1
AND (:p_school_id IS NULL OR t.school_id = :p_school_id)
ORDER BY t.start_date DESC
```

**Changes:**
- Added school_id filter to terms table (aliased as `t`)
- Preserves existing session dependency
- Maintains term_definitions join

**JSON Structure:**
```json
{
    "source": "query",
    "query": "[QUERY_HERE]",
    "depends_on": "p_session_id"
}
```

---

### ID 35: p_term_id

**BEFORE:**
```sql
SELECT t.id AS value, td.name AS label
FROM terms t
INNER JOIN term_definitions td ON t.term_definition_id = td.id
WHERE (:p_session_id IS NULL OR t.session_id = :p_session_id)
AND td.is_active = 1
ORDER BY t.start_date DESC
```

**AFTER:**
```sql
SELECT t.id AS value, td.name AS label
FROM terms t
INNER JOIN term_definitions td ON t.term_definition_id = td.id
WHERE (:p_session_id IS NULL OR t.session_id = :p_session_id)
AND td.is_active = 1
AND (:p_school_id IS NULL OR t.school_id = :p_school_id)
ORDER BY t.start_date DESC
```

---

## 6. EXAM TYPES (p_exam_type_id) - 1 Parameter

### ID 30: p_exam_type_id

**BEFORE:**
```sql
SELECT id AS value, name AS label FROM exam_types WHERE status = 1 ORDER BY name
```

**AFTER:**
```sql
SELECT id AS value, name AS label FROM exam_types WHERE status = 1 AND (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name
```

---

## 7. STUDENT CATEGORIES (p_student_category_id) - 1 Parameter

### ID 44: p_category_id

**BEFORE:**
```sql
SELECT id AS value, name AS label FROM student_categories ORDER BY name
```

**AFTER:**
```sql
SELECT id AS value, name AS label FROM student_categories WHERE (:p_school_id IS NULL OR school_id = :p_school_id) ORDER BY name
```

**Note:** Original query had no `WHERE status = 1` clause, so school_id filter is the only WHERE condition.

---

## 8. STUDENTS (p_student_id) - 2 Parameters (Complex Joins)

### ID 33: p_student_id (Filtered by Class)

**BEFORE:**
```sql
SELECT DISTINCT s.id AS value, CONCAT(s.first_name, ' ', s.last_name) AS label
FROM students s
INNER JOIN session_class_students scs ON s.id = scs.student_id
WHERE (:p_class_id IS NULL OR scs.classes_id = :p_class_id)
AND s.status = 1
ORDER BY label
```

**AFTER:**
```sql
SELECT DISTINCT s.id AS value, CONCAT(s.first_name, ' ', s.last_name) AS label
FROM students s
INNER JOIN session_class_students scs ON s.id = scs.student_id
WHERE (:p_class_id IS NULL OR scs.classes_id = :p_class_id)
AND s.status = 1
AND (:p_school_id IS NULL OR s.school_id = :p_school_id)
ORDER BY label
```

**Changes:**
- Added school_id filter to students table (aliased as `s`)
- Preserves DISTINCT for duplicate prevention
- Maintains CONCAT for full name display
- Preserves existing class dependency

**JSON Structure:**
```json
{
    "source": "query",
    "query": "[QUERY_HERE]"
}
```

---

### ID 38: p_student_id (Filtered by Session, Class, Section)

**BEFORE:**
```sql
SELECT DISTINCT s.id AS value, CONCAT(s.first_name, ' ', s.last_name) AS label
FROM students s
INNER JOIN session_class_students scs ON s.id = scs.student_id
WHERE (:p_session_id IS NULL OR scs.session_id = :p_session_id)
AND (:p_class_id IS NULL OR scs.classes_id = :p_class_id)
AND (:p_section_id IS NULL OR scs.section_id = :p_section_id)
AND s.status = 1
ORDER BY label
```

**AFTER:**
```sql
SELECT DISTINCT s.id AS value, CONCAT(s.first_name, ' ', s.last_name) AS label
FROM students s
INNER JOIN session_class_students scs ON s.id = scs.student_id
WHERE (:p_session_id IS NULL OR scs.session_id = :p_session_id)
AND (:p_class_id IS NULL OR scs.classes_id = :p_class_id)
AND (:p_section_id IS NULL OR scs.section_id = :p_section_id)
AND s.status = 1
AND (:p_school_id IS NULL OR s.school_id = :p_school_id)
ORDER BY label
```

**Changes:**
- Added school_id filter to students table (aliased as `s`)
- Preserves all three existing dependencies (session, class, section)
- Maintains DISTINCT and CONCAT logic

**JSON Structure:**
```json
{
    "source": "query",
    "query": "[QUERY_HERE]",
    "depends_on": "p_class_id"
}
```

---

## 9. EXPENSE CATEGORIES (p_expense_category_id) - 1 Parameter (Complex)

### ID 78: p_expense_category_id

**BEFORE:**
```sql
SELECT id AS value, name AS label
FROM expense_categories
WHERE status = 1
AND (:p_branch_id IS NULL OR branch_id = :p_branch_id)
ORDER BY name
```

**AFTER:**
```sql
SELECT id AS value, name AS label
FROM expense_categories
WHERE status = 1
AND (:p_branch_id IS NULL OR branch_id = :p_branch_id)
AND (:p_school_id IS NULL OR school_id = :p_school_id)
ORDER BY name
```

**Changes:**
- Added school_id filter alongside existing branch filter
- Preserves existing branch dependency
- Both filters work together (branch AND school)

**JSON Structure:**
```json
{
    "source": "query",
    "query": "[QUERY_HERE]",
    "depends_on": "p_branch_id"
}
```

---

## Summary Statistics

### Total Changes: 31 Parameters

| Category | Count | Type | Notes |
|----------|-------|------|-------|
| Sessions | 3 | Simple | Direct WHERE clause addition |
| Classes | 9 | Simple | Direct WHERE clause addition |
| Sections | 9 | Complex | JOIN query with table alias |
| Shifts | 3 | Simple | Direct WHERE clause addition |
| Terms | 2 | Complex | JOIN query with table alias |
| Exam Types | 1 | Simple | Direct WHERE clause addition |
| Student Categories | 1 | Simple | Added WHERE clause (none existed) |
| Students | 2 | Complex | JOIN query with CONCAT and DISTINCT |
| Expense Categories | 1 | Complex | Multiple filters (branch + school) |

### Filter Placement Analysis

**Simple Queries (17 parameters):**
- Pattern: Added `AND (:p_school_id IS NULL OR school_id = :p_school_id)` before `ORDER BY`
- Tables: sessions, classes, shifts, exam_types, student_categories

**Complex Queries with Joins (14 parameters):**
- Pattern: Added `AND (:p_school_id IS NULL OR table_alias.school_id = :p_school_id)` before `ORDER BY`
- Preserved all existing JOINs and dependencies
- Used proper table aliases (s., t.) for clarity
- Tables: sections, terms, students, expense_categories

### Syntax Preservation Checklist

✅ All column aliases preserved (`AS value`, `AS label`)
✅ All table aliases preserved (`s`, `scs`, `cs`, `csc`, `t`, `td`)
✅ All JOIN syntax unchanged
✅ All ORDER BY clauses unchanged
✅ All existing dependencies maintained (`depends_on`)
✅ All DISTINCT, CONCAT functions preserved
✅ All quote styles unchanged
✅ Filter consistently added BEFORE ORDER BY in all cases

---

**Generated**: 2025-01-14
**Author**: Backend Architect
**Purpose**: Complete transformation reference for report parameters school filtering update
