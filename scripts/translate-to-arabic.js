#!/usr/bin/env node

/**
 * Arabic Translation Script
 * Translates all English JSON files to Arabic using Google Translate API
 */

const fs = require('fs');
const path = require('path');
const { translate } = require('@vitalets/google-translate-api');

// Configuration
const SOURCE_LANG = 'en';
const TARGET_LANG = 'ar';
const SOURCE_DIR = path.join(__dirname, '../lang/en');
const TARGET_DIR = path.join(__dirname, '../lang/ar');

// Translation statistics
const stats = {
    totalFiles: 0,
    totalKeys: 0,
    translatedKeys: 0,
    errors: 0,
    startTime: Date.now()
};

/**
 * Preserve placeholders in text before translation
 * Examples: :attribute, {name}, :min, {count}
 */
function preservePlaceholders(text) {
    const placeholders = [];
    let index = 0;

    // Replace placeholders with temporary markers
    const processed = text
        .replace(/:([\w]+)/g, (match) => {
            placeholders.push(match);
            return `__PLACEHOLDER_${index++}__`;
        })
        .replace(/\{([\w]+)\}/g, (match) => {
            placeholders.push(match);
            return `__PLACEHOLDER_${index++}__`;
        });

    return { processed, placeholders };
}

/**
 * Restore placeholders after translation
 */
function restorePlaceholders(text, placeholders) {
    let restored = text;
    placeholders.forEach((placeholder, index) => {
        restored = restored.replace(`__PLACEHOLDER_${index}__`, placeholder);
    });
    return restored;
}

/**
 * Translate text to Arabic with retry logic
 */
async function translateText(text, retries = 3) {
    if (!text || typeof text !== 'string') {
        return text;
    }

    // Preserve placeholders
    const { processed, placeholders } = preservePlaceholders(text);

    for (let attempt = 1; attempt <= retries; attempt++) {
        try {
            // Add delay to avoid rate limiting
            if (attempt > 1) {
                await new Promise(resolve => setTimeout(resolve, 1000 * attempt));
            }

            const result = await translate(processed, { from: SOURCE_LANG, to: TARGET_LANG });
            const translated = restorePlaceholders(result.text, placeholders);

            return translated;
        } catch (error) {
            if (attempt === retries) {
                console.error(`❌ Translation failed after ${retries} attempts: ${text.substring(0, 50)}...`);
                console.error(`   Error: ${error.message}`);
                stats.errors++;
                return text; // Return original text on failure
            }
            console.warn(`⚠️  Retry ${attempt}/${retries} for: ${text.substring(0, 30)}...`);
        }
    }

    return text;
}

/**
 * Translate a single JSON file
 */
async function translateJsonFile(filename) {
    const sourcePath = path.join(SOURCE_DIR, filename);
    const targetPath = path.join(TARGET_DIR, filename);

    console.log(`\n📄 Processing: ${filename}`);

    try {
        // Read source file
        const sourceContent = fs.readFileSync(sourcePath, 'utf8');
        const sourceData = JSON.parse(sourceContent);

        const keys = Object.keys(sourceData);
        console.log(`   Keys to translate: ${keys.length}`);

        stats.totalKeys += keys.length;

        // Translate each key
        const translatedData = {};
        let progress = 0;

        for (const key of keys) {
            const originalValue = sourceData[key];

            // Translate the value
            const translatedValue = await translateText(originalValue);
            translatedData[key] = translatedValue;

            stats.translatedKeys++;
            progress++;

            // Show progress every 10 keys
            if (progress % 10 === 0) {
                console.log(`   Progress: ${progress}/${keys.length} keys...`);
            }

            // Small delay to avoid rate limiting
            await new Promise(resolve => setTimeout(resolve, 100));
        }

        // Write translated file with proper formatting
        fs.writeFileSync(
            targetPath,
            JSON.stringify(translatedData, null, 4),
            'utf8'
        );

        console.log(`   ✅ Completed: ${keys.length} keys translated`);
        stats.totalFiles++;

        return true;
    } catch (error) {
        console.error(`   ❌ Error processing ${filename}:`, error.message);
        stats.errors++;
        return false;
    }
}

/**
 * Main execution
 */
async function main() {
    console.log('╔════════════════════════════════════════════════╗');
    console.log('║   Arabic Translation Script for Laravel       ║');
    console.log('║   Auto-translating English to Arabic          ║');
    console.log('╚════════════════════════════════════════════════╝\n');

    // Ensure target directory exists
    if (!fs.existsSync(TARGET_DIR)) {
        fs.mkdirSync(TARGET_DIR, { recursive: true });
        console.log(`✅ Created target directory: ${TARGET_DIR}\n`);
    }

    // Get all JSON files from source directory
    const files = fs.readdirSync(SOURCE_DIR).filter(file => file.endsWith('.json'));

    if (files.length === 0) {
        console.error('❌ No JSON files found in source directory!');
        process.exit(1);
    }

    console.log(`📚 Found ${files.length} JSON files to translate\n`);
    console.log('Starting translation process...');
    console.log('⏳ This may take several minutes depending on file size.\n');

    // Translate each file
    for (const file of files) {
        await translateJsonFile(file);
    }

    // Print summary
    const duration = ((Date.now() - stats.startTime) / 1000).toFixed(2);

    console.log('\n╔════════════════════════════════════════════════╗');
    console.log('║            Translation Summary                 ║');
    console.log('╚════════════════════════════════════════════════╝');
    console.log(`\n📊 Statistics:`);
    console.log(`   Files processed: ${stats.totalFiles}/${files.length}`);
    console.log(`   Total keys: ${stats.totalKeys}`);
    console.log(`   Translated: ${stats.translatedKeys}`);
    console.log(`   Errors: ${stats.errors}`);
    console.log(`   Duration: ${duration} seconds`);
    console.log(`\n✅ Translation complete!`);
    console.log(`\n📁 Arabic files saved to: ${TARGET_DIR}`);

    if (stats.errors > 0) {
        console.log(`\n⚠️  Warning: ${stats.errors} translation errors occurred.`);
        console.log(`   Please review the error messages above.`);
    }

    console.log('\n🎯 Next steps:');
    console.log('   1. Test language switcher in dashboard');
    console.log('   2. Review critical files via admin UI at /languages/2/terms');
    console.log('   3. Perform functional testing\n');
}

// Run the script
main().catch(error => {
    console.error('\n❌ Fatal error:', error);
    process.exit(1);
});
