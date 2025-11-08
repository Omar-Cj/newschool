# Migration: Populate school_id for Existing Data

**File**: `database/migrations/tenant/2025_11_05_000002_populate_school_id_for_existing_data.php`

**Purpose**: Populate existing database records with `school_id = 1` (default school) where school_id is null or 0.

---

## Overview

This migration handles the data population phase after the `school_id` column has been added to tables by the previous migration (`2025_01_01_000001_add_school_id_to_all_tables.php`).

### Key Responsibilities:

1. **Batch Updates**: Uses `DB::statement()` and `DB::table()` for efficient batch updates instead of updating records individually
2. **Comprehensive Coverage**: Updates 130+ tables across all system domains
3. **Special User Handling**: Implements role-based logic for the users table
4. **Safety Checks**: Verifies table and column existence before attempting updates
5. **Detailed Logging**: Logs all operations for auditing and troubleshooting

---

## Migration Logic

### Standard Tables (130+ tables)

For all tables except `users`, the migration:

1. Checks if table exists in database
2. Verifies `school_id` column is present
3. Updates all records where `school_id IS NULL OR school_id = 0` to `school_id = 1`
4. Logs the number of records updated

**SQL Operation**:
```sql
UPDATE table_name
SET school_id = 1
WHERE school_id IS NULL OR school_id = 0;
```

### Users Table (Special Handling)

The users table has role-based logic:

- **Regular Users** (role_id != 1 or NULL):
  - Set `school_id = 1` (assigned to default school)
  - Logs: "Updated X regular users with school_id = 1"

- **Admin Users** (role_id = 1):
  - Keep `school_id = NULL` (not assigned to any specific school)
  - Logs: "X admin users kept with NULL school_id"

**SQL Operation for Regular Users**:
```sql
UPDATE users
SET school_id = 1
WHERE (school_id IS NULL OR school_id = 0)
  AND (role_id != 1 OR role_id IS NULL);
```

---

## Tables Updated

### Academic Domain (12 tables)
- classes
- sections
- subjects
- class_setups
- class_setup_childrens
- class_routines
- class_routine_childrens
- session_class_students
- sessions
- academic_level_configs
- time_schedules
- shifts

### Student Domain (8 tables)
- students
- student_categories
- student_services
- student_absent_notifications
- parent_guardians
- parent_deposits
- parent_deposit_transactions
- parent_balances

### Staff Domain (5 tables)
- staff
- departments
- designations
- leave_types
- leave_requests

### Fees Domain (16 tables)
- fees_types
- fees_groups
- fees_masters
- fees_master_childrens
- fees_assigns
- fees_assign_childrens
- fees_collects
- fees_generations
- fees_generation_logs
- receipts
- payment_transactions
- payment_transaction_allocations
- receipt_number_reservations
- assign_fees_discounts
- sibling_fees_discounts
- early_payment_discounts

### Exam Domain (17 tables)
- exam_types
- exam_assigns
- exam_assign_childrens
- exam_routines
- exam_routine_childrens
- marks_grades
- marks_registers
- marks_register_childrens
- examination_results
- examination_settings
- online_exams
- online_exam_children_questions
- online_exam_children_students
- question_banks
- question_bank_childrens
- question_groups

### Library Domain (5 tables)
- books
- book_categories
- members
- member_categories
- issue_books

### Attendance & Homework Domain (4 tables)
- attendances
- subject_attendances
- homework
- homework_students

### Communication Domain (11 tables)
- gmeets
- certificates
- id_cards
- notice_boards
- events
- news
- sliders
- galleries
- gallery_categories
- counters
- pages
- searches

### Accounting Domain (7 tables)
- account_heads
- incomes
- expenses
- expense_categories
- cash_transfers
- terms
- term_definitions

### Settings Domain (3 tables)
- settings
- notification_settings
- online_admission_settings

### Communication Settings Domain (3 tables)
- sms_mail_logs
- sms_mail_templates
- system_notifications

### Forum Domain (2 tables)
- forum_posts
- forum_post_comments

### Journal Domain (2 tables)
- journals
- journal_audit_logs

### Special Table (1 table)
- users (with role-based logic)

**Total: 131 tables**

---

## Performance Characteristics

### Optimization Strategies:

1. **Batch Updates**: Uses database-level batch updates instead of individual record updates
   - Single UPDATE query per table instead of N queries for N records
   - Reduces memory usage and execution time significantly

2. **Conditional Updates**: Only updates records where school_id is null or 0
   - Skips already-populated records
   - Minimizes unnecessary updates

3. **Early Table Checks**: Verifies table and column existence before queries
   - Prevents errors on missing tables/columns
   - Allows graceful handling of optional features

4. **Logging**: Detailed logging for monitoring and troubleshooting
   - Records number of rows updated per table
   - Logs warnings for missing tables
   - Critical errors with full context

### Expected Performance:

- **Small Database** (< 100k total records): < 10 seconds
- **Medium Database** (100k - 1M records): 10-60 seconds
- **Large Database** (> 1M records): 1-5 minutes

---

## Execution

### Running the Migration:

```bash
# For multi-tenant setup (individual tenant)
php artisan module:migrate MainApp

# For single school setup
php artisan migrate
```

### Pre-Migration Checklist:

1. Backup database
2. Verify `2025_01_01_000001_add_school_id_to_all_tables.php` has run successfully
3. Confirm no active user operations (off-peak time recommended)
4. Check available disk space (for logs)
5. Monitor database connections

### Post-Migration Verification:

```bash
# Check migration history
php artisan migrate:status

# Verify school_id population via artisan tinker
php artisan tinker
> DB::table('students')->whereNull('school_id')->count()  // Should return 0
> DB::table('staff')->where('school_id', 0)->count()       // Should return 0
> DB::table('users')->where('role_id', '=', 1)->where('school_id', '!=', null)->count() // Should return 0
```

---

## Rollback Behavior

If rolled back, the migration:

1. Resets all `school_id` values to NULL for all 130+ tables
2. Resets users table `school_id` to NULL regardless of role
3. Maintains data integrity (no records deleted)
4. Allows re-running migration if needed

**Rollback Command**:
```bash
php artisan migrate:rollback --step=1
```

---

## Error Handling

### Table Not Found

**Message**: `Table '{table_name}' does not exist, skipping population`

**Action**: Logged as warning, migration continues with next table

**Resolution**: This is expected for optional/conditional modules

### Column Not Found

**Message**: `Table '{table_name}' does not have school_id column`

**Action**: Logged as warning, migration continues

**Resolution**: Verify that `2025_01_01_000001_add_school_id_to_all_tables.php` ran first

### Update Failure

**Message**: `Error updating school_id in '{table}': [error details]`

**Action**: Logged as error, migration throws exception and rolls back

**Resolution**: Check logs for specific error, fix issue, re-run migration

### Database Connection Error

**Message**: `Critical error during school_id population: [error details]`

**Action**: Migration fails, database transaction rolls back

**Resolution**: Check database connection, permissions, and disk space

---

## Logging Output

The migration produces detailed logs in `storage/logs/laravel.log`:

### Success Log Format:

```log
[2025-11-05 10:30:15] local.INFO: Starting population of school_id for existing data
[2025-11-05 10:30:15] local.INFO: Table 'students': Updated 150 records with school_id = 1 (Total records: 150)
[2025-11-05 10:30:16] local.INFO: Table 'staff': Updated 45 records with school_id = 1 (Total records: 45)
[2025-11-05 10:30:17] local.INFO: Processing users table with role-based school_id logic
[2025-11-05 10:30:17] local.INFO: Users table: Updated 45 regular users with school_id = 1
[2025-11-05 10:30:17] local.INFO: Users table: 2 admin users kept with NULL school_id (role_id = 1)
[2025-11-05 10:30:17] local.INFO: Users table: Total users processed = 47
[2025-11-05 10:30:20] local.INFO: Successfully completed population of school_id for existing data
```

### Warning Log Format:

```log
[2025-11-05 10:30:15] local.WARNING: Table 'optional_feature_table' does not exist, skipping population
```

---

## Multi-Tenant Considerations

### For SaaS Multi-Tenant Deployment:

1. **Run per Tenant**: Execute migration for each tenant separately
   ```bash
   php artisan tenants:migrate --step=2
   ```

2. **Isolated Data**: Each tenant's school_id = 1 represents their default/primary school

3. **Admin Users**: Admins (role_id = 1) remain with NULL school_id in each tenant

### For Single School Installation:

1. **Run Once**: Execute migration once for the entire installation
   ```bash
   php artisan migrate --path=database/migrations/tenant
   ```

2. **All Data**: All records populated with school_id = 1 (single school context)

---

## Troubleshooting

### Issue: Migration Hangs

**Cause**: Large number of records or slow database

**Solution**:
1. Check database performance (indexes, locks)
2. Monitor with `SHOW PROCESSLIST` in MySQL
3. Run migration during off-peak hours
4. Consider chunking if database is very large (modify migration)

### Issue: Partial Update

**Cause**: Network interruption or timeout

**Solution**:
1. Verify which tables were updated (check logs)
2. Rollback migration: `php artisan migrate:rollback --step=1`
3. Re-run migration after fixing issue

### Issue: School_id Still NULL After Migration

**Cause**: Migration didn't run or table missing

**Solution**:
1. Check `migrations` table: `SELECT * FROM migrations WHERE migration LIKE '%populate_school_id%'`
2. Verify table exists: `SHOW TABLES LIKE 'students'`
3. Verify column exists: `SHOW COLUMNS FROM students WHERE Field = 'school_id'`
4. Run migration: `php artisan migrate`

### Issue: Admin Users Getting school_id = 1

**Cause**: role_id is missing or incorrect

**Solution**:
1. Check users table: `SELECT id, role_id, school_id FROM users WHERE role_id = 1`
2. Verify role_id = 1 is admin: `SELECT * FROM roles WHERE id = 1`
3. Manually fix: `UPDATE users SET school_id = NULL WHERE role_id = 1`
4. Re-run migration if needed

---

## Implementation Checklist

Before running migration:
- [ ] Database is backed up
- [ ] `2025_01_01_000001_add_school_id_to_all_tables.php` has run
- [ ] No active user sessions (off-peak time)
- [ ] Disk space available (20% free minimum)
- [ ] Database connections available (30% capacity free)

After running migration:
- [ ] Check migration status: `php artisan migrate:status`
- [ ] Verify logs: `tail -100 storage/logs/laravel.log`
- [ ] Test data access from application
- [ ] Verify multi-school functionality (if applicable)
- [ ] Monitor application performance

---

## Related Migrations

**Prerequisite**:
- `2025_01_01_000001_add_school_id_to_all_tables.php` - Adds school_id columns

**No dependent migrations** - This is a data population migration that doesn't affect schema

---

## Notes for Developers

### When Adding New Tables with school_id:

1. Add table name to `getSchoolIdTables()` method in both migrations:
   - `2025_01_01_000001_add_school_id_to_all_tables.php`
   - `2025_11_05_000002_populate_school_id_for_existing_data.php`

2. If special logic needed (like users table), create separate handling in `up()` and `down()` methods

3. Always include logging for audit trail

### Performance Optimization Tips:

If migration is too slow:
1. Add indexes to school_id columns (already done in `2025_01_01_000001`)
2. Chunk updates by date if tables are huge:
   ```php
   DB::table('large_table')
       ->where('created_at', '<', now()->subMonths(6))
       ->update(['school_id' => 1]);
   ```
3. Run during maintenance window (low traffic)
4. Increase `max_execution_time` and `max_allowed_packet` in MySQL

### Testing in Development:

```php
// In artisan tinker or test
DB::table('students')->update(['school_id' => null]); // Reset
php artisan migrate:rollback --step=1
php artisan migrate --step=2  // Re-run both migrations

// Verify results
DB::table('students')->whereNull('school_id')->count()  // Should be 0
```

