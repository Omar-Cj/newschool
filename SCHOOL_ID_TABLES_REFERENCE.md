# School ID Tables Reference

## Migration Details
- **File**: `database/migrations/tenant/2025_01_01_000001_add_school_id_to_all_tables.php`
- **Total Tables**: 101 business tables + 1 users table
- **Column Type**: `unsignedBigInteger`
- **Default Value**: 1
- **Indexed**: Yes (automatic index on all school_id columns)
- **Nullable**: No (except on users table)

## Complete Table List by Category

### Academic Management (12 tables)
```
classes
├── id, school_id, name, section_id, ...
sections
├── id, school_id, name, class_id, ...
subjects
├── id, school_id, name, code, ...
class_setups
├── id, school_id, ...
class_setup_childrens
├── id, school_id, class_setup_id, ...
class_routines
├── id, school_id, ...
class_routine_childrens
├── id, school_id, class_routine_id, ...
session_class_students
├── id, school_id, session_id, class_id, student_id, ...
sessions
├── id, school_id, name, start_date, end_date, ...
academic_level_configs
├── id, school_id, level_id, ...
time_schedules
├── id, school_id, ...
shifts
├── id, school_id, name, ...
```

### Student Management (8 tables)
```
students
├── id, school_id, name, email, enrollment_number, ...
student_categories
├── id, school_id, name, ...
student_services
├── id, school_id, student_id, ...
student_absent_notifications
├── id, school_id, student_id, ...
parent_guardians
├── id, school_id, student_id, guardian_name, ...
parent_deposits
├── id, school_id, parent_id, ...
parent_deposit_transactions
├── id, school_id, deposit_id, ...
parent_balances
├── id, school_id, parent_id, balance, ...
```

### Staff Management (5 tables)
```
staff
├── id, school_id, name, designation_id, department_id, ...
departments
├── id, school_id, name, ...
designations
├── id, school_id, name, ...
leave_types
├── id, school_id, name, days, ...
leave_requests
├── id, school_id, staff_id, leave_type_id, ...
```

### Financial Management (14 tables)
```
fees_types
├── id, school_id, name, ...
fees_groups
├── id, school_id, name, ...
fees_masters
├── id, school_id, class_id, ...
fees_master_childrens
├── id, school_id, fees_master_id, ...
fees_assigns
├── id, school_id, student_id, ...
fees_assign_childrens
├── id, school_id, fees_assign_id, ...
fees_collects
├── id, school_id, fees_assign_id, ...
fees_generations
├── id, school_id, ...
fees_generation_logs
├── id, school_id, ...
receipts
├── id, school_id, student_id, amount, ...
payment_transactions
├── id, school_id, receipt_id, ...
payment_transaction_allocations
├── id, school_id, transaction_id, ...
receipt_number_reservations
├── id, school_id, ...
assign_fees_discounts
├── id, school_id, fees_assign_id, ...
sibling_fees_discounts
├── id, school_id, student_id, ...
early_payment_discounts
├── id, school_id, fees_assign_id, ...
```

### Examination System (16 tables)
```
exam_types
├── id, school_id, name, ...
exam_assigns
├── id, school_id, exam_id, class_id, ...
exam_assign_childrens
├── id, school_id, exam_assign_id, ...
exam_routines
├── id, school_id, exam_id, ...
exam_routine_childrens
├── id, school_id, exam_routine_id, ...
marks_grades
├── id, school_id, mark_from, mark_to, grade, ...
marks_registers
├── id, school_id, exam_id, class_id, subject_id, ...
marks_register_childrens
├── id, school_id, marks_register_id, student_id, ...
examination_results
├── id, school_id, student_id, exam_id, ...
examination_settings
├── id, school_id, ...
online_exams
├── id, school_id, exam_id, ...
online_exam_children_questions
├── id, school_id, online_exam_id, ...
online_exam_children_students
├── id, school_id, online_exam_id, student_id, ...
question_banks
├── id, school_id, subject_id, ...
question_bank_childrens
├── id, school_id, question_bank_id, ...
question_groups
├── id, school_id, ...
```

### Library Management (5 tables)
```
books
├── id, school_id, title, isbn, ...
book_categories
├── id, school_id, name, ...
members
├── id, school_id, name, membership_id, ...
member_categories
├── id, school_id, name, ...
issue_books
├── id, school_id, book_id, member_id, issue_date, ...
```

### Attendance & Learning (4 tables)
```
attendances
├── id, school_id, student_id, attendance_date, status, ...
subject_attendances
├── id, school_id, student_id, subject_id, ...
homework
├── id, school_id, class_id, subject_id, ...
homework_students
├── id, school_id, homework_id, student_id, ...
```

### Communication & Events (13 tables)
```
gmeets
├── id, school_id, class_id, ...
certificates
├── id, school_id, student_id, ...
id_cards
├── id, school_id, student_id, ...
notice_boards
├── id, school_id, title, content, ...
events
├── id, school_id, title, event_date, ...
news
├── id, school_id, title, content, ...
sliders
├── id, school_id, title, image, ...
galleries
├── id, school_id, name, ...
gallery_categories
├── id, school_id, name, ...
counters
├── id, school_id, title, count, ...
abouts
├── id, school_id, title, description, ...
pages
├── id, school_id, title, slug, ...
searches
├── id, school_id, search_query, ...
```

### Accounting (7 tables)
```
account_heads
├── id, school_id, name, code, ...
incomes
├── id, school_id, account_head_id, amount, ...
expenses
├── id, school_id, category_id, amount, ...
expense_categories
├── id, school_id, name, ...
cash_transfers
├── id, school_id, from_account, to_account, ...
terms
├── id, school_id, name, start_date, end_date, ...
term_definitions
├── id, school_id, term_id, definition, ...
```

### Communication Settings (3 tables)
```
sms_mail_logs
├── id, school_id, recipient, message_type, ...
sms_mail_templates
├── id, school_id, name, template, ...
system_notifications
├── id, school_id, user_id, message, ...
```

### Configuration (3 tables)
```
settings
├── id, school_id, key, value, ...
notification_settings
├── id, school_id, setting_key, setting_value, ...
online_admission_settings
├── id, school_id, setting_key, ...
```

### Community Features (2 tables)
```
forum_posts
├── id, school_id, user_id, title, content, ...
forum_post_comments
├── id, school_id, post_id, user_id, comment, ...
```

### Audit & Journals (2 tables)
```
journals
├── id, school_id, user_id, action, ...
journal_audit_logs
├── id, school_id, entity_type, entity_id, ...
```

### Special: Users Table (1 table)
```
users
├── id, school_id (NULLABLE), name, email, ...
```

## Index Information

All tables receive an automatic index:
```sql
-- Example for students table
CREATE INDEX idx_school_id ON students(school_id);

-- This enables fast queries like:
SELECT * FROM students WHERE school_id = 1;
```

## Migration Execution

### Command
```bash
php artisan migrate --path=database/migrations/tenant/2025_01_01_000001_add_school_id_to_all_tables.php
```

### What Happens
1. Checks if each table exists
2. Checks if school_id column already exists
3. Adds school_id column if missing
4. Creates index if column is newly added
5. Logs all operations to Laravel logs

### Verification Commands
```php
// In Laravel Tinker
php artisan tinker

// Check if column exists
Schema::hasColumn('students', 'school_id')  // true

// Get column info
Schema::getColumnListing('students')

// Check indexes
Schema::getIndexes('students')
```

## SQL Examples

### After Migration - Common Queries

```sql
-- Get all students for a school
SELECT * FROM students WHERE school_id = 1;

-- Count students per school
SELECT school_id, COUNT(*) as total FROM students GROUP BY school_id;

-- Update school_id in bulk
UPDATE students SET school_id = 2 WHERE branch_id = 2;

-- Join across schools (not recommended)
SELECT s.*, c.name FROM students s
LEFT JOIN classes c ON s.class_id = c.id AND s.school_id = c.school_id;

-- Delete all data for a school
DELETE FROM students WHERE school_id = 5;
```

## Data Consistency

### Single School Migration
If you have a single school:
- All new rows will have `school_id = 1` (default)
- Existing rows already have `school_id = 1`
- No additional data migration needed

### Multi-School Migration
If you have multiple schools:
1. First run this migration
2. Then run a follow-up migration to populate school_id based on your business logic
3. Example:
   ```php
   // Map schools based on existing branch_id
   DB::statement('UPDATE students SET school_id = branch_id WHERE school_id = 1 AND branch_id > 1');
   ```

## Performance Impact

### Minimal Impact During Migration
- Added as non-nullable columns with defaults
- Indexed immediately for fast access
- No data transformation required

### Query Performance
- Queries filtering by school_id benefit from automatic index
- No performance degradation for existing queries
- Recommend adding composite indexes later:
  ```php
  $table->index(['school_id', 'status']);
  $table->index(['school_id', 'class_id', 'status']);
  ```

## Rollback Information

To remove all school_id columns:
```bash
php artisan migrate:rollback --path=database/migrations/tenant
```

This will:
1. Drop all indexes created by the migration
2. Drop all school_id columns from business tables
3. Drop nullable school_id from users table
4. Restore database to pre-migration state

---

**Last Updated**: 2025-01-01
**Status**: Ready for Production
