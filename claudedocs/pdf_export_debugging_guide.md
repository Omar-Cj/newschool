# PDF Export Debugging Guide

**Quick Reference for Troubleshooting PDF Export Issues**

---

## Common Issues & Solutions

### Issue 1: Student Name Not Showing in PDF Title

**Symptoms:**
- PDF title shows "Student Gradebook" instead of "[Name] Gradebook"
- Generic title appears even with valid student ID

**Debugging Steps:**

1. **Check parameter is passed**
   ```bash
   # Check logs for export initiation
   grep "Report export initiated" storage/logs/laravel.log | tail -1
   ```
   Look for: `p_student_id` in logged data

2. **Verify student exists in database**
   ```bash
   php artisan tinker
   >>> Student::find(123) // Replace 123 with actual ID
   >>> DB::table('students')->where('id', 123)->first()
   ```

3. **Check name resolution logs**
   ```bash
   grep "Student name resolved\|Student not found" storage/logs/laravel.log
   ```

4. **Verify student has name fields**
   ```bash
   php artisan tinker
   >>> $student = Student::find(123)
   >>> $student->first_name
   >>> $student->last_name
   ```

**Common Causes:**
- Student ID parameter not passed from controller
- Student record doesn't exist
- Both first_name and last_name are null/empty
- Database connection issue

**Fix:**
- Ensure controller passes `p_student_id` in `$metadata['parameters']`
- Check student record exists and has name data
- Review ExportService line 167-170 for parameter extraction

---

### Issue 2: Summary Table Not Displaying

**Symptoms:**
- Main data table shows correctly
- No "Exam Summary" section appears
- No errors in logs

**Debugging Steps:**

1. **Check summary data is passed**
   ```bash
   # Add temporary logging in controller before export
   Log::debug('Summary data check', [
       'has_summary' => isset($metadata['summary']),
       'summary_data' => $metadata['summary'] ?? null
   ]);
   ```

2. **Verify summary structure**
   ```bash
   php artisan tinker
   >>> $metadata = ['summary' => ['rows' => [...]]];
   >>> isset($metadata['summary'])
   >>> isset($metadata['summary']['rows'])
   >>> is_array($metadata['summary']['rows'])
   ```

3. **Check template condition**
   - Template line 338: `@if(isset($summaryData) && !empty($summaryData) && isset($summaryData['rows']))`
   - All three conditions must be true

**Common Causes:**
- Summary data not calculated in report query
- Key name mismatch (e.g., 'summaries' vs 'summary')
- Empty rows array
- Missing 'rows' key in array structure

**Fix:**
- Ensure controller calculates summary before export
- Verify structure matches: `['summary' => ['rows' => [...]]]`
- Check ReportService computes exam summaries

---

### Issue 3: Summary Shows 4 Columns Instead of 2

**Symptoms:**
- Summary table shows too many columns
- Layout doesn't match design
- Extra columns from main data bleed into summary

**Debugging Steps:**

1. **Verify correct template is used**
   ```bash
   ls -la resources/views/reports/pdf/template.blade.php
   ```

2. **Check template version**
   ```bash
   # Template lines 342-346 should have only 2 columns
   grep -A 10 "Exam Summary" resources/views/reports/pdf/template.blade.php
   ```

3. **Clear cached views**
   ```bash
   php artisan view:clear
   ```

**Common Causes:**
- Using old template version
- Cached view not updated
- Template file not deployed to server

**Fix:**
- Verify template lines 342-346 define only 2 columns
- Clear view cache after template changes
- Check git deployment included template file

---

### Issue 4: "Total All Exams" Row Not Bold

**Symptoms:**
- Total row displays but not bold
- No special styling on last summary row
- Border same as other rows

**Debugging Steps:**

1. **Verify row naming**
   ```bash
   # Check summary data structure
   php artisan tinker
   >>> $summaryRows = [...]; // Your summary rows
   >>> end($summaryRows)['exam_name'] === 'Total All Exams'
   ```

2. **Check CSS class application**
   - Template line 352: `$rowClass = $isTotal ? 'total-all-exams-row' : 'exam-row';`
   - Template line 354: `<tr class="{{ $rowClass }}">`

3. **Verify CSS exists**
   ```bash
   # Check lines 235-243 for .total-all-exams-row styling
   grep "total-all-exams-row" resources/views/reports/pdf/template.blade.php
   ```

**Common Causes:**
- Last row exam_name not exactly "Total All Exams"
- Case sensitivity issue (e.g., "total all exams")
- CSS class not applied correctly
- DomPDF CSS rendering issue

**Fix:**
- Ensure exact match: `$row['exam_name'] = 'Total All Exams';`
- Check ReportService summary calculation logic
- Verify CSS in template lines 235-243

---

### Issue 5: PDF Export Times Out

**Symptoms:**
- Export fails with 504 Gateway Timeout
- Large datasets fail to export
- Memory exhausted errors

**Debugging Steps:**

1. **Check row count**
   ```bash
   grep "Report export initiated" storage/logs/laravel.log | grep "row_count"
   ```

2. **Verify row limit enforcement**
   - ExportService line 135-138: Blocks >2000 rows
   - Check if limit is bypassed

3. **Monitor memory usage**
   ```bash
   # Add to ExportService before PDF generation
   Log::debug('Memory usage', [
       'current' => memory_get_usage(true) / 1024 / 1024 . 'MB',
       'peak' => memory_get_peak_usage(true) / 1024 / 1024 . 'MB'
   ]);
   ```

**Common Causes:**
- Dataset exceeds 2000 rows
- Memory limit too low (ExportService line 89: 256M)
- Complex PDF layout causes slow rendering
- Server timeout configured too short

**Fix:**
- Use async export for large datasets (line 60-62)
- Increase PHP memory_limit if needed
- Consider paginated PDF or Excel export instead

---

## Logging Strategy

### Enable Debug Logging

**config/logging.php:**
```php
'channels' => [
    'pdf_export' => [
        'driver' => 'daily',
        'path' => storage_path('logs/pdf_export.log'),
        'level' => 'debug',
        'days' => 14,
    ],
],
```

**Usage in code:**
```php
Log::channel('pdf_export')->debug('Custom debug message', $context);
```

---

### Key Log Points to Add (Optional)

**1. After student name resolution (ExportService.php line 170):**
```php
if ($studentName) {
    Log::debug('Student name resolved for PDF export', [
        'report_id' => $reportId,
        'student_id' => $metadata['parameters']['p_student_id'],
        'student_name' => $studentName,
    ]);
} elseif (isset($metadata['parameters']['p_student_id'])) {
    Log::warning('Student name resolution returned null', [
        'report_id' => $reportId,
        'student_id' => $metadata['parameters']['p_student_id'],
    ]);
}
```

**2. Before PDF view rendering (ExportService.php line 173):**
```php
Log::debug('PDF view data prepared', [
    'report_id' => $reportId,
    'has_student_name' => !is_null($studentName),
    'student_name' => $studentName,
    'has_summary' => !is_null($summaryData),
    'summary_row_count' => isset($summaryData['rows']) ? count($summaryData['rows']) : 0,
    'results_count' => count($formattedResults),
]);
```

**3. After PDF generation (ExportService.php line 194):**
```php
Log::info('PDF export completed successfully', [
    'report_id' => $reportId,
    'filename' => $filename,
    'filesize' => strlen($pdf->output()) / 1024 . 'KB',
]);
```

---

## Database Queries for Debugging

### Check Student Data
```sql
-- Verify student exists
SELECT id, first_name, last_name, email, admission_no
FROM students
WHERE id = 123;

-- Find students with missing names
SELECT id, admission_no, email
FROM students
WHERE (first_name IS NULL OR first_name = '')
  AND (last_name IS NULL OR last_name = '');

-- Count students by name status
SELECT
    COUNT(*) as total,
    SUM(CASE WHEN first_name IS NOT NULL AND first_name != '' THEN 1 ELSE 0 END) as has_first_name,
    SUM(CASE WHEN last_name IS NOT NULL AND last_name != '' THEN 1 ELSE 0 END) as has_last_name
FROM students;
```

### Check Report Data
```sql
-- Find reports with student parameter
SELECT id, name, query_string
FROM dynamic_reports
WHERE query_string LIKE '%p_student_id%';

-- Check report executions
SELECT *
FROM report_executions
WHERE report_id = 1
ORDER BY created_at DESC
LIMIT 10;
```

---

## Performance Monitoring

### Execution Time Tracking

**Add to ExportService.exportPdf() start (line 133):**
```php
$startTime = microtime(true);
```

**Add before return (line 194):**
```php
$executionTime = microtime(true) - $startTime;
Log::info('PDF export performance', [
    'report_id' => $reportId,
    'execution_time' => round($executionTime, 2) . 's',
    'row_count' => count($results),
    'has_summary' => !is_null($summaryData),
]);
```

### Memory Usage Tracking

**Add before PDF generation (line 173):**
```php
Log::debug('PDF generation memory usage', [
    'current_mb' => round(memory_get_usage(true) / 1024 / 1024, 2),
    'peak_mb' => round(memory_get_peak_usage(true) / 1024 / 1024, 2),
    'limit' => ini_get('memory_limit'),
]);
```

---

## Testing Data Generators

### Generate Test Student with Complete Data
```php
// Run in tinker
$student = Student::create([
    'admission_no' => 'TEST' . rand(1000, 9999),
    'first_name' => 'Test',
    'last_name' => 'Student',
    'email' => 'test' . rand(1000, 9999) . '@example.com',
    'dob' => now()->subYears(15),
    'admission_date' => now(),
    'status' => 1,
]);

echo "Created student ID: " . $student->id;
```

### Generate Test Summary Data
```php
// Add to controller before export call
$metadata['summary'] = [
    'rows' => [
        ['exam_name' => 'Midterm Exam', 'total_marks' => 95.5],
        ['exam_name' => 'Final Exam', 'total_marks' => 88.0],
        ['exam_name' => 'Project Work', 'total_marks' => 92.5],
        ['exam_name' => 'Total All Exams', 'total_marks' => 276.0],
    ]
];
```

---

## Template Inspection Commands

### Verify Template Structure
```bash
# Check student name usage
grep -n "studentName" resources/views/reports/pdf/template.blade.php

# Check summary section
grep -n "summaryData" resources/views/reports/pdf/template.blade.php

# Verify CSS classes
grep -n "total-all-exams-row\|exam-row" resources/views/reports/pdf/template.blade.php

# Check column count in summary table
grep -A 5 "<thead>" resources/views/reports/pdf/template.blade.php | grep -c "<th>"
```

### Compare Template Versions
```bash
# Show recent changes
git log --oneline -10 resources/views/reports/pdf/template.blade.php

# Show specific commit changes
git show <commit-hash>:resources/views/reports/pdf/template.blade.php

# Diff against production
diff resources/views/reports/pdf/template.blade.php /path/to/production/template.blade.php
```

---

## Checklist: Before Reporting Bug

- [ ] Verified student ID is passed in request
- [ ] Checked student exists in database with name fields
- [ ] Confirmed summary data is calculated and passed
- [ ] Verified template file is latest version
- [ ] Cleared view cache (`php artisan view:clear`)
- [ ] Checked logs for warnings/errors
- [ ] Tested with different student IDs
- [ ] Reviewed recent code changes
- [ ] Verified database connection works
- [ ] Tested in staging environment

---

## Emergency Rollback Procedure

If PDF exports are completely broken:

1. **Identify last working version**
   ```bash
   git log --oneline -20 app/Services/ExportService.php
   git log --oneline -20 resources/views/reports/pdf/template.blade.php
   ```

2. **Revert changes**
   ```bash
   git checkout <last-good-commit> app/Services/ExportService.php
   git checkout <last-good-commit> resources/views/reports/pdf/template.blade.php
   ```

3. **Clear caches**
   ```bash
   php artisan view:clear
   php artisan config:clear
   php artisan cache:clear
   ```

4. **Test immediately**
   - Export sample PDF
   - Verify student name appears
   - Check summary displays

5. **Document issue**
   - Capture error logs
   - Note what changed
   - Create rollback ticket

---

## Support Resources

### Documentation
- Full verification report: `claudedocs/pdf_export_verification_report.md`
- Testing checklist: `claudedocs/pdf_export_testing_checklist.md`
- Summary: `claudedocs/pdf_export_verification_summary.md`

### Code References
- Export service: `app/Services/ExportService.php`
- PDF template: `resources/views/reports/pdf/template.blade.php`
- Students migration: `database/migrations/tenant/2023_02_24_124400_create_students_table.php`

### External Dependencies
- DomPDF: `barryvdh/laravel-dompdf`
- Laravel Excel: `maatwebsite/excel`
- Documentation: https://github.com/barryvdh/laravel-dompdf

---

**Last Updated:** 2025-10-13
**Maintained By:** Quality Engineering Team
**Version:** 1.0
