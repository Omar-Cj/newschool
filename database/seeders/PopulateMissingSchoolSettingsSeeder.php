<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * PopulateMissingSchoolSettingsSeeder
 *
 * Populates missing settings for schools by using School 1 as a reference template.
 * This seeder is safe to run multiple times as it only inserts missing settings.
 *
 * Usage: php artisan db:seed --class=PopulateMissingSchoolSettingsSeeder
 */
class PopulateMissingSchoolSettingsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // School to populate settings for
        $targetSchoolId = 6;
        $targetBranchId = 9;

        // Reference school with complete settings
        $referenceSchoolId = 2;

        Log::info('PopulateMissingSchoolSettingsSeeder started', [
            'target_school_id' => $targetSchoolId,
            'target_branch_id' => $targetBranchId,
            'reference_school_id' => $referenceSchoolId
        ]);

        DB::beginTransaction();

        try {
            // Get all setting names and values from reference school (School 1)
            $referenceSettings = DB::table('settings')
                ->where('school_id', $referenceSchoolId)
                ->get(['name', 'value'])
                ->keyBy('name')
                ->toArray();

            echo "Found " . count($referenceSettings) . " reference settings from School {$referenceSchoolId}\n";

            // Get existing setting names for target school
            $existingSettings = DB::table('settings')
                ->where('school_id', $targetSchoolId)
                ->pluck('name')
                ->toArray();

            echo "School {$targetSchoolId} currently has " . count($existingSettings) . " settings\n";

            // Identify missing settings
            $missingSettings = array_diff(array_keys($referenceSettings), $existingSettings);

            if (empty($missingSettings)) {
                echo "No missing settings found for School {$targetSchoolId}. All settings are complete.\n";
                DB::commit();
                return;
            }

            echo "⚠️  Found " . count($missingSettings) . " missing settings for School {$targetSchoolId}\n";

            // Get target school's active session (if exists) for session setting
            $activeSession = DB::table('sessions')
                ->where('school_id', $targetSchoolId)
                ->where('status', 1)
                ->orderBy('id', 'desc')
                ->first();

            // Prepare settings to insert
            $settingsToInsert = [];
            $now = now();

            foreach ($missingSettings as $settingName) {
                $referenceValue = $referenceSettings[$settingName]->value;

                // Determine appropriate value for this setting
                $value = $this->getAppropriateValue(
                    $settingName,
                    $referenceValue,
                    $targetSchoolId,
                    $activeSession
                );

                $settingsToInsert[] = [
                    'school_id' => $targetSchoolId,
                    'branch_id' => $targetBranchId,
                    'name' => $settingName,
                    'value' => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];

                echo "  → Will create: {$settingName} = {$value}\n";
            }

            // Insert all missing settings
            DB::table('settings')->insert($settingsToInsert);

            DB::commit();

            echo "✅ Successfully created " . count($settingsToInsert) . " missing settings for School {$targetSchoolId}\n";

            Log::info('PopulateMissingSchoolSettingsSeeder completed successfully', [
                'settings_created' => count($settingsToInsert),
                'setting_names' => array_column($settingsToInsert, 'name')
            ]);

            echo "⚠️  Don't forget to run: php artisan cache:clear\n";

        } catch (\Exception $e) {
            DB::rollBack();

            echo "❌ Seeder failed: " . $e->getMessage() . "\n";

            Log::error('PopulateMissingSchoolSettingsSeeder failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);

            throw $e;
        }
    }

    /**
     * Get appropriate value for a setting based on its name and context
     *
     * @param string $settingName
     * @param mixed $referenceValue
     * @param int $targetSchoolId
     * @param object|null $activeSession
     * @return mixed
     */
    private function getAppropriateValue($settingName, $referenceValue, $targetSchoolId, $activeSession)
    {
        // Special handling for specific settings
        switch ($settingName) {
            case 'session':
                // Use target school's active session if available, otherwise use reference
                if ($activeSession) {
                    echo "    Using School {$targetSchoolId}'s active session: {$activeSession->id}\n";
                    return (string) $activeSession->id;
                }
                echo "    ⚠️  No active session found for School {$targetSchoolId}, using reference value\n";
                return $referenceValue;

            case 'application_name':
                // This should already exist for target school, but if not, use a default
                return "School {$targetSchoolId} - Management System";

            case 'footer_text':
                // Use a generic footer if missing
                return "© " . date('Y') . " School Management System. All rights reserved.";

            // For all other settings, copy from reference school
            // This includes: currency_code, currency_symbol, timezone, tax settings,
            // file_system settings, mail settings, etc.
            default:
                return $referenceValue;
        }
    }
}
