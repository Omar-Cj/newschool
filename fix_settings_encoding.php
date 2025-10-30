<?php
/**
 * Fix double-encoded settings values
 *
 * This script removes the extra JSON encoding from settings values
 * that were incorrectly stored due to the array cast in the Setting model.
 *
 * Usage: php artisan tinker < fix_settings_encoding.php
 */

use App\Models\Setting;

// Get ALL settings to check for encoding issues
// This ensures we catch any setting that was affected by the array cast bug
$settings = Setting::all();

echo "Found " . $settings->count() . " settings to check\n";

foreach ($settings as $setting) {
    $originalValue = $setting->getRawOriginal('value');
    echo "\nSetting: {$setting->name}\n";
    echo "Original value: {$originalValue}\n";

    // Check if the value is JSON-encoded (starts and ends with quotes)
    if (is_string($originalValue) && substr($originalValue, 0, 1) === '"' && substr($originalValue, -1) === '"') {
        // Decode the JSON to get the actual value
        $decodedValue = json_decode($originalValue);

        if ($decodedValue !== null) {
            // Update directly in database to bypass model's save logic
            \DB::table('settings')
                ->where('id', $setting->id)
                ->update(['value' => $decodedValue]);

            echo "✓ Fixed: {$setting->name} -> {$decodedValue}\n";
        } else {
            echo "⚠ Could not decode: {$setting->name}\n";
        }
    } else {
        echo "○ Already clean: {$setting->name}\n";
    }
}

echo "\n✓ Done! Settings have been cleaned.\n";
echo "Please refresh your browser with Ctrl+Shift+R to see the changes.\n";
