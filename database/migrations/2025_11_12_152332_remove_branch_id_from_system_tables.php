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
     * Remove branch_id column from permissions and roles tables.
     * These are system-level tables and should not be scoped by branch.
     *
     * @return void
     */
    public function up(): void
    {
        // ========================================================
        // SAFETY CHECK 1: Verify permissions table has single branch_id
        // ========================================================
        $permissionBranches = DB::table('permissions')
            ->select('branch_id')
            ->distinct()
            ->pluck('branch_id');

        if ($permissionBranches->count() > 1) {
            throw new \Exception(
                'SAFETY CHECK FAILED: Cannot remove branch_id from permissions table. ' .
                'Multiple branch values exist: ' . $permissionBranches->implode(', ') . '. ' .
                'All permissions must have the same branch_id before proceeding.'
            );
        }

        // ========================================================
        // SAFETY CHECK 2: Verify no duplicate permission attributes
        // ========================================================
        $duplicates = DB::table('permissions')
            ->select('attribute', DB::raw('count(*) as count'))
            ->groupBy('attribute')
            ->having('count', '>', 1)
            ->get();

        if ($duplicates->count() > 0) {
            $duplicateList = $duplicates->pluck('attribute')->implode(', ');
            throw new \Exception(
                'SAFETY CHECK FAILED: Cannot remove branch_id from permissions table. ' .
                'Duplicate attributes still exist: ' . $duplicateList . '. ' .
                'Run de-duplication first before removing branch_id column.'
            );
        }

        // ========================================================
        // SAFETY CHECK 3: Verify roles table has single branch value
        // ========================================================
        $roleBranches = DB::table('roles')
            ->select('branch_id')
            ->distinct()
            ->pluck('branch_id');

        if ($roleBranches->count() > 1) {
            throw new \Exception(
                'SAFETY CHECK FAILED: Cannot remove branch_id from roles table. ' .
                'Multiple branch values exist: ' . $roleBranches->implode(', ') . '. ' .
                'All roles must have the same branch_id before proceeding.'
            );
        }

        // ========================================================
        // SAFETY CHECK 4: Verify Permission model extends Model (not BaseModel)
        // ========================================================
        $permissionModel = new \App\Models\Permission();
        $parentClass = get_parent_class($permissionModel);

        if ($parentClass !== 'Illuminate\Database\Eloquent\Model') {
            throw new \Exception(
                'SAFETY CHECK FAILED: Permission model must extend Model, not BaseModel. ' .
                'Current parent class: ' . $parentClass . '. ' .
                'Update app/Models/Permission.php to extend Model before running this migration.'
            );
        }

        // ========================================================
        // SAFETY CHECK 5: Verify Role model extends Model (not BaseModel)
        // ========================================================
        $roleModel = new \App\Models\Role();
        $parentClass = get_parent_class($roleModel);

        if ($parentClass !== 'Illuminate\Database\Eloquent\Model') {
            throw new \Exception(
                'SAFETY CHECK FAILED: Role model must extend Model, not BaseModel. ' .
                'Current parent class: ' . $parentClass . '. ' .
                'Update app/Models/Role.php to extend Model before running this migration.'
            );
        }

        // ========================================================
        // SAFETY CHECKS PASSED - Proceed with column removal
        // ========================================================

        echo "\n✅ All safety checks passed. Removing branch_id columns...\n";

        // Remove branch_id from permissions table
        Schema::table('permissions', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });

        echo "✅ Removed branch_id column from permissions table\n";

        // Remove branch_id from roles table
        Schema::table('roles', function (Blueprint $table) {
            $table->dropColumn('branch_id');
        });

        echo "✅ Removed branch_id column from roles table\n";
        echo "\n✅ Migration completed successfully!\n";
        echo "📊 Summary:\n";
        echo "   - Permissions table: branch_id column removed\n";
        echo "   - Roles table: branch_id column removed\n";
        echo "   - Both tables are now system-level (no branch scoping)\n\n";
    }

    /**
     * Reverse the migrations.
     *
     * Add branch_id column back to permissions and roles tables.
     * Default value is 1 for backward compatibility.
     *
     * @return void
     */
    public function down(): void
    {
        echo "\n⚠️  Rolling back migration: Adding branch_id columns back...\n";

        // Add branch_id back to permissions table
        Schema::table('permissions', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->default(1)->after('keywords');
        });

        echo "✅ Added branch_id column back to permissions table\n";

        // Add branch_id back to roles table
        Schema::table('roles', function (Blueprint $table) {
            $table->unsignedBigInteger('branch_id')->default(1);
        });

        echo "✅ Added branch_id column back to roles table\n";
        echo "\n✅ Rollback completed successfully!\n";
        echo "📊 Summary:\n";
        echo "   - Permissions table: branch_id column restored (default=1)\n";
        echo "   - Roles table: branch_id column restored (default=1)\n";
        echo "   - Both tables are now branch-scoped again\n\n";
    }
};
