# Receipt Class/Section Fix Command - Quick Guide

**Command**: `php artisan receipts:fix-class-section`

---

## 📋 Quick Reference

```bash
# Preview changes (recommended first step)
php artisan receipts:fix-class-section --dry-run

# Test on small dataset
php artisan receipts:fix-class-section --dry-run --limit=10

# Fix specific session only
php artisan receipts:fix-class-section --session=5

# Apply all changes (after testing)
php artisan receipts:fix-class-section
```

---

## 🎯 What This Command Does

Fixes incorrect class/section data in existing receipts by:
1. Finding all receipts generated from payment transactions
2. Looking up the historical enrollment record for each receipt's session
3. Updating class/section to match what student was enrolled in at time of payment

**Example Fix**:
- Receipt from 2023-2024 session
- Currently shows: Grade 1 (wrong - oldest enrollment)
- Should show: Grade 2 (correct - enrollment in 2023-2024)
- Command updates to: Grade 2 ✅

---

## 🚦 Recommended Workflow

### Step 1: Test on Small Sample
```bash
php artisan receipts:fix-class-section --dry-run --limit=10
```
**Purpose**: See what changes would be made on just 10 receipts
**Review**: Check the output to verify logic is correct

### Step 2: Preview All Changes
```bash
php artisan receipts:fix-class-section --dry-run
```
**Purpose**: See full scope of changes without applying
**Review**: Check statistics - how many updated, skipped, errors

### Step 3: Apply Changes
```bash
php artisan receipts:fix-class-section
```
**Purpose**: Actually update the database
**Result**: All receipts will have correct historical class/section

### Step 4: Verify
- Check a few receipts in the UI
- Compare before/after for students who changed classes
- Review logs: `storage/logs/laravel.log`

---

## 📊 Understanding the Output

```
===========================================
  Results Summary
===========================================

Total Receipts Processed       250    ← Total receipts examined
✅ Updated                     180    ← Changed to correct values
⏭️  Skipped (Already Correct)  50    ← No change needed
⚠️  No Enrollment Record       15    ← Missing enrollment data
⚠️  No Session ID              5     ← Missing session context
❌ Errors                      0     ← Processing errors
```

### What Each Metric Means

- **Updated**: Class/section was wrong, now fixed
- **Skipped**: Class/section already correct, no change
- **No Enrollment Record**: Student has no enrollment record for that session (data gap)
- **No Session ID**: Receipt has no session context (shouldn't happen for enhanced receipts)
- **Errors**: Unexpected errors (check logs)

---

## 🛡️ Safety Features

### 1. Dry Run Mode
```bash
--dry-run
```
- Shows what WOULD change
- Does NOT save to database
- Safe to run anytime

### 2. Limit Option
```bash
--limit=N
```
- Process only first N receipts
- Good for testing
- Example: `--limit=50`

### 3. Session Filter
```bash
--session=ID
```
- Only process receipts for specific session
- Useful for targeted fixes
- Example: `--session=5`

### 4. Confirmation Prompt
Command asks "Do you want to continue?" before processing

### 5. Progress Bar
Shows real-time progress: `[====] 100% (250/250)`

### 6. Detailed Logging
All changes logged to `storage/logs/laravel.log` with:
- Receipt ID and number
- Old vs new class/section
- Student ID and session ID

---

## 🔍 Example Scenarios

### Scenario 1: First Time Running
```bash
# Step 1: Test on 10 receipts
php artisan receipts:fix-class-section --dry-run --limit=10

# Output shows: "✅ Updated: 8, ⏭️ Skipped: 2"
# Looks good, proceed to full test

# Step 2: Preview all
php artisan receipts:fix-class-section --dry-run

# Output shows: "✅ Updated: 180, ⏭️ Skipped: 70"
# Ready to apply

# Step 3: Apply changes
php artisan receipts:fix-class-section

# ✅ All changes saved!
```

### Scenario 2: Fixing Specific Session
```bash
# Only fix receipts from session 3 (2024-2025)
php artisan receipts:fix-class-section --session=3

# Output:
# "Filtering by session: 3"
# "Found 45 receipts to process"
# "✅ Updated: 30"
```

### Scenario 3: Re-running After Initial Fix
```bash
php artisan receipts:fix-class-section --dry-run

# Output:
# "✅ Updated: 0, ⏭️ Skipped: 250"
# Already fixed, no changes needed
```

---

## ⚠️ Troubleshooting

### "No Enrollment Record" for Many Receipts

**Problem**: Students don't have enrollment records for the receipt's session

**Possible Causes**:
1. Enrollment data not migrated properly
2. Receipts created before student was formally enrolled
3. Data integrity issue

**Solution**:
1. Check `session_class_students` table for missing data
2. Verify student was actually enrolled in that session
3. May need to manually fix enrollment data first

### "No Session ID" for Receipts

**Problem**: Receipts don't have session_id populated

**Possible Causes**:
1. Legacy receipts created before session_id field added
2. Receipt generation bug

**Solution**:
1. Check if receipts have `source_id` pointing to payment_transaction
2. Command will try to get session from payment_transaction → fees_collect
3. If still missing, may need manual data fix

### Command Runs Slow

**Issue**: Processing thousands of receipts takes time

**Optimization**:
1. Command uses chunk processing (100 at a time)
2. Processes in memory efficiently
3. For very large datasets (10,000+), run during off-hours

---

## 📝 Logs and Debugging

### Check Logs
```bash
tail -f storage/logs/laravel.log
```

### Log Entry Example
```json
{
  "level": "info",
  "message": "Receipt class/section fix",
  "context": {
    "receipt_id": 123,
    "receipt_number": "RCPT-2024-123",
    "student_id": 45,
    "session_id": 3,
    "old_class": "Grade 1",
    "new_class": "Grade 3",
    "old_section": "Section A",
    "new_section": "Section B",
    "dry_run": false
  }
}
```

---

## ✅ Best Practices

1. **Always dry-run first** - Never run without `--dry-run` on production the first time
2. **Test on limited set** - Use `--limit=10` to verify logic
3. **Review logs** - Check `storage/logs/laravel.log` after running
4. **Backup database** - Before applying changes to production
5. **Run during off-hours** - For large datasets to avoid performance impact
6. **Verify in UI** - Spot-check receipts after running to ensure correctness

---

## 📞 Support

**If you encounter issues**:
1. Run with `--dry-run` to see what's happening
2. Check logs: `storage/logs/laravel.log`
3. Look for patterns in "No Enrollment Record" or errors
4. Verify data integrity in source tables

**Common Questions**:

**Q: Can I run this multiple times?**
A: Yes! Command is idempotent - if receipts are already correct, they're skipped.

**Q: Will this affect new receipts?**
A: No, this only updates existing receipts. New receipts use the fixed code.

**Q: Can I undo changes?**
A: Not automatically. Check logs to see what changed, or restore from database backup.

**Q: How long does it take?**
A: ~0.02 seconds per receipt. 1000 receipts = ~20 seconds.

---

## 🎯 Success Criteria

After running the command, you should see:
- ✅ Most receipts show "Updated" or "Skipped"
- ✅ Few or no "No Enrollment Record" errors
- ✅ Zero "Errors"
- ✅ Spot-checking receipts in UI shows correct class/section
- ✅ Receipts match student's enrollment for that specific session

---

**Command Created**: 2025-10-20
**Purpose**: Fix historical class/section data in receipts
**Safe to Run**: Yes (especially with --dry-run)
