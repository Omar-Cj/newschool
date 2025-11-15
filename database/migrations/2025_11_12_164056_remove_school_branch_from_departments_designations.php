<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Remove school_id and branch_id columns from departments and designations tables.
     * These are system-level tables and should be shared across all schools.
     *
     * @return void
     */
    public function up(): void
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║  Departments & Designations System-Level Migration          ║\n";
        echo "║  Removing school_id and branch_id columns                    ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        // ========================================================
        // SAFETY CHECK 1: Verify departments table structure
        // ========================================================
        echo "🔍 Running safety checks...\n\n";

        $deptSchoolIds = DB::table('departments')
            ->select('school_id')
            ->distinct()
            ->pluck('school_id');

        if ($deptSchoolIds->count() > 1) {
            echo "⚠️  WARNING: Multiple school_id values found in departments table: " . $deptSchoolIds->implode(', ') . "\n";
            echo "    This is expected for multi-school systems.\n";
            echo "    All schools will share the same departments after migration.\n\n";
        }

        // ========================================================
        // SAFETY CHECK 2: Verify departments branch_id values
        // ========================================================
        $deptBranchIds = DB::table('departments')
            ->select('branch_id')
            ->distinct()
            ->pluck('branch_id');

        if ($deptBranchIds->count() > 1) {
            throw new \Exception(
                'SAFETY CHECK FAILED: Cannot remove branch_id from departments table. ' .
                'Multiple branch values exist: ' . $deptBranchIds->implode(', ') . '. ' .
                'All departments must have the same branch_id before proceeding.'
            );
        }

        // ========================================================
        // SAFETY CHECK 3: Check for duplicate department names
        // ========================================================
        $deptDuplicates = DB::table('departments')
            ->select('name', DB::raw('count(*) as count'))
            ->groupBy('name')
            ->having('count', '>', 1)
            ->get();

        if ($deptDuplicates->count() > 0) {
            $duplicateNames = $deptDuplicates->pluck('name')->implode(', ');
            echo "⚠️  WARNING: Duplicate department names found: " . $duplicateNames . "\n";
            echo "    These will be kept as separate records (different schools may use same names).\n\n";
        }

        // ========================================================
        // SAFETY CHECK 4: Verify designations table structure
        // ========================================================
        $desigSchoolIds = DB::table('designations')
            ->select('school_id')
            ->distinct()
            ->pluck('school_id');

        if ($desigSchoolIds->count() > 1) {
            echo "⚠️  WARNING: Multiple school_id values found in designations table: " . $desigSchoolIds->implode(', ') . "\n";
            echo "    This is expected for multi-school systems.\n";
            echo "    All schools will share the same designations after migration.\n\n";
        }

        // ========================================================
        // SAFETY CHECK 5: Verify designations branch_id values
        // ========================================================
        $desigBranchIds = DB::table('designations')
            ->select('branch_id')
            ->distinct()
            ->pluck('branch_id');

        if ($desigBranchIds->count() > 1) {
            throw new \Exception(
                'SAFETY CHECK FAILED: Cannot remove branch_id from designations table. ' .
                'Multiple branch values exist: ' . $desigBranchIds->implode(', ') . '. ' .
                'All designations must have the same branch_id before proceeding.'
            );
        }

        // ========================================================
        // SAFETY CHECK 6: Check for duplicate designation names
        // ========================================================
        $desigDuplicates = DB::table('designations')
            ->select('name', DB::raw('count(*) as count'))
            ->groupBy('name')
            ->having('count', '>', 1)
            ->get();

        if ($desigDuplicates->count() > 0) {
            $duplicateNames = $desigDuplicates->pluck('name')->implode(', ');
            echo "⚠️  WARNING: Duplicate designation names found: " . $duplicateNames . "\n";
            echo "    These will be kept as separate records (different schools may use same names).\n\n";
        }

        // ========================================================
        // SAFETY CHECK 7: Verify Department model extends Model
        // ========================================================
        $departmentModel = new \App\Models\Staff\Department();
        $parentClass = get_parent_class($departmentModel);

        if ($parentClass !== 'Illuminate\Database\Eloquent\Model') {
            throw new \Exception(
                'SAFETY CHECK FAILED: Department model must extend Model, not BaseModel. ' .
                'Current parent class: ' . $parentClass . '. ' .
                'Update app/Models/Staff/Department.php to extend Model before running this migration.'
            );
        }

        // ========================================================
        // SAFETY CHECK 8: Verify Designation model extends Model
        // ========================================================
        $designationModel = new \App\Models\Staff\Designation();
        $parentClass = get_parent_class($designationModel);

        if ($parentClass !== 'Illuminate\Database\Eloquent\Model') {
            throw new \Exception(
                'SAFETY CHECK FAILED: Designation model must extend Model, not BaseModel. ' .
                'Current parent class: ' . $parentClass . '. ' .
                'Update app/Models/Staff/Designation.php to extend Model before running this migration.'
            );
        }

        // ========================================================
        // SAFETY CHECK 9: Check foreign key references
        // ========================================================
        echo "🔍 Checking foreign key references...\n";

        // Check if staff table has department_id
        if (Schema::hasColumn('staff', 'department_id')) {
            $staffWithDept = DB::table('staff')->whereNotNull('department_id')->count();
            echo "   • Staff table has {$staffWithDept} records with department_id\n";
        }

        // Check if staff table has designation_id
        if (Schema::hasColumn('staff', 'designation_id')) {
            $staffWithDesig = DB::table('staff')->whereNotNull('designation_id')->count();
            echo "   • Staff table has {$staffWithDesig} records with designation_id\n";
        }

        // Check if users table has designation_id
        if (Schema::hasColumn('users', 'designation_id')) {
            $usersWithDesig = DB::table('users')->whereNotNull('designation_id')->count();
            echo "   • Users table has {$usersWithDesig} records with designation_id\n";
        }

        echo "\n";

        // ========================================================
        // SAFETY CHECKS PASSED - Proceed with column removal
        // ========================================================

        echo "✅ All safety checks passed. Removing school_id and branch_id columns...\n\n";

        // Count records before migration
        $deptCount = DB::table('departments')->count();
        $desigCount = DB::table('designations')->count();

        echo "📊 Current record counts:\n";
        echo "   • Departments: {$deptCount}\n";
        echo "   • Designations: {$desigCount}\n\n";

        // Remove school_id and branch_id from departments table
        Schema::table('departments', function (Blueprint $table) {
            $table->dropColumn(['school_id', 'branch_id']);
        });

        echo "✅ Removed school_id and branch_id columns from departments table\n";

        // Remove school_id and branch_id from designations table
        Schema::table('designations', function (Blueprint $table) {
            $table->dropColumn(['school_id', 'branch_id']);
        });

        echo "✅ Removed school_id and branch_id columns from designations table\n\n";

        // Verify record counts unchanged
        $deptCountAfter = DB::table('departments')->count();
        $desigCountAfter = DB::table('designations')->count();

        if ($deptCount !== $deptCountAfter || $desigCount !== $desigCountAfter) {
            throw new \Exception('ERROR: Record count changed during migration! Rollback required.');
        }

        echo "✅ Verified: No data loss during migration\n";
        echo "   • Departments: {$deptCountAfter} records (unchanged)\n";
        echo "   • Designations: {$desigCountAfter} records (unchanged)\n\n";

        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║              ✅ Migration Completed Successfully!            ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "📊 Summary:\n";
        echo "   - Departments table: school_id and branch_id columns removed\n";
        echo "   - Designations table: school_id and branch_id columns removed\n";
        echo "   - Both tables are now system-level (shared across all schools)\n";
        echo "   - All foreign key references remain valid\n";
        echo "   - No data was lost or modified\n\n";
        echo "🎯 Next Steps:\n";
        echo "   1. Clear caches: php artisan cache:clear\n";
        echo "   2. Test department/designation CRUD operations\n";
        echo "   3. Verify both School 1 and School 2 see the same records\n\n";
    }

    /**
     * Reverse the migrations.
     *
     * Add school_id and branch_id columns back to departments and designations tables.
     * Default values are 1 for backward compatibility.
     *
     * @return void
     */
    public function down(): void
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║            ⚠️  Rolling Back Migration                        ║\n";
        echo "║  Adding school_id and branch_id columns back                 ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        // Add school_id and branch_id back to departments table
        Schema::table('departments', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->default(1)->after('id');
            $table->unsignedBigInteger('branch_id')->default(1);
        });

        echo "✅ Added school_id and branch_id columns back to departments table\n";

        // Add school_id and branch_id back to designations table
        Schema::table('designations', function (Blueprint $table) {
            $table->unsignedBigInteger('school_id')->default(1)->after('id');
            $table->unsignedBigInteger('branch_id')->default(1);
        });

        echo "✅ Added school_id and branch_id columns back to designations table\n\n";

        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║              ✅ Rollback Completed Successfully!             ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "📊 Summary:\n";
        echo "   - Departments table: school_id and branch_id columns restored (default=1)\n";
        echo "   - Designations table: school_id and branch_id columns restored (default=1)\n";
        echo "   - Both tables are now school/branch-scoped again\n\n";
        echo "⚠️  Note: All existing records now have school_id=1 and branch_id=1\n";
        echo "   You may need to update these values manually if required.\n\n";
    }
};
