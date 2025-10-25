# Receipt Print & UI Retention Improvements - Testing Guide

## 📋 Overview

This guide provides step-by-step testing procedures for the receipt print data accuracy and UI state retention improvements.

## ✅ Changes Implemented

### **Phase 1: Print Data Accuracy** ✅ COMPLETE
1. **ReceiptController** - Now queries `receipts` table directly (no recalculation)
2. **New Print Template** - Uses EXACT receipt data from database
3. **ReceiptRepository** - Display amounts consistently between list and print

### **Phase 2: UI State Retention** ✅ COMPLETE
1. **DataTables State Saving** - Preserves pagination, sort, and search
2. **Custom Filter Persistence** - Saves student search, dates, payment method, collector, family payments checkbox
3. **SessionStorage Backup** - 1-hour duration with timestamp validation
4. **URL Parameter Support** - Shareable filtered views

## 🧪 Testing Procedures

### Test 1: Print Data Accuracy

**Objective**: Verify printed receipt shows EXACT data from receipts table row

**Steps**:
1. Navigate to `Fees > Receipts`
2. Note the following for any receipt row:
   - Receipt Number (e.g., RCP-2025-00001)
   - Student Name
   - Net Amount (displayed amount)
   - Discount (if shown)
3. Click **Print** or **Download** action for that receipt
4. Compare printed receipt with table row:
   - ✅ Receipt number matches exactly
   - ✅ Student name matches exactly
   - ✅ Net amount matches displayed amount
   - ✅ Discount amount matches (if applicable)
   - ✅ Fee breakdown matches `receipt_data` JSON from database

**Expected Results**:
- ✅ All values match EXACTLY between row and print
- ✅ No recalculation or aggregation
- ✅ Discount shown consistently if present
- ✅ Family payment badge shows in both list and print

**Verification Query** (optional database check):
```sql
SELECT
    id,
    receipt_number,
    student_name,
    total_amount,
    discount_amount,
    (total_amount - discount_amount) as net_amount,
    receipt_data
FROM receipts
WHERE id = [receipt_id];
```

---

### Test 2: UI State Retention - Basic Filters

**Objective**: Verify filters persist when navigating away and back

**Steps**:
1. Go to `Fees > Receipts`
2. Apply filters:
   - Student Search: Enter "John"
   - From Date: 2025-01-01
   - To Date: 2025-01-31
   - Payment Method: Cash
3. Wait for table to reload with filtered results
4. Navigate to `Dashboard` or any other menu
5. Navigate back to `Fees > Receipts`

**Expected Results**:
- ✅ All filter inputs retain their values
- ✅ Table automatically loads filtered data
- ✅ Student search field shows "John"
- ✅ Date fields show 2025-01-01 to 2025-01-31
- ✅ Payment Method dropdown shows "Cash"

---

### Test 3: UI State Retention - Pagination & Sort

**Objective**: Verify pagination and sort state persist

**Steps**:
1. Go to `Fees > Receipts`
2. Change page size to 25 entries (from default 10)
3. Go to page 3 of results
4. Click "Student Name" column to sort ascending
5. Navigate to `Fees > Collect Fees` (or any other page)
6. Navigate back to `Fees > Receipts`

**Expected Results**:
- ✅ Page size remains at 25 entries
- ✅ Still on page 3
- ✅ Student Name column still sorted ascending
- ✅ Sort arrow indicator visible on Student Name column

---

### Test 4: UI State Retention - Family Payments Filter

**Objective**: Verify family payments checkbox state persists

**Steps**:
1. Go to `Fees > Receipts`
2. Check "Family Payments Only" checkbox
3. Wait for table to reload showing only family payment receipts
4. Navigate away and back to `Fees > Receipts`

**Expected Results**:
- ✅ "Family Payments Only" checkbox remains checked
- ✅ Table shows only family payment receipts
- ✅ Family payment badges visible on filtered receipts

---

### Test 5: UI State Retention - Clear Filters

**Objective**: Verify clear filters resets all state

**Steps**:
1. Go to `Fees > Receipts`
2. Apply multiple filters (student search, dates, payment method)
3. Change pagination to page 2
4. Click "Clear" button
5. Verify state reset

**Expected Results**:
- ✅ All filter inputs cleared
- ✅ Table shows all receipts (no filtering)
- ✅ Pagination resets to page 1
- ✅ Page size resets to default (10)
- ✅ Sort resets to default (payment_date descending)

---

### Test 6: UI State Retention - URL Sharing

**Objective**: Verify filtered view can be shared via URL

**Steps**:
1. Go to `Fees > Receipts`
2. Apply filters:
   - Student Search: "Jane"
   - From Date: 2025-02-01
   - Payment Method: Stripe
3. Copy the URL from browser address bar
4. Open a new browser tab/window
5. Paste and navigate to the copied URL

**Expected Results**:
- ✅ Filters automatically applied from URL parameters
- ✅ Table loads with filtered data
- ✅ All filter inputs populated correctly
- ✅ URL parameters visible: `?student_search=Jane&from_date=2025-02-01&payment_method=2`

---

### Test 7: SessionStorage Expiry

**Objective**: Verify old state is cleared after 1 hour

**Steps**:
1. Apply filters and navigate away
2. Wait 1 hour (or manually edit sessionStorage timestamp in browser dev tools)
3. Navigate back to `Fees > Receipts`

**Expected Results**:
- ✅ Old filters cleared (not restored)
- ✅ Table shows default state (no filters)
- ✅ Clean sessionStorage (old state removed)

**Developer Testing** (shortcut):
```javascript
// In browser console, edit timestamp to simulate expiry
let state = JSON.parse(sessionStorage.getItem('receiptFilters'));
state.timestamp = Date.now() - (2 * 60 * 60 * 1000); // 2 hours ago
sessionStorage.setItem('receiptFilters', JSON.stringify(state));
// Refresh page - filters should NOT restore
```

---

### Test 8: Print Template Consistency

**Objective**: Verify print template matches system theme and styling

**Steps**:
1. Generate a receipt print preview (`?print=1`)
2. Verify styling:
   - School logo displayed correctly
   - Receipt header formatted properly
   - Student info table aligned
   - Fee breakdown table styled consistently
   - Totals summary clearly visible
   - Signature section properly positioned
3. Test actual PDF download
4. Open PDF and verify:
   - All data visible
   - No text cutoff
   - Professional appearance

**Expected Results**:
- ✅ Print preview looks professional
- ✅ All sections properly aligned
- ✅ PDF downloads successfully
- ✅ PDF matches print preview
- ✅ No styling issues or text overflow

---

### Test 9: Discount Display Consistency

**Objective**: Verify discount amounts shown consistently

**Steps**:
1. Find a receipt with discount > 0
2. Note the discount in receipt list view
3. Print/download that receipt
4. Compare discount display

**Expected Results**:
- ✅ Discount shown in list view as separate line
- ✅ Discount shown in print as separate row in totals
- ✅ Net amount (total - discount) calculated correctly
- ✅ Both list and print show same discount value

---

### Test 10: Legacy Receipt Fallback

**Objective**: Verify fallback for non-persisted receipts

**Steps**:
1. Try to print a legacy receipt (if any exist without `receipts` table entry)
2. System should fall back to old template
3. Print should still work without errors

**Expected Results**:
- ✅ Legacy receipt prints using old template
- ✅ No errors or exceptions
- ✅ Smooth fallback behavior
- ✅ User sees receipt successfully

---

## 🐛 Common Issues & Solutions

### Issue 1: Filters Not Restoring
**Symptom**: Navigating back shows empty filters
**Solution**:
- Check browser console for JavaScript errors
- Verify sessionStorage is enabled (not disabled by browser)
- Check DataTables state is being saved (localStorage)

### Issue 2: Print Shows Wrong Amount
**Symptom**: Printed amount different from list row
**Solution**:
- Verify receipt exists in `receipts` table
- Check `total_amount` and `discount_amount` columns
- Ensure `receipt_data` JSON is properly formatted

### Issue 3: Nice-Select Dropdowns Not Updating
**Symptom**: Dropdown shows value but display doesn't update
**Solution**:
- Check if `setTimeout()` in `stateLoadParams` is sufficient (100ms)
- Verify nice-select plugin is properly initialized
- Trigger 'change' event manually: `$('#dropdown').trigger('change')`

### Issue 4: URL Parameters Not Loading
**Symptom**: URL has parameters but filters not applied
**Solution**:
- Verify `restoreFilterState()` is called BEFORE DataTables init
- Check URL parameter names match filter IDs
- Ensure page fully loaded before restoration

---

## 📊 Performance Verification

### DataTables Performance
- **Initial Load**: Should be fast (server-side processing)
- **Filter Application**: < 1 second response time
- **State Restoration**: Instant (no API call)

### Database Queries
```sql
-- Verify receipt query is efficient (should use index)
EXPLAIN SELECT * FROM receipts WHERE id = ?;

-- Check if payment_session_id index is used
EXPLAIN SELECT * FROM receipts WHERE payment_session_id = ?;
```

---

## 🔍 Developer Testing Tools

### Browser Console Tests

```javascript
// Check if state is being saved
console.log(sessionStorage.getItem('receiptFilters'));

// Check DataTables state
console.log(localStorage.getItem('DataTables_receiptsTable_/'));

// Test URL parameter parsing
const urlParams = new URLSearchParams(window.location.search);
console.log('Student Search:', urlParams.get('student_search'));
console.log('From Date:', urlParams.get('from_date'));
```

### Database Verification

```sql
-- Check receipt data structure
SELECT
    id,
    receipt_number,
    student_name,
    total_amount,
    discount_amount,
    payment_status,
    receipt_data
FROM receipts
ORDER BY payment_date DESC
LIMIT 5;

-- Verify family payment grouping
SELECT
    payment_session_id,
    COUNT(*) as receipt_count,
    SUM(total_amount) as total_family_amount
FROM receipts
WHERE payment_session_id IS NOT NULL
GROUP BY payment_session_id;
```

---

## ✅ Final Acceptance Checklist

### Print Data Accuracy
- [ ] Receipt row amount matches printed receipt
- [ ] Student name exact match
- [ ] Receipt number exact match
- [ ] Discount shown consistently
- [ ] Fee breakdown matches database `receipt_data`
- [ ] Family payment badge displayed correctly

### UI State Retention
- [ ] Student search filter persists
- [ ] Date range filters persist
- [ ] Payment method dropdown persists
- [ ] Collector dropdown persists
- [ ] Family payments checkbox persists
- [ ] Pagination page persists
- [ ] Page size persists
- [ ] Sort column and direction persist

### Additional Features
- [ ] Clear filters resets everything
- [ ] URL parameters work for sharing
- [ ] SessionStorage expires after 1 hour
- [ ] State saved on filter changes
- [ ] State saved on navigation away
- [ ] Print template looks professional
- [ ] PDF download works correctly

### Browser Compatibility
- [ ] Chrome/Edge - All features work
- [ ] Firefox - All features work
- [ ] Safari - All features work
- [ ] Mobile browsers - Responsive design

---

## 🎯 Success Criteria

**Phase 1 Success**: Print data accuracy
- ✅ 100% match between receipt row and printed receipt
- ✅ No recalculation or aggregation
- ✅ Exact database values displayed

**Phase 2 Success**: UI state retention
- ✅ Filters persist across navigation
- ✅ Pagination and sort persist
- ✅ SessionStorage backup works
- ✅ URL sharing works correctly
- ✅ State expires properly (1 hour)

---

## 📞 Support & Troubleshooting

### Browser DevTools Commands

```javascript
// Clear all state (for fresh testing)
sessionStorage.clear();
localStorage.clear();
location.reload();

// Manually set filters (for testing)
sessionStorage.setItem('receiptFilters', JSON.stringify({
    studentSearch: 'Test',
    fromDate: '2025-01-01',
    toDate: '2025-01-31',
    paymentMethod: '1',
    collectorId: '',
    familyPayments: false,
    timestamp: Date.now()
}));
location.reload();
```

### PHP Artisan Commands

```bash
# Clear Laravel caches (if views not updating)
php artisan view:clear
php artisan cache:clear
php artisan route:clear

# Check receipt data in database
php artisan tinker
>>> \App\Models\Fees\Receipt::latest()->first();
>>> \App\Models\Fees\Receipt::where('payment_session_id', 'FAM_123')->get();
```

---

## 📝 Notes

- **State Duration**: 1 hour for both DataTables and sessionStorage
- **Priority**: URL parameters > sessionStorage > DataTables localStorage
- **Backward Compatibility**: Legacy receipts still work with fallback template
- **Performance**: No impact on load time (state restore is instant)

---

**Document Version**: 1.0
**Last Updated**: 2025-10-20
**Status**: Ready for Testing
