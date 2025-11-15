# Quick Reference - Report Parameters School Filter Update

## 📋 Summary
- **Total Parameters Updated**: 31
- **Entity Types**: 9 (Sessions, Classes, Sections, Shifts, Terms, Exam Types, Student Categories, Students, Expense Categories)
- **Filter Pattern**: `AND (:p_school_id IS NULL OR table.school_id = :p_school_id)`

## 🚀 Deployment Methods

### Method 1: Laravel Migration (Recommended for Multi-Tenant SaaS)
```bash
# Apply to all tenants
php artisan tenants:migrate

# Or for single school
php artisan migrate --path=database/migrations/tenant
```

### Method 2: Direct SQL Execution
```bash
# Forward (apply changes)
mysql -u root -p school_management < database/sql/add_school_id_filtering_to_report_parameters.sql

# Rollback (revert changes)
mysql -u root -p school_management < database/sql/rollback_school_id_filtering_from_report_parameters.sql
```

## ✅ Verification Query
```sql
-- Check all 31 parameters were updated
SELECT
    COUNT(*) as total_updated,
    SUM(CASE WHEN JSON_UNQUOTE(JSON_EXTRACT(`values`, '$.query')) LIKE '%:p_school_id%' THEN 1 ELSE 0 END) as has_filter
FROM report_parameters
WHERE id IN (28, 34, 39, 18, 25, 31, 36, 41, 50, 58, 63, 68, 21, 43, 52, 30, 44,
             19, 26, 32, 37, 42, 51, 59, 64, 69, 29, 35, 33, 38, 78);
-- Expected: total_updated = 31, has_filter = 31
```

## 📊 Updated Parameter IDs by Category

| Category | IDs | Count |
|----------|-----|-------|
| Sessions | 28, 34, 39 | 3 |
| Classes | 18, 25, 31, 36, 41, 50, 58, 63, 68 | 9 |
| Sections | 19, 26, 32, 37, 42, 51, 59, 64, 69 | 9 |
| Shifts | 21, 43, 52 | 3 |
| Terms | 29, 35 | 2 |
| Exam Types | 30 | 1 |
| Student Categories | 44 | 1 |
| Students | 33, 38 | 2 |
| Expense Categories | 78 | 1 |

## 🔒 Security Impact
- **CRITICAL FIX**: Closes multi-tenant data leak
- System Admins (school_id=NULL) → See all schools
- School Users (school_id=X) → See only their school

## ⚡ Performance Impact
- Minimal - uses indexed school_id column
- Faster queries for school users (smaller dataset)

## 🔄 Rollback Options

### Option 1: Migration Rollback
```bash
php artisan migrate:rollback --path=database/migrations/tenant --step=1
```

### Option 2: SQL Script Rollback
```bash
mysql -u root -p school_management < database/sql/rollback_school_id_filtering_from_report_parameters.sql
```

## 📝 Testing Checklist
- [ ] Backup report_parameters table
- [ ] Verify all tables have school_id column
- [ ] Run forward script
- [ ] Run verification query (expect 31/31)
- [ ] Test as System Admin (see all schools)
- [ ] Test as School User (see only their school)
- [ ] Test dependent dropdowns (sections, terms)
- [ ] Have rollback script ready

## 🛠️ Files Created

1. **Migration**: `/database/migrations/tenant/2025_01_14_add_school_id_filtering_to_report_parameters.php`
2. **Forward SQL**: `/database/sql/add_school_id_filtering_to_report_parameters.sql`
3. **Rollback SQL**: `/database/sql/rollback_school_id_filtering_from_report_parameters.sql`
4. **Full Documentation**: `/database/sql/REPORT_PARAMETERS_SCHOOL_FILTER_SUMMARY.md`
5. **Quick Reference**: `/database/sql/QUICK_REFERENCE.md` (this file)

## 🔍 Sample Before/After

### Before
```sql
SELECT id AS value, name AS label
FROM classes
WHERE status = 1
ORDER BY name
```

### After
```sql
SELECT id AS value, name AS label
FROM classes
WHERE status = 1
AND (:p_school_id IS NULL OR school_id = :p_school_id)
ORDER BY name
```

## ⚠️ Important Notes
- Original query syntax preserved exactly
- No frontend changes required
- Zero breaking changes
- ParameterValueResolver handles :p_school_id automatically
- All tables MUST have school_id column

## 📞 Support
See full documentation in `REPORT_PARAMETERS_SCHOOL_FILTER_SUMMARY.md` for:
- Detailed query transformations
- Troubleshooting guide
- Complete testing procedures
- Index creation recommendations
