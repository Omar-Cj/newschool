#!/usr/bin/env node

/**
 * Arabic Translation Script (Rate-Limit Safe Version)
 * Uses slower translation with longer delays to avoid rate limiting
 */

const fs = require('fs');
const path = require('path');
const { translate } = require('@vitalets/google-translate-api');
const { getCode } = require('country-list');

// Configuration
const SOURCE_LANG = 'en';
const TARGET_LANG = 'ar';
const SOURCE_DIR = path.join(__dirname, '../lang/en');
const TARGET_DIR = path.join(__dirname, '../lang/ar');
const DELAY_BETWEEN_REQUESTS = 2000; // 2 seconds between translations
const DELAY_BETWEEN_FILES = 5000; // 5 seconds between files

// Translation statistics
const stats = {
    totalFiles: 0,
    totalKeys: 0,
    translatedKeys: 0,
    skippedKeys: 0,
    errors: 0,
    startTime: Date.now()
};

/**
 * Sleep function
 */
function sleep(ms) {
    return new Promise(resolve => setTimeout(resolve, ms));
}

/**
 * Preserve placeholders in text before translation
 */
function preservePlaceholders(text) {
    const placeholders = [];
    let index = 0;

    const processed = text
        .replace(/:([\w]+)/g, (match) => {
            placeholders.push(match);
            return `__PH${index++}__`;
        })
        .replace(/\{([\w]+)\}/g, (match) => {
            placeholders.push(match);
            return `__PH${index++}__`;
        })
        .replace(/\/([\w]+)/g, (match) => {
            placeholders.push(match);
            return `__PH${index++}__`;
        });

    return { processed, placeholders };
}

/**
 * Restore placeholders after translation
 */
function restorePlaceholders(text, placeholders) {
    let restored = text;
    placeholders.forEach((placeholder, index) => {
        restored = restored.replace(`__PH${index}__`, placeholder);
    });
    return restored;
}

/**
 * Translate text to Arabic with aggressive retry and delay logic
 */
async function translateText(text, retries = 5) {
    if (!text || typeof text !== 'string') {
        return text;
    }

    // Preserve placeholders
    const { processed, placeholders } = preservePlaceholders(text);

    for (let attempt = 1; attempt <= retries; attempt++) {
        try {
            // Exponential backoff: 2s, 4s, 8s, 16s, 32s
            if (attempt > 1) {
                const delay = DELAY_BETWEEN_REQUESTS * Math.pow(2, attempt - 1);
                console.log(`   ⏳ Waiting ${delay/1000}s before retry ${attempt}...`);
                await sleep(delay);
            }

            const result = await translate(processed, {
                from: SOURCE_LANG,
                to: TARGET_LANG,
                fetchOptions: {
                    agent: null
                }
            });

            const translated = restorePlaceholders(result.text, placeholders);
            return translated;

        } catch (error) {
            if (attempt === retries) {
                console.error(`   ❌ Failed: "${text.substring(0, 40)}..." - keeping English`);
                stats.errors++;
                return text; // Return original text on failure
            }
            console.warn(`   ⚠️  Attempt ${attempt}/${retries} failed, retrying...`);
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
        console.log(`   Total keys: ${keys.length}`);

        stats.totalKeys += keys.length;

        // Translate each key
        const translatedData = {};
        let progress = 0;

        for (const key of keys) {
            const originalValue = sourceData[key];
            progress++;

            console.log(`   [${progress}/${keys.length}] Translating: "${originalValue.substring(0, 40)}..."`);

            // Translate the value
            const translatedValue = await translateText(originalValue);
            translatedData[key] = translatedValue;

            stats.translatedKeys++;

            // Delay between requests to avoid rate limiting
            if (progress < keys.length) {
                await sleep(DELAY_BETWEEN_REQUESTS);
            }
        }

        // Write translated file
        fs.writeFileSync(
            targetPath,
            JSON.stringify(translatedData, null, 4),
            'utf8'
        );

        console.log(`   ✅ Completed: ${filename}`);
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
    console.log('║   Arabic Translation Script (Safe Mode)       ║');
    console.log('║   Slower translation to avoid rate limits     ║');
    console.log('╚════════════════════════════════════════════════╝\n');

    console.log(`⏱️  Delay between translations: ${DELAY_BETWEEN_REQUESTS/1000}s`);
    console.log(`⏱️  Delay between files: ${DELAY_BETWEEN_FILES/1000}s\n`);

    // Ensure target directory exists
    if (!fs.existsSync(TARGET_DIR)) {
        fs.mkdirSync(TARGET_DIR, { recursive: true });
    }

    // Get all JSON files from source directory
    const files = fs.readdirSync(SOURCE_DIR).filter(file => file.endsWith('.json'));

    if (files.length === 0) {
        console.error('❌ No JSON files found!');
        process.exit(1);
    }

    console.log(`📚 Found ${files.length} JSON files to translate\n`);

    // Translate each file
    for (let i = 0; i < files.length; i++) {
        await translateJsonFile(files[i]);

        // Delay between files
        if (i < files.length - 1) {
            console.log(`\n⏳ Waiting ${DELAY_BETWEEN_FILES/1000}s before next file...`);
            await sleep(DELAY_BETWEEN_FILES);
        }
    }

    // Print summary
    const duration = ((Date.now() - stats.startTime) / 1000 / 60).toFixed(2);

    console.log('\n╔════════════════════════════════════════════════╗');
    console.log('║            Translation Complete!               ║');
    console.log('╚════════════════════════════════════════════════╝');
    console.log(`\n📊 Statistics:`);
    console.log(`   Files: ${stats.totalFiles}/${files.length}`);
    console.log(`   Keys: ${stats.translatedKeys}/${stats.totalKeys}`);
    console.log(`   Errors: ${stats.errors}`);
    console.log(`   Duration: ${duration} minutes`);
    console.log(`\n✅ Arabic files saved to: ${TARGET_DIR}`);

    if (stats.errors > 0) {
        console.log(`\n⚠️  ${stats.errors} translations failed and kept English text.`);
        console.log(`   These can be manually corrected via admin UI.`);
    }

    console.log('\n🎯 Next steps:');
    console.log('   1. Test: Switch language to Arabic in dashboard');
    console.log('   2. Review: Visit /languages/2/terms for manual corrections');
    console.log('   3. Verify: Check critical pages (dashboard, fees, students)\n');
}

// Run the script
main().catch(error => {
    console.error('\n❌ Fatal error:', error);
    process.exit(1);
});
