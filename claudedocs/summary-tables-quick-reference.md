# Report Summary Tables - Quick Reference Guide

## Summary Types Supported

The report system currently supports **four** types of summary tables:

### 1. Exam Gradebook Summary
**Procedure**: `GetStudentGradebook`
**Type**: `exam_gradebook`
**Layout**: Single table, centered, 60% width

**Structure**:
```
┌─────────────────────────────────┐
│   Exam Summary                  │
├────────────────┬────────────────┤
│ Exam Name      │ Total Marks    │
├────────────────┼────────────────┤
│ Midterm        │ 85.00          │
│ Final          │ 92.00          │
│ Total All Exams│ 177.00        │ ← Highlighted
└────────────────┴────────────────┘
```

---

### 2. Paid Students Summary
**Procedure**: `GetPaidStudentsReport`
**Type**: `financial`
**Layout**: Single table, right-aligned, 50% width

**Structure**:
```
┌─────────────────────────────────┐
│   Financial Summary             │
├────────────────┬────────────────┤
│ Paid Amount:   │ $5,328.00      │
│ Deposit Used:  │ $1,000.00      │
│ Discount:      │ $488.67        │
│ Grand Total:   │ $6,816.67     │ ← Highlighted
└────────────────┴────────────────┘
```

---

### 3. Fee Generation Summary
**Procedure**: `GetFeeGenerationReport`
**Type**: `financial`
**Layout**: Single table, right-aligned, 50% width

**Structure**:
```
┌─────────────────────────────────┐
│   Financial Summary             │
├────────────────┬────────────────┤
│ Total Invoices:│ $50,420.00    │ ← Highlighted
└────────────────┴────────────────┘
```

---

### 4. Fee Generation & Collection Summary ⭐ NEW
**Procedure**: `GetFeeGenerationCollectionReport`
**Type**: `fee_generation_collection`
**Layout**: Three-column responsive grid

**Structure**:
```
┌─────────────────────┐  ┌─────────────────────┐  ┌─────────────────────┐
│ Invoice Generation  │  │ Unpaid Generation   │  │ Collection          │
├───────────┬─────────┤  ├───────────┬─────────┤  ├───────────┬─────────┤
│ PREVIOUS  │$2,154.00│  │ UNPAID    │$1,672.33│  │ TOTAL PAID│$5,328.00│
│ CURRENT   │$5,420.00│  │ TOTAL     │         │  │ TOTAL     │ $488.67│
│ TOTAL     │$7,574.00│  │           │         │  │ DISCOUNT  │         │
│ INVOICE   │         │  │           │         │  │ SUB TOTAL │$5,816.67│
│           │         │  │           │         │  │ ADVANCE   │  $86.00│
│           │         │  │           │         │  │ PAYMENT   │         │
│           │         │  │           │         │  │ GRAND     │$5,901.67│
│           │         │  │           │         │  │ TOTAL     │         │
└───────────┴─────────┘  └───────────┴─────────┘  └───────────┴─────────┘
    (Highlighted)              (Highlighted)             (Highlighted)
```

---

## How to Use Summaries in Your Reports

### Step 1: Add Summary Calculation Method

Edit `app/Services/Report/ReportExecutionService.php`:

```php
private function addYourReportSummary(array $data, string $procedureName): array
{
    // Check procedure name
    if ($procedureName !== 'YourProcedureName') {
        return $data;
    }

    // Calculate totals
    $total = 0;
    foreach ($data['rows'] as $row) {
        $total += (float) ($row['amount'] ?? 0);
    }

    // Structure summary
    $data['summary'] = [
        'rows' => [
            ['metric' => 'Total Amount', 'value' => $total]
        ],
        'type' => 'financial' // or 'exam_gradebook' or 'fee_generation_collection'
    ];

    return $data;
}
```

### Step 2: Register in Execution Pipeline

In `executeReport()` method, add:

```php
$transformedResults = $this->addYourReportSummary(
    $transformedResults,
    $report->procedure_name
);
```

### Step 3: Display in Templates

Summary automatically displays in:
- ✅ Web view (`show.blade.php`)
- ✅ PDF export (`pdf-export.blade.php`)
- ✅ Print view (`print-wrapper.blade.php`)

**No additional template changes needed!**

---

## Column Name Mapping

### Fee Generation & Collection Report

The system looks for these column names (in order of priority):

| Metric | Primary | Alternative 1 | Alternative 2 |
|--------|---------|---------------|---------------|
| Previous Invoice | `previous_invoice` | `previous_invoices` | - |
| Current Invoice | `current_invoice` | `current_invoices` | - |
| Unpaid Total | `unpaid_total` | `unpaid_amount` | `unpaid` |
| Total Paid | `total_paid` | `paid_amount` | `amount_paid` |
| Total Discount | `total_discount` | `discount` | `discount_amount` |
| Advance Payment | `advance_payment` | `advance` | `advance_amount` |

**Your stored procedure must return at least one of these column names for each metric.**

---

## Styling Guide

### Bootstrap Classes Used

- `col-md-4` - Three-column grid layout
- `card` - Container for each section
- `card-header` - Section title header
- `table` - Data table
- `table-bordered` - Border around table
- `table-active` - Highlight total rows
- `text-end` - Right-align numbers
- `font-weight-bold` - Bold text for totals

### Custom Classes

- `.summary-container` - Main wrapper
- `.summary-section` - Individual section
- `.summary-table` - Summary table styling

---

## Troubleshooting Guide

### Problem: Summary Not Showing

**Check:**
1. Procedure name matches exactly
2. Report returns data rows
3. Column names match expected names
4. Summary type is set correctly

**Debug:**
```bash
# Check Laravel logs
tail -f storage/logs/laravel.log | grep "summary"

# Verify data structure
Log::debug('Report data', ['data' => $transformedResults]);
```

---

### Problem: Wrong Calculations

**Check:**
1. Column values are numeric
2. Currency formatting removed before calculation
3. Null values handled properly

**Debug:**
```php
// In summary method
Log::debug('Calculation debug', [
    'sample_row' => $data['rows'][0] ?? null,
    'total_calculated' => $total
]);
```

---

### Problem: Layout Broken

**Check:**
1. Bootstrap CSS loaded
2. Responsive classes present
3. Browser compatibility

**Fix:**
```html
<!-- Ensure Bootstrap is loaded -->
<link href="{{ asset('css/bootstrap.min.css') }}" rel="stylesheet">

<!-- Test in different browsers -->
Chrome, Firefox, Safari, Edge
```

---

## Best Practices

### ✅ DO:
- Use null coalescing operator for missing columns: `$row['amount'] ?? 0`
- Cast values to float: `(float) $value`
- Add logging for debugging: `Log::debug('Summary calculated')`
- Follow existing naming conventions
- Include `is_total` flag for total rows
- Format currency consistently: `number_format($value, 2)`

### ❌ DON'T:
- Hardcode column names without alternatives
- Skip error handling
- Modify core report data structure
- Add unnecessary database queries
- Break existing summary types

---

## Quick Copy-Paste Templates

### Financial Summary (Simple)

```php
$data['summary'] = [
    'rows' => [
        ['metric' => 'Total Amount', 'value' => $totalAmount],
        ['metric' => 'Total Discount', 'value' => $totalDiscount],
        ['metric' => 'Grand Total', 'value' => $grandTotal]
    ],
    'type' => 'financial'
];
```

### Multi-Section Summary

```php
$data['summary'] = [
    'sections' => [
        [
            'title' => 'Section 1',
            'rows' => [
                ['label' => 'Metric 1', 'value' => 100.00, 'is_total' => false],
                ['label' => 'Total', 'value' => 100.00, 'is_total' => true]
            ]
        ]
    ],
    'type' => 'fee_generation_collection'
];
```

---

## File Locations

### Backend
- `app/Services/Report/ReportExecutionService.php` - Calculation logic

### Frontend
- `resources/views/reports/partials/summary-tables.blade.php` - Display component
- `resources/views/reports/pdf-export.blade.php` - PDF template
- `resources/views/reports/show.blade.php` - Web view
- `resources/views/reports/print-wrapper.blade.php` - Print template

### Documentation
- `claudedocs/fee-generation-collection-summary-implementation.md` - Full docs
- `claudedocs/summary-tables-quick-reference.md` - This file

---

## Support

**Questions?** Check:
1. `Report-System-Documentation.md` for report system overview
2. `fee-generation-collection-summary-implementation.md` for detailed docs
3. Laravel logs at `storage/logs/laravel.log`
4. Report Center database table for procedure configuration

---

**Quick Reference Version**: 1.0
**Last Updated**: 2025-10-16
