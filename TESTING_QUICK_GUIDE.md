# Multi-Tenant Testing Quick Guide

## 🚀 Quick Start

### **Step 1: Run Migration** (5 minutes)

```bash
# From project root
php artisan migrate --path=database/migrations/2025_11_09_000001_add_school_id_to_critical_tables.php

# Clear caches
php artisan cache:clear && php artisan config:clear && php artisan view:clear

# Verify migration
php artisan db:seed --class=VerifySchoolIdColumnsSeeder
```

**Expected Output:**
- ✅ Migration adds school_id to 67 tables
- ✅ Data populated from existing relationships
- ✅ Verification shows all critical tables have school_id

---

## 🧪 Critical Tests (15 minutes)

### **Test 1: Logo Isolation** (MOST IMPORTANT)

#### **Before Fix:**
- School users saw system admin's logo ❌
- Settings showed wrong school's data ❌

#### **After Fix (Expected):**
```
1. Login as School 1 Admin → Should see School 1's logo ✅
2. Login as School 2 Admin → Should see School 2's logo ✅
3. Login as System Admin → Should see system logo ✅
```

#### **How to Test:**
```bash
# Test as School 1 User
1. Navigate to: General Settings
2. Upload logo: school1_logo.png
3. Refresh page - verify logo appears in sidebar
4. Logout

# Test as School 2 User
1. Navigate to: General Settings
2. Should NOT see school1_logo.png ✅
3. Upload logo: school2_logo.png
4. Verify it's different from School 1

# Test as System Admin
1. Navigate to: MainApp General Settings
2. Upload system_logo.png
3. Verify separate from school logos
```

---

### **Test 2: Data Isolation**

```bash
# Login as School 1 Admin
- Dashboard student count → Should ONLY count School 1 students
- Student list → Should ONLY show School 1 students
- Financial reports → Should ONLY show School 1 transactions

# Verify in database:
mysql -u root -p'321' school_new

# Run this query while logged in as School 1 user:
SELECT COUNT(*) FROM students;  -- Should match dashboard count
```

---

### **Test 3: Session Contamination**

```bash
# Critical security test:
1. Login as School 1 Admin
2. Note: Student count = X
3. Logout
4. Immediately login as School 2 Admin
5. Verify: Student count ≠ X (should be School 2's count)
6. Check browser console for no errors
```

---

## 🔍 Database Verification

### **Quick Checks:**

```sql
-- 1. Branches have school_id
SELECT id, name, school_id FROM branches;
-- Should show: All branches with valid school_id (no NULLs)

-- 2. Uploads have school_id
SELECT id, LEFT(path, 50) as path, branch_id, school_id FROM uploads LIMIT 10;
-- Should show: All uploads with school_id matching their branch's school_id

-- 3. System Admin is properly configured
SELECT id, name, email, role_id, school_id FROM users WHERE role_id = 0;
-- Should show: System admin with school_id = NULL

-- 4. School admins have school_id
SELECT id, name, email, role_id, school_id FROM users WHERE role_id = 1;
-- Should show: All school admins with valid school_id
```

---

## ✅ Success Criteria

### **All must pass:**

- [ ] Migration completes without errors
- [ ] School 1 users see ONLY School 1 data
- [ ] School 2 users see ONLY School 2 data
- [ ] Logos are properly isolated per school
- [ ] System admin can access all schools
- [ ] No session contamination between users
- [ ] Dashboard statistics are school-specific
- [ ] Settings are school-specific
- [ ] No SQL errors in logs

---

## 🚨 Common Issues

### **Issue:** "Column 'school_id' not found"

**Solution:**
```bash
# Migration not run
php artisan migrate --path=database/migrations/2025_11_09_000001_add_school_id_to_critical_tables.php
```

### **Issue:** School users still see wrong logo

**Solution:**
```bash
# Clear cache
php artisan cache:clear
php artisan config:clear
php artisan view:clear

# Verify uploads table has school_id
mysql -u root -p'321' -e "DESCRIBE uploads" school_new
```

### **Issue:** System admin can't access schools

**Solution:**
```sql
-- Fix system admin user
UPDATE users SET school_id = NULL WHERE role_id = 0;
```

---

## 📊 Expected Results Summary

| User Type | school_id | Can See |
|-----------|-----------|---------|
| System Admin | NULL | ALL schools |
| School 1 Admin | 1 | ONLY School 1 |
| School 2 Admin | 2 | ONLY School 2 |
| School 1 Teacher | 1 | ONLY School 1 |

---

## 🎯 Quick Validation Commands

```bash
# 1. Check migration status
php artisan migrate:status | grep "2025_11_09_000001"

# 2. Count tables with school_id
mysql -u root -p'321' -e "
SELECT COUNT(*) as tables_with_school_id
FROM information_schema.COLUMNS
WHERE TABLE_SCHEMA = 'school_new'
AND COLUMN_NAME = 'school_id'
" school_new

# Expected: ~165 tables (98 existing + 67 from migration)

# 3. Verify critical tables
for table in branches uploads exam_entries subject_assigns; do
  echo "Checking $table..."
  mysql -u root -p'321' -e "DESCRIBE $table" school_new | grep school_id
done

# All should show school_id column
```

---

## 📝 Testing Checklist

```
MIGRATION:
[ ] Migration ran successfully
[ ] No errors in migration output
[ ] Verification seeder confirms all tables

SYSTEM ADMIN:
[ ] Can login to MainApp dashboard
[ ] Can view all schools list
[ ] Can create new school
[ ] Sees system-level settings/logo

SCHOOL ISOLATION:
[ ] School 1 admin sees only School 1 data
[ ] School 2 admin sees only School 2 data
[ ] Logos are properly isolated
[ ] No cross-school data visible

SECURITY:
[ ] Session cleanup works between users
[ ] No security warnings in logs
[ ] Cannot access other school's data via URL manipulation

DATABASE:
[ ] All critical tables have school_id
[ ] Foreign keys are set up
[ ] Data population completed correctly
```

---

**Time Estimate:** 20-30 minutes total
**Priority:** HIGH - Fixes critical logo/settings isolation bug

For detailed documentation, see: `claudedocs/MULTI_TENANT_IMPLEMENTATION_SUMMARY.md`
