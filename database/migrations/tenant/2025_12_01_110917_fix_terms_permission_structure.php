<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Fix terms permission structure to match the standard pattern used by other permissions.
     *
     * Problem: Terms permissions were created as 4 separate records with granular names as attribute:
     * - terms_read, terms_create, terms_update, terms_delete (4 separate permission records)
     *
     * Solution: Consolidate into 1 properly structured record like other permissions:
     * - attribute: "terms"
     * - keywords: {"read":"terms_read","create":"terms_create","update":"terms_update","delete":"terms_delete"}
     *
     * This allows hasFeature('terms') to work correctly for Admin users (role_id=2).
     *
     * IMPORTANT: Updates permission_features table (NOT package_permission_features)
     * The relationship chain is: package → package_permission_features → permission_features → permissions
     *
     * @return void
     */
    public function up(): void
    {
        // Step 1: Create the correct "terms" permission record
        $termsPermission = \App\Models\Permission::create([
            'attribute' => 'terms',
            'keywords' => [
                'read' => 'terms_read',
                'create' => 'terms_create',
                'update' => 'terms_update',
                'delete' => 'terms_delete',
            ],
        ]);

        // Step 2: Get the old permission IDs (105, 106, 107, 108)
        $oldPermissionIds = \App\Models\Permission::whereIn('attribute', [
            'terms_read', 'terms_create', 'terms_update', 'terms_delete'
        ])->pluck('id')->toArray();

        // Step 3: Update permission_features to point to new permission
        // CRITICAL: Table is "permission_features" not "package_permission_features"
        // Column is "permission_id" which DOES exist in this table
        DB::table('permission_features')
            ->whereIn('permission_id', $oldPermissionIds)
            ->update(['permission_id' => $termsPermission->id]);

        // Step 4: Deduplicate - If same feature_group_id has multiple permission_features
        // now pointing to same permission, keep only the first one
        $featureGroupIds = DB::table('permission_features')
            ->where('permission_id', $termsPermission->id)
            ->distinct()
            ->pluck('feature_group_id');

        foreach ($featureGroupIds as $groupId) {
            // Keep only the first record, delete duplicates
            $records = DB::table('permission_features')
                ->where('permission_id', $termsPermission->id)
                ->where('feature_group_id', $groupId)
                ->get();

            if ($records->count() > 1) {
                $idsToDelete = $records->skip(1)->pluck('id');
                DB::table('permission_features')
                    ->whereIn('id', $idsToDelete)
                    ->delete();
            }
        }

        // Step 5: Delete the old incorrect permission records
        \App\Models\Permission::whereIn('attribute', [
            'terms_read', 'terms_create', 'terms_update', 'terms_delete'
        ])->delete();
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        // Get the correct terms permission
        $termsPermission = \App\Models\Permission::where('attribute', 'terms')->first();

        if ($termsPermission) {
            // Recreate the old granular permissions
            $oldPermissions = [
                ['attribute' => 'terms_read', 'keywords' => ['terms_read']],
                ['attribute' => 'terms_create', 'keywords' => ['terms_create']],
                ['attribute' => 'terms_update', 'keywords' => ['terms_update']],
                ['attribute' => 'terms_delete', 'keywords' => ['terms_delete']],
            ];

            foreach ($oldPermissions as $perm) {
                \App\Models\Permission::create($perm);
            }

            // Delete the correct permission
            $termsPermission->delete();
        }
    }
};
