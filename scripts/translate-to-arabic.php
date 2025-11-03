#!/usr/bin/env php
<?php

/**
 * Arabic Translation Script (PHP Version)
 * Translates all English JSON files to Arabic using Google Translate
 * More reliable than Node.js version for batch operations
 */

// Configuration
define('SOURCE_LANG', 'en');
define('TARGET_LANG', 'ar');
define('SOURCE_DIR', __DIR__ . '/../lang/en');
define('TARGET_DIR', __DIR__ . '/../lang/ar');
define('DELAY_MS', 500); // 500ms delay between requests
define('BATCH_SIZE', 10); // Process 10 keys then take a longer break

// Statistics
$stats = [
    'total_files' => 0,
    'total_keys' => 0,
    'translated' => 0,
    'errors' => 0,
    'start_time' => microtime(true),
];

/**
 * Print colored output
 */
function printColor($text, $color = 'white') {
    $colors = [
        'red' => "\033[31m",
        'green' => "\033[32m",
        'yellow' => "\033[33m",
        'blue' => "\033[34m",
        'magenta' => "\033[35m",
        'cyan' => "\033[36m",
        'white' => "\033[37m",
        'reset' => "\033[0m",
    ];

    echo $colors[$color] . $text . $colors['reset'];
}

/**
 * Translate text using Google Translate API (free method)
 */
function translateText($text, $retries = 3) {
    if (empty($text)) {
        return $text;
    }

    // Preserve placeholders
    $placeholders = [];
    $text = preg_replace_callback('/:([\w]+)|\{([\w]+)\}/', function($match) use (&$placeholders) {
        $placeholders[] = $match[0];
        return '__PH' . (count($placeholders) - 1) . '__';
    }, $text);

    for ($attempt = 1; $attempt <= $retries; $attempt++) {
        try {
            // Use Google Translate web endpoint
            $url = 'https://translate.googleapis.com/translate_a/single?client=gtx&sl='
                   . SOURCE_LANG . '&tl=' . TARGET_LANG . '&dt=t&q=' . urlencode($text);

            // Add user agent to avoid blocking
            $opts = [
                'http' => [
                    'method' => 'GET',
                    'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36\r\n"
                ]
            ];

            $context = stream_context_create($opts);
            $response = @file_get_contents($url, false, $context);

            if ($response === false) {
                throw new Exception('Request failed');
            }

            // Parse response
            $result = json_decode($response, true);

            if (isset($result[0][0][0])) {
                $translated = $result[0][0][0];

                // Restore placeholders
                foreach ($placeholders as $index => $placeholder) {
                    $translated = str_replace('__PH' . $index . '__', $placeholder, $translated);
                }

                return $translated;
            }

            throw new Exception('Invalid response format');

        } catch (Exception $e) {
            if ($attempt === $retries) {
                printColor("   ❌ Translation failed: " . substr($text, 0, 40) . "...\n", 'red');
                return $text; // Return original on failure
            }

            // Exponential backoff
            $delay = $attempt * 2;
            printColor("   ⚠️  Retry $attempt/$retries, waiting {$delay}s...\n", 'yellow');
            sleep($delay);
        }
    }

    return $text;
}

/**
 * Translate a single JSON file
 */
function translateJsonFile($filename) {
    global $stats;

    $sourcePath = SOURCE_DIR . '/' . $filename;
    $targetPath = TARGET_DIR . '/' . $filename;

    printColor("\n📄 Processing: $filename\n", 'cyan');

    try {
        // Read source file
        $sourceContent = file_get_contents($sourcePath);
        $sourceData = json_decode($sourceContent, true);

        if (!$sourceData) {
            throw new Exception('Failed to parse JSON');
        }

        $keys = array_keys($sourceData);
        $totalKeys = count($keys);
        printColor("   Keys to translate: $totalKeys\n", 'white');

        $stats['total_keys'] += $totalKeys;

        // Translate each key
        $translatedData = [];
        $progress = 0;

        foreach ($keys as $key) {
            $originalValue = $sourceData[$key];
            $progress++;

            // Show progress
            $percentage = round(($progress / $totalKeys) * 100);
            echo "   [{$progress}/{$totalKeys}] {$percentage}% - ";
            printColor(substr($originalValue, 0, 40) . "...\n", 'white');

            // Translate
            $translatedValue = translateText($originalValue);
            $translatedData[$key] = $translatedValue;

            $stats['translated']++;

            // Add delay to avoid rate limiting
            usleep(DELAY_MS * 1000);

            // Longer break every BATCH_SIZE translations
            if ($progress % BATCH_SIZE === 0 && $progress < $totalKeys) {
                printColor("   ⏸️  Batch break (2s)...\n", 'yellow');
                sleep(2);
            }
        }

        // Write translated file with proper JSON formatting
        $json = json_encode($translatedData, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        file_put_contents($targetPath, $json);

        printColor("   ✅ Completed: $filename\n", 'green');
        $stats['total_files']++;

        return true;

    } catch (Exception $e) {
        printColor("   ❌ Error: " . $e->getMessage() . "\n", 'red');
        $stats['errors']++;
        return false;
    }
}

/**
 * Main execution
 */
function main() {
    global $stats;

    printColor("╔════════════════════════════════════════════════╗\n", 'cyan');
    printColor("║   Arabic Translation Script (PHP Edition)     ║\n", 'cyan');
    printColor("║   Translating English to Arabic               ║\n", 'cyan');
    printColor("╚════════════════════════════════════════════════╝\n\n", 'cyan');

    // Ensure target directory exists
    if (!is_dir(TARGET_DIR)) {
        mkdir(TARGET_DIR, 0777, true);
        printColor("✅ Created target directory\n\n", 'green');
    }

    // Get all JSON files
    $files = array_filter(scandir(SOURCE_DIR), function($file) {
        return pathinfo($file, PATHINFO_EXTENSION) === 'json';
    });

    if (empty($files)) {
        printColor("❌ No JSON files found!\n", 'red');
        exit(1);
    }

    $fileCount = count($files);
    printColor("📚 Found $fileCount JSON files to translate\n\n", 'white');
    printColor("⏱️  Estimated time: " . round(($fileCount * 5), 0) . "-" . round(($fileCount * 10), 0) . " minutes\n\n", 'yellow');

    // Translate each file
    $fileNum = 0;
    foreach ($files as $file) {
        $fileNum++;
        printColor("═══ File $fileNum/$fileCount ═══\n", 'magenta');
        translateJsonFile($file);

        // Delay between files
        if ($fileNum < $fileCount) {
            printColor("\n⏳ Waiting 3s before next file...\n", 'yellow');
            sleep(3);
        }
    }

    // Print summary
    $duration = round((microtime(true) - $stats['start_time']) / 60, 2);

    printColor("\n╔════════════════════════════════════════════════╗\n", 'green');
    printColor("║            Translation Complete!               ║\n", 'green');
    printColor("╚════════════════════════════════════════════════╝\n\n", 'green');

    printColor("📊 Statistics:\n", 'cyan');
    printColor("   Files: {$stats['total_files']}/$fileCount\n", 'white');
    printColor("   Keys translated: {$stats['translated']}/{$stats['total_keys']}\n", 'white');
    printColor("   Errors: {$stats['errors']}\n", 'white');
    printColor("   Duration: $duration minutes\n\n", 'white');

    printColor("✅ Arabic files saved to: " . TARGET_DIR . "\n\n", 'green');

    if ($stats['errors'] > 0) {
        printColor("⚠️  {$stats['errors']} translations failed.\n", 'yellow');
        printColor("   These kept English text and can be corrected via admin UI.\n\n", 'yellow');
    }

    printColor("🎯 Next Steps:\n", 'cyan');
    printColor("   1. Test language switcher in dashboard\n", 'white');
    printColor("   2. Review critical files at /languages/2/terms\n", 'white');
    printColor("   3. Verify Student/Parent portal pages\n\n", 'white');
}

// Run the script
try {
    main();
} catch (Exception $e) {
    printColor("\n❌ Fatal error: " . $e->getMessage() . "\n", 'red');
    exit(1);
}
