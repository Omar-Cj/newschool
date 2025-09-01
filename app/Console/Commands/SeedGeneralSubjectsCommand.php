<?php

namespace App\Console\Commands;

use Database\Seeders\Academic\GeneralSubjectSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SeedGeneralSubjectsCommand extends Command
{
    protected $signature = 'subjects:seed-general 
                          {--level=all : Educational level (all, primary, secondary)}
                          {--dry-run : Preview what will be created without saving}
                          {--replace-existing=false : Replace existing subjects with same codes}';

    protected $description = 'Seed authentic Somaliland curriculum subjects for primary and secondary education';

    public function handle()
    {
        $level = $this->option('level');
        $isDryRun = $this->option('dry-run');
        $replaceExisting = $this->option('replace-existing') === 'true';

        // Validate level option
        if (!in_array($level, ['all', 'primary', 'secondary'])) {
            $this->error('Invalid level. Use: all, primary, or secondary');
            return 1;
        }

        $this->info('🇸🇴 Seeding Authentic Somaliland Curriculum Subjects');
        $this->line("   📚 Level: " . ucfirst($level));
        $this->line("   🔍 Mode: " . ($isDryRun ? 'DRY RUN (Preview Only)' : 'LIVE EXECUTION'));
        $this->line("   🔄 Replace Existing: " . ($replaceExisting ? 'Yes' : 'No'));

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE: No data will be saved to database');
        }

        $this->newLine();

        try {
            DB::beginTransaction();

            $seeder = new GeneralSubjectSeeder();
            $seeder->setCommand($this);
            $seeder->setOptions([
                'level' => $level,
                'dry_run' => $isDryRun,
                'replace_existing' => $replaceExisting
            ]);

            $seeder->run();

            if (!$isDryRun) {
                DB::commit();
                $this->newLine();
                $this->info('✅ Successfully seeded Somaliland curriculum subjects!');
            } else {
                DB::rollBack();
                $this->newLine();
                $this->info('🔍 DRY RUN completed - no changes made to database');
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error occurred: ' . $e->getMessage());
            $this->line('   Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        $this->newLine();
        $this->line('💡 Usage examples:');
        $this->line('   php artisan subjects:seed-general --level=primary --dry-run');
        $this->line('   php artisan subjects:seed-general --level=secondary');
        $this->line('   php artisan subjects:seed-general --level=all');

        return 0;
    }
}