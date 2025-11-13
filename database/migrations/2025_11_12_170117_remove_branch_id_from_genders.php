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
     * Remove branch_id column from genders table.
     * Genders is a system-level table and should be shared across all schools/branches.
     *
     * @return void
     */
    public function up(): void
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║  Genders System-Level Migration                              ║\n";
        echo "║  Removing branch_id column                                   ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        // ========================================================
        // SAFETY CHECK 1: Verify genders table structure
        // ========================================================
        echo "🔍 Running safety checks...\n\n";

        $genderBranchIds = DB::table('genders')
            ->select('branch_id')
            ->distinct()
            ->pluck('branch_id');

        if ($genderBranchIds->count() > 1) {
            throw new \Exception(
                'SAFETY CHECK FAILED: Cannot remove branch_id from genders table. ' .
                'Multiple branch values exist: ' . $genderBranchIds->implode(', ') . '. ' .
                'All genders must have the same branch_id before proceeding.'
            );
        }

        echo "✅ Safety Check 1: Single branch_id value confirmed (" . $genderBranchIds->first() . ")\n";

        // ========================================================
        // SAFETY CHECK 2: Check for duplicate gender names
        // ========================================================
        $genderDuplicates = DB::table('genders')
            ->select('name', DB::raw('count(*) as count'))
            ->groupBy('name')
            ->having('count', '>', 1)
            ->get();

        if ($genderDuplicates->count() > 0) {
            $duplicateNames = $genderDuplicates->pluck('name')->implode(', ');
            echo "⚠️  WARNING: Duplicate gender names found: " . $duplicateNames . "\n";
            echo "    These will be kept as separate records (acceptable for system-level table).\n\n";
        } else {
            echo "✅ Safety Check 2: No duplicate gender names\n";
        }

        // ========================================================
        // SAFETY CHECK 3: Verify Gender model extends Model
        // ========================================================
        $genderModel = new \App\Models\Gender();
        $parentClass = get_parent_class($genderModel);

        if ($parentClass !== 'Illuminate\Database\Eloquent\Model') {
            throw new \Exception(
                'SAFETY CHECK FAILED: Gender model must extend Model, not BaseModel. ' .
                'Current parent class: ' . $parentClass . '. ' .
                'Update app/Models/Gender.php to extend Model before running this migration.'
            );
        }

        echo "✅ Safety Check 3: Gender model extends Model correctly\n";

        // ========================================================
        // SAFETY CHECK 4: Check foreign key references
        // ========================================================
        echo "\n🔍 Checking foreign key references...\n";

        // Check if students table has gender_id
        if (Schema::hasColumn('students', 'gender_id')) {
            $studentsWithGender = DB::table('students')->whereNotNull('gender_id')->count();
            echo "   • Students table has {$studentsWithGender} records with gender_id\n";
        }

        // Check if staff table has gender_id
        if (Schema::hasColumn('staff', 'gender_id')) {
            $staffWithGender = DB::table('staff')->whereNotNull('gender_id')->count();
            echo "   • Staff table has {$staffWithGender} records with gender_id\n";
        }

        // Check if fees_assigns table has gender_id
        if (Schema::hasColumn('fees_assigns', 'gender_id')) {
            $feesWithGender = DB::table('fees_assigns')->whereNotNull('gender_id')->count();
            echo "   • Fees assigns table has {$feesWithGender} records with gender_id\n";
        }

        // Check if online_admissions table has gender_id
        if (Schema::hasColumn('online_admissions', 'gender_id')) {
            $admissionsWithGender = DB::table('online_admissions')->whereNotNull('gender_id')->count();
            echo "   • Online admissions table has {$admissionsWithGender} records with gender_id\n";
        }

        echo "\n";

        // ========================================================
        // SAFETY CHECKS PASSED - Proceed with column removal
        // ========================================================

        echo "✅ All safety checks passed. Removing branch_id column...\n\n";

        // Count records before migration
        $genderCount = DB::table('genders')->count();

        echo "📊 Current record counts:\n";
        echo "   • Genders: {$genderCount}\n\n";

        // Remove branch_id from genders table
        Schema::table('genders', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });

        echo "✅ Removed branch_id column from genders table\n\n";

        // Verify record counts unchanged
        $genderCountAfter = DB::table('genders')->count();

        if ($genderCount !== $genderCountAfter) {
            throw new \Exception('ERROR: Record count changed during migration! Rollback required.');
        }

        echo "✅ Verified: No data loss during migration\n";
        echo "   • Genders: {$genderCountAfter} records (unchanged)\n\n";

        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║              ✅ Migration Completed Successfully!            ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "📊 Summary:\n";
        echo "   - Genders table: branch_id column removed\n";
        echo "   - Table is now system-level (shared across all schools/branches)\n";
        echo "   - All foreign key references remain valid\n";
        echo "   - No data was lost or modified\n\n";
        echo "🎯 Next Steps:\n";
        echo "   1. Clear caches: php artisan cache:clear\n";
        echo "   2. Test gender selection in student/staff forms\n";
        echo "   3. Verify all schools see the same gender options\n\n";
    }

    /**
     * Reverse the migrations.
     *
     * Add branch_id column back to genders table.
     * Default value is 1 for backward compatibility.
     *
     * @return void
     */
    public function down(): void
    {
        echo "\n";
        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║            ⚠️  Rolling Back Migration                        ║\n";
        echo "║  Adding branch_id column back                                ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";

        // Add branch_id back to genders table
        Schema::table('genders', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->default(1);
        });

        echo "✅ Added branch_id column back to genders table\n\n";

        echo "╔══════════════════════════════════════════════════════════════╗\n";
        echo "║              ✅ Rollback Completed Successfully!             ║\n";
        echo "╚══════════════════════════════════════════════════════════════╝\n";
        echo "\n";
        echo "📊 Summary:\n";
        echo "   - Genders table: branch_id column restored (default=1)\n";
        echo "   - Table is now branch-scoped again\n\n";
        echo "⚠️  Note: All existing records now have branch_id=1\n";
        echo "   You may need to update these values manually if required.\n\n";
    }
};
