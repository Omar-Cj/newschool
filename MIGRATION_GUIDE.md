# School ID Migration Guide

## Overview
This document describes the comprehensive migration that adds `school_id` columns to all necessary tables in the School Management System.

## Migration File
**Location**: `database/migrations/tenant/2025_01_01_000001_add_school_id_to_all_tables.php`

## What This Migration Does

### Core Functionality
1. **Adds `school_id` column** to 101 application tables
2. **Default value**: 1 (for single-school installations)
3. **Column type**: `unsignedBigInteger` (non-nullable for business tables, nullable for `users` table)
4. **Placement**: After `id` column (when id exists)
5. **Indexing**: Automatic index creation for performance optimization
6. **Idempotency**: Safe to run multiple times - checks if columns already exist

### Tables Updated

#### Academic Management (12 tables)
- classes, sections, subjects
- class_setups, class_setup_childrens
- class_routines, class_routine_childrens
- session_class_students, sessions
- academic_level_configs, time_schedules, shifts

#### Student Management (8 tables)
- students, student_categories, student_services
- student_absent_notifications
- parent_guardians, parent_deposits
- parent_deposit_transactions, parent_balances

#### Staff Management (5 tables)
- staff, departments, designations
- leave_types, leave_requests

#### Financial Management (14 tables)
- fees_types, fees_groups, fees_masters, fees_master_childrens
- fees_assigns, fees_assign_childrens
- fees_collects, fees_generations, fees_generation_logs
- receipts, payment_transactions, payment_transaction_allocations
- receipt_number_reservations
- assign_fees_discounts, sibling_fees_discounts, early_payment_discounts

#### Examination System (16 tables)
- exam_types, exam_assigns, exam_assign_childrens
- exam_routines, exam_routine_childrens
- marks_grades, marks_registers, marks_register_childrens
- examination_results, examination_settings
- online_exams, online_exam_children_questions
- online_exam_children_students
- question_banks, question_bank_childrens, question_groups

#### Library Management (5 tables)
- books, book_categories
- members, member_categories
- issue_books

#### Attendance & Learning (4 tables)
- attendances, subject_attendances
- homework, homework_students

#### Communication & Events (13 tables)
- gmeets, certificates, id_cards
- notice_boards, events, news
- sliders, galleries, gallery_categories
- counters, abouts, pages, searches

#### Accounting (7 tables)
- account_heads, incomes, expenses
- expense_categories, cash_transfers
- terms, term_definitions

#### Communication Settings (3 tables)
- sms_mail_logs, sms_mail_templates
- system_notifications

#### Configuration (3 tables)
- settings, notification_settings
- online_admission_settings

#### Community Features (2 tables)
- forum_posts, forum_post_comments

#### Audit & Journals (2 tables)
- journals, journal_audit_logs

#### Users Table (Special Handling)
- `users` table receives **nullable** `school_id` column
- Allows for system administrators without school assignment

## Running the Migration

### Command
```bash
# For multi-tenant setup (runs in tenant context)
php artisan migrate --path=database/migrations/tenant

# Or specifically run this migration
php artisan migrate --path=database/migrations/tenant/2025_01_01_000001_add_school_id_to_all_tables.php
```

### Verification
After running the migration, verify the changes:

```bash
# List all columns in a table
php artisan tinker
Schema::getColumnListing('students')
```

## Rollback

If you need to revert the migration:

```bash
php artisan migrate:rollback --path=database/migrations/tenant
```

The rollback will:
1. Drop indexes for all `school_id` columns
2. Drop all `school_id` columns from business tables
3. Drop nullable `school_id` column from users table
4. Log all operations for auditing

## Error Handling

The migration includes robust error handling:

- **Table existence check**: Skips tables that don't exist (safe for partial installations)
- **Column existence check**: Won't re-add columns if they already exist (idempotent)
- **Index error handling**: Gracefully handles index drop failures in rollback
- **Comprehensive logging**: All operations logged to Laravel logs for debugging

### Common Issues

#### Issue: "Column already exists"
**Solution**: The migration checks for existing columns and skips them. This is expected behavior.

#### Issue: "Table doesn't exist"
**Solution**: The migration logs a warning and continues. This happens for disabled modules.

#### Issue: "Index doesn't exist on rollback"
**Solution**: The rollback catches these exceptions and logs warnings. This is normal.

## Multi-Tenancy Architecture

This migration enables true multi-tenancy at the application level:

### Before Migration
- All data in a tenant database mixed together
- No school isolation within data

### After Migration
- `school_id = 1`: Default/primary school in tenant
- `school_id > 1`: Multi-branch support
- All queries can filter by school_id for isolation
- Foreign key constraints can be added later if needed

## Usage in Application Code

### Query Examples
```php
// Get students for specific school
Student::where('school_id', auth()->user()->school_id)->get();

// Using global scope (recommended)
class Student extends Model
{
    protected static function booted()
    {
        static::addGlobalScope('school', function (Builder $query) {
            if (auth()->check() && auth()->user()->school_id) {
                $query->where('school_id', auth()->user()->school_id);
            }
        });
    }
}

// Bulk update for school
Student::where('school_id', $schoolId)->update(['status' => 'active']);
```

### Model Relationships
```php
class School extends Model
{
    public function students()
    {
        return $this->hasMany(Student::class);
    }

    public function staff()
    {
        return $this->hasMany(Staff::class);
    }
}

class Student extends Model
{
    public function school()
    {
        return $this->belongsTo(School::class);
    }
}
```

## Performance Considerations

### Indexing
- Each `school_id` column receives an index for fast queries
- Composite indexes should be added later if needed:
  ```php
  $table->index(['school_id', 'status']); // In future migrations
  ```

### Query Optimization
Always include `school_id` in WHERE clauses:
```php
// Good: Queries indexed columns
Student::where('school_id', $schoolId)
    ->where('status', 'active')
    ->get();

// Avoid: Missing school_id in filter
Student::where('status', 'active')->get(); // Could return all schools!
```

## Data Migration Strategy

If you have existing data in these tables:

1. **Single School**: All rows get `school_id = 1` (default)
2. **Multiple Schools**: Use a follow-up migration to update `school_id` based on business logic

Example follow-up migration:
```php
// database/migrations/tenant/2025_01_02_000002_populate_school_ids.php
public function up()
{
    // Map schools based on existing branch_id or other logic
    Student::where('school_id', 1)
        ->where('branch_id', 2)
        ->update(['school_id' => 2]);
}
```

## Next Steps

After this migration:

1. **Add Foreign Keys** (Optional, in future migration):
   ```php
   $table->foreign('school_id')->references('id')->on('schools')->cascadeOnDelete();
   ```

2. **Add Global Scopes** to models for automatic school filtering

3. **Add Validation** to ensure school_id consistency

4. **Add Authorization** to prevent cross-school data access

5. **Update Tests** to handle school_id in test data

## Troubleshooting

### Migration stuck or hanging
- Check if other migrations are running
- Kill the process: `php artisan queue:flush`
- Review logs: `tail -f storage/logs/laravel.log`

### Column type mismatch errors
- All columns use consistent `unsignedBigInteger` type
- Compatible with foreign keys to `schools` table (id column)

### Index issues
- Migration handles missing indexes gracefully
- Can be manually verified: `SHOW INDEXES FROM students WHERE Key_name = 'school_id';`

## Support

For issues or questions:
1. Check Laravel logs: `storage/logs/laravel.log`
2. Review this guide for common solutions
3. Run with verbose flag for more details: `php artisan migrate --verbose`

---

**Created**: 2025-01-01
**Status**: Production-Ready
**Reversible**: Yes (via rollback)
**Idempotent**: Yes (safe to run multiple times)
