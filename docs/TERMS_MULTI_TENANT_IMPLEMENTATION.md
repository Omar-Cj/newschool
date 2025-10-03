# Terms Module - Multi-Tenant Implementation

**Implementation Date:** October 3, 2025
**Status:** ✅ Complete

## Overview

Successfully implemented multi-tenant (branch) awareness for the Terms module, allowing each branch to manage its own academic terms independently while sharing term definition templates across the institution.

## Architecture Design

### Term Definitions (Templates)
- **Scope:** Institution-wide (shared across all branches)
- **Model:** Extends `Model` (not `BaseModel`) to avoid branch filtering
- **Purpose:** Reusable templates like "First Term", "Second Term", "Third Term"
- **Benefits:** Consistency across institution, reduced configuration duplication

### Terms (Instances)
- **Scope:** Branch-specific
- **Model:** Extends `BaseModel` with automatic branch filtering
- **Purpose:** Actual term instances with specific dates per branch
- **Benefits:** Independent management, branch-specific schedules

## Implementation Details

### 1. Database Changes

#### Migration Created
**File:** `database/migrations/tenant/2025_10_03_000001_add_branch_foreign_key_to_terms.php`

**Changes:**
- Set default `branch_id = 1` for existing terms
- Updated unique constraint: `UNIQUE(term_definition_id, session_id, branch_id)`
- Added foreign key: `terms.branch_id → branches.id` with restrict on delete
- Added index on `branch_id` for query performance

### 2. Model Updates

#### Term Model (`app/Models/Examination/Term.php`)
**Changes:**
- Added `'branch_id'` to `$fillable` array
- Added `branch()` relationship method (checks if MultiBranch module is enabled)
- Continues extending `BaseModel` for automatic branch filtering

**Code:**
```php
public function branch()
{
    if (!hasModule('MultiBranch')) {
        return null;
    }
    return $this->belongsTo(\Modules\MultiBranch\Entities\Branch::class);
}
```

#### TermDefinition Model (`app/Models/Examination/TermDefinition.php`)
**Changes:**
- Changed base class from `BaseModel` to `Model`
- Removed automatic branch filtering (templates are institution-wide)
- Added documentation explaining the design decision

#### Branch Model (`Modules/MultiBranch/Entities/Branch.php`)
**Changes:**
- Added `terms()` relationship method
- Added `activeTerms()` relationship method for convenience

**Code:**
```php
public function terms(): HasMany
{
    return $this->hasMany(\App\Models\Examination\Term::class);
}

public function activeTerms(): HasMany
{
    return $this->hasMany(\App\Models\Examination\Term::class)
        ->where('status', 'active');
}
```

### 3. Repository Updates

#### TermRepository (`app/Repositories/Academic/TermRepository.php`)
**Changes:**
- Added conditional eager loading of `branch` relationship
- Added `branch_name` to DataTables output
- Added branch_id default setting in `openTerm()` method
- Updated comments to clarify automatic branch filtering

**Code:**
```php
// Dynamic eager loading
$with = ['termDefinition', 'session', 'openedBy'];
if (hasModule('MultiBranch')) {
    $with[] = 'branch';
}
$query = Term::with($with);

// Branch name in DataTables output
$branchName = 'N/A';
if (hasModule('MultiBranch') && $row->branch) {
    $branchName = $row->branch->name;
}
```

### 4. Service Layer

#### TermService (`app/Services/Academic/TermService.php`)
**Status:** No changes required

**Reason:** All methods automatically inherit branch filtering from BaseModel global scope:
- `validateTermDates()` - Branch-scoped overlap detection
- `validateTermSequence()` - Branch-scoped term sequence checking
- `getDashboardData()` - Returns branch-specific dashboard data
- All queries are automatically filtered by user's branch

### 5. View Updates

#### Terms Index View (`resources/views/backend/examination/terms/index.blade.php`)
**Changes:**
- Added conditional branch column header
- Added conditional branch column in DataTables configuration
- Branch column only displays when MultiBranch module is enabled

**Code:**
```blade
{{-- Table Header --}}
@if(hasModule('MultiBranch'))
<th class="purchase">{{ ___('common.branch') }}</th>
@endif

{{-- DataTables Configuration --}}
columns: [
    {data: 'DT_RowIndex', name: 'DT_RowIndex', orderable: false, searchable: false, width: '5%'},
    {data: 'term_name', name: 'term_name'},
    {data: 'session_name', name: 'session_name'},
    @if(hasModule('MultiBranch'))
    {data: 'branch_name', name: 'branch_name'},
    @endif
    {data: 'date_range', name: 'date_range'},
    // ... other columns
]
```

## Automatic Features (No Code Changes Needed)

### 1. Branch Isolation
- All Term queries automatically filtered by user's branch via BaseModel global scope
- Users can only see and manage terms for their assigned branch
- No cross-branch data access possible

### 2. Overlap Detection
- `Term::hasOverlap()` automatically branch-scoped
- Prevents overlapping terms within the same branch only
- Different branches can have overlapping term dates

### 3. Status Management
- Active term status tracked per branch
- Multiple branches can have active terms simultaneously
- Closing active terms only affects current branch

### 4. Dashboard Statistics
- All dashboard data automatically branch-filtered
- Shows only current branch's terms and statistics
- No code changes required for branch isolation

## Multi-Tenant Behavior

### Single Branch Installation
- Works exactly as before
- All terms default to `branch_id = 1`
- No visible changes to UI or functionality

### Multi-Branch Installation
- Each branch has independent term management
- Branch column displayed in term listings
- Users see only their branch's terms
- Super admins can potentially see all branches (based on role configuration)

## Security & Data Integrity

### Automatic Protection
- BaseModel global scope prevents unauthorized cross-branch access
- Branch context automatically set from `auth()->user()->branch_id`
- Foreign key constraints prevent orphaned records

### Validation
- Branch assignment automatic during term creation
- Overlap detection respects branch boundaries
- Sequence validation within branch context

## Performance Optimizations

### Database
- Index on `terms.branch_id` for fast filtering
- Foreign key constraint for referential integrity
- Composite unique key includes branch_id

### Query Optimization
- Eager loading of branch relationship (N+1 prevention)
- Conditional loading only when MultiBranch module enabled
- Global scope applied at query builder level (efficient)

## Backward Compatibility

### Existing Data
- Migration sets `branch_id = 1` for all existing terms
- No data loss or disruption
- Seamless upgrade path

### Single School Mode
- All functionality preserved
- No visible UI changes when MultiBranch disabled
- Terms work exactly as before

## Testing Checklist

- [x] Migration runs successfully
- [x] Existing terms assigned default branch_id
- [x] Foreign key constraint created
- [x] Unique constraint includes branch_id
- [x] Term model has branch relationship
- [x] TermDefinition model doesn't filter by branch
- [x] Branch model has terms relationship
- [x] Repository includes branch data in output
- [x] Views conditionally show branch column
- [x] DataTables configuration updated

## Deployment Instructions

### 1. Run Migration
```bash
php artisan migrate --path=database/migrations/tenant/2025_10_03_000001_add_branch_foreign_key_to_terms.php
```

### 2. Verify Data
```sql
-- Check all terms have branch_id assigned
SELECT COUNT(*) as total, COUNT(branch_id) as with_branch FROM terms;

-- Verify foreign key constraint
SHOW CREATE TABLE terms;
```

### 3. Test Functionality
1. Create new term - verify branch_id is set automatically
2. View term listing - verify branch column shows (if MultiBranch enabled)
3. Check overlap detection - verify works within branch only
4. Verify dashboard shows branch-specific data

## Future Enhancements

### Potential Features
- **Cross-branch viewing for super-admins:** Add branch filter dropdown
- **Branch-specific term templates:** Allow customizing term definitions per branch
- **Bulk term creation:** Create same term across multiple branches at once
- **Cross-branch reporting:** Consolidated reports across all branches

### Configuration Options
- **Default branch assignment:** Configure default branch for new terms
- **Branch visibility:** Control which users can see all branches vs own branch only
- **Template sharing:** Option to make term definitions branch-specific instead of shared

## Summary

✅ **Complete multi-tenant implementation**
✅ **Zero breaking changes**
✅ **Backward compatible**
✅ **Automatic branch isolation**
✅ **Leverages existing BaseModel infrastructure**
✅ **Minimal code changes required**
✅ **Production ready**

The Terms module is now fully multi-tenant aware, with each branch able to independently manage its academic terms while sharing common term definition templates across the institution.
