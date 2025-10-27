# Student Bulk Import V2 - Testing Guide

## Quick Start Testing

### Prerequisites
1. Ensure you have admin access to the system
2. Navigate to Student Management section
3. Access the Import functionality

### Test Scenario 1: Basic Import with Required Fields Only

**Steps:**
1. Navigate to **Students → Import**
2. You should see the Somali instruction: **"Fadlan lasoo dag sample fileka kadib kusoo Buuxi Xogta ardayda"**
3. Click **"Sample File"** button
4. Download opens `student_import_template.csv`
5. Open the CSV file

**Create test data:**
```csv
first_name,last_name,parent_mobile,shift,gender,category,mobile,email,username,date_of_birth,admission_date,parent_name,parent_relation,fee_services
Ali,Mohamed,33111111,,,,,,,,,Amina Mohamed,Mother,
Fatima,Hassan,33222222,,,,,,,,,Ahmed Hassan,Father,
Omar,Ali,33333333,,,,,,,,,Khadija Ali,Mother,
```

**Import process:**
1. Select **Grade**: Grade1
2. Select **Class**: From available classes
3. Select **Section**: From available sections
4. Upload your CSV file
5. Click **Submit**

**Expected Results:**
- ✅ Import successful message
- ✅ 3 students created
- ✅ 3 parents created
- ✅ All students assigned to Grade1, selected class & section
- ✅ Mandatory services auto-assigned based on Grade1

**Verification:**
1. Go to **Students → Student List**
2. Filter by the class/section you imported to
3. Verify all 3 students appear
4. Click on each student to verify:
   - Correct name
   - Correct parent assignment
   - Grade field shows "Grade1"
   - Mandatory services present in fees tab

---

### Test Scenario 2: Import with Optional Fields

**Create advanced test data:**
```csv
first_name,last_name,parent_mobile,shift,gender,category,mobile,email,username,date_of_birth,admission_date,parent_name,parent_relation,fee_services
Ahmed,Yusuf,33444444,1,1,1,33555555,ahmed.yusuf@test.com,ahmed_2024,2015-05-10,2024-09-01,Halima Yusuf,Mother,
Maryam,Ibrahim,33666666,2,2,2,33777777,maryam.ibrahim@test.com,maryam_2024,2015-08-15,2024-09-01,Fatima Ibrahim,Mother,
```

**Note:** Replace shift, gender, category with actual IDs from your system.

**Import process:**
1. Select **Grade**: Grade2
2. Select **Class** and **Section**
3. Upload the CSV file
4. Click **Submit**

**Expected Results:**
- ✅ Both students imported with all details
- ✅ Gender assigned correctly (1=Male for Ahmed, 2=Female for Maryam)
- ✅ Custom usernames preserved
- ✅ Email addresses saved
- ✅ Date of birth and admission date set correctly
- ✅ Shifts assigned

**Verification:**
1. Check student profiles for complete information
2. Verify login works with username and default password (123456)
3. Verify emails are unique

---

### Test Scenario 3: Parent Duplication Prevention (Siblings)

**Purpose:** Test that siblings are correctly linked to the same parent

**Create sibling test data:**
```csv
first_name,last_name,parent_mobile,shift,gender,category,mobile,email,username,date_of_birth,admission_date,parent_name,parent_relation,fee_services
Hassan,Ahmed,33888888,1,1,1,33999991,hassan.ahmed@test.com,,2015-06-20,2024-09-01,Amina Ahmed,Mother,
Zainab,Ahmed,33888888,1,2,1,33999992,zainab.ahmed@test.com,,2016-03-15,2024-09-01,Amina Ahmed,Mother,
Khalid,Ahmed,33888888,1,1,1,33999993,khalid.ahmed@test.com,,2017-01-10,2024-09-01,Amina Ahmed,Mother,
```

**Note:** All three students have the **same parent_mobile** (33888888)

**Import process:**
1. Select **Grade**: KG-2
2. Select **Class** and **Section**
3. Upload CSV
4. Submit

**Expected Results:**
- ✅ 3 students created
- ✅ **Only 1 parent** created (Amina Ahmed with mobile 33888888)
- ✅ All 3 students linked to the same parent

**Verification:**
1. Go to **Parent/Guardian List**
2. Search for mobile "33888888"
3. **Only ONE parent** should exist
4. View parent details
5. Verify all 3 children (Hassan, Zainab, Khalid) are listed under this parent

---

### Test Scenario 4: Fee Services Assignment

**Purpose:** Test optional fee service assignment during import

**Prerequisites:**
- Get optional fee service IDs from **Fees → Fee Types**
- Note down some optional service IDs (e.g., 3, 5, 7)

**Create test data with services:**
```csv
first_name,last_name,parent_mobile,shift,gender,category,mobile,email,username,date_of_birth,admission_date,parent_name,parent_relation,fee_services
Yasmin,Omar,33101010,1,2,1,33202020,yasmin.omar@test.com,,2015-09-12,2024-09-01,Sara Omar,Mother,"3,5,7"
```

**Note:** Replace "3,5,7" with actual optional service IDs from your system

**Import process:**
1. Select **Grade**: Form1
2. Select **Class** and **Section**
3. Upload CSV
4. Submit

**Expected Results:**
- ✅ Student created successfully
- ✅ Mandatory services for Form1 auto-assigned
- ✅ Optional services (3,5,7) also assigned
- ✅ Student fees show both mandatory and optional services

**Verification:**
1. View Yasmin's student profile
2. Go to **Fees** tab
3. Verify **mandatory services** for Form1 level are present
4. Verify **optional services** (3,5,7) are also assigned
5. Check service subscription dates

---

### Test Scenario 5: Validation Testing

**Purpose:** Test error handling and validation

#### Test 5A: Missing Required Fields
```csv
first_name,last_name,parent_mobile,shift,gender,category,mobile,email,username,date_of_birth,admission_date,parent_name,parent_relation,fee_services
Ahmed,,33303030
```

**Expected:** Error message indicating "last_name is required (row 2)"

#### Test 5B: Duplicate Email
1. Import a student with email "test@example.com"
2. Try importing another student with same email

**Expected:** Error message "Email already exists in system (row X)"

#### Test 5C: Duplicate Username
1. Import student with username "test_user"
2. Try importing another with same username

**Expected:** Error message "Username already exists in system (row X)"

#### Test 5D: Invalid IDs
```csv
first_name,last_name,parent_mobile,shift,gender,category,mobile,email,username,date_of_birth,admission_date,parent_name,parent_relation,fee_services
Test,Student,33404040,999,999,999
```

**Expected:** Multiple error messages:
- "Invalid shift ID (row 2)"
- "Invalid gender ID (row 2)"
- "Invalid category ID (row 2)"

---

### Test Scenario 6: Large Batch Import

**Purpose:** Test performance with larger imports

**Create test file with 20-50 students:**
- Mix of new and existing parents (some siblings)
- Various combinations of optional fields
- Different shifts, genders, categories

**Expected Results:**
- ✅ All students imported successfully
- ✅ Parents correctly deduplicated
- ✅ Transaction integrity maintained
- ✅ Process completes without timeout

---

## Verification Checklist

After each import, verify:

### Student Data
- [ ] Student name is correct
- [ ] Grade field matches selected grade
- [ ] Class and section match selection
- [ ] Optional fields populated when provided
- [ ] Status is Active

### Parent Data
- [ ] No duplicate parents for same mobile
- [ ] Parent details correct
- [ ] All children linked to correct parent
- [ ] Siblings share same parent

### Fee Services
- [ ] Mandatory services assigned based on grade
- [ ] Optional services assigned when specified
- [ ] Service amounts calculated correctly
- [ ] Academic year set to current session

### User Accounts
- [ ] Student user account created
- [ ] Parent user account created (if new)
- [ ] Default password is 123456
- [ ] Login works for both student and parent
- [ ] Permissions assigned correctly

---

## Common Issues & Solutions

### Issue 1: "Template file not found"
**Solution:** Ensure `student_bulk_import_template_v2.csv` exists in `/public/` folder

### Issue 2: "Invalid grade selected"
**Solution:** Make sure you selected a grade from the dropdown before uploading

### Issue 3: "Section not found"
**Solution:** Ensure class and section are properly selected and exist in database

### Issue 4: Import appears to succeed but no students created
**Solution:**
- Check Laravel logs: `storage/logs/laravel.log`
- Look for validation errors or database issues
- Verify file encoding is UTF-8

### Issue 5: Parent duplicates still created
**Solution:**
- Ensure parent_mobile values are exactly the same (no spaces)
- Check database for existing parent with that mobile
- Review logs for parent creation process

---

## Testing Database Queries

### Check Students Imported
```sql
SELECT * FROM students
WHERE grade = 'Grade1'
ORDER BY created_at DESC
LIMIT 10;
```

### Check Parents by Mobile
```sql
SELECT * FROM parent_guardians
WHERE guardian_mobile = '33888888';
```

### Check Student Services
```sql
SELECT ss.*, ft.name as service_name
FROM student_services ss
JOIN fees_types ft ON ss.fee_type_id = ft.id
WHERE ss.student_id = [STUDENT_ID];
```

### Check Session Class Assignments
```sql
SELECT scs.*, s.first_name, s.last_name, c.name as class_name, sec.name as section_name
FROM session_class_students scs
JOIN students s ON scs.student_id = s.id
JOIN classes c ON scs.classes_id = c.id
JOIN sections sec ON scs.section_id = sec.id
WHERE scs.session_id = [CURRENT_SESSION_ID]
ORDER BY scs.created_at DESC
LIMIT 10;
```

---

## Log Monitoring

**Watch logs during import:**
```bash
tail -f storage/logs/laravel.log
```

**Look for:**
- "Student imported successfully" - confirms import worked
- "Using existing parent" - confirms parent lookup worked
- "Created new parent" - confirms new parent creation
- "Auto-subscribed to mandatory services" - confirms service assignment
- Error messages with row numbers

---

## Performance Benchmarks

**Expected performance:**
- 10 students: < 5 seconds
- 50 students: < 20 seconds
- 100 students: < 45 seconds

**If slower:**
- Check database indexes
- Review query performance
- Consider batch processing for very large imports

---

## Rollback Testing

If import creates issues:

1. Note the `created_at` timestamp
2. Use database queries to identify imported records
3. Delete imported students (cascade will handle related records)
4. Or restore from backup if major issues

---

## Success Criteria

The implementation is successful if:

✅ Users can download the new V2 template
✅ Template has correct headers (first_name, last_name, etc.)
✅ Somali instruction text displays correctly
✅ Import form shows Grade dropdown first
✅ All validation works with meaningful errors
✅ Parents are not duplicated for same mobile
✅ Siblings correctly linked to same parent
✅ Mandatory services auto-assigned by grade
✅ Optional services assigned when specified
✅ All students assigned correct grade/class/section
✅ Logs show detailed import process
✅ Performance is acceptable for batch sizes

---

**Happy Testing! 🚀**

For any issues, refer to `BULK_IMPORT_GUIDE.md` for detailed field descriptions.
