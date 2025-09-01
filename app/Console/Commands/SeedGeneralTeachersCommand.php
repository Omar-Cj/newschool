<?php

namespace App\Console\Commands;

use Database\Seeders\Staff\GeneralTeacherSeeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use App\Models\Staff\Department;
use App\Models\Staff\Designation;
use App\Models\Gender;
use App\Models\Role;

class SeedGeneralTeachersCommand extends Command
{
    protected $signature = 'teachers:seed-general 
                          {--count= : Number of teachers to generate (defaults to level-based count)}
                          {--level=all : Educational level (all, primary, secondary, admin)}
                          {--with-specializations=true : Create subject-specialized teachers}
                          {--branch=1 : Branch ID to assign teachers}
                          {--dry-run : Preview what will be created without saving}
                          {--replace-existing=false : Replace existing teachers with same staff IDs}';

    protected $description = 'Seed authentic Somaliland curriculum teachers for primary, secondary, and administrative positions';

    public function handle()
    {
        $level = $this->option('level');
        $count = $this->option('count') ? (int) $this->option('count') : null;
        $branchId = (int) $this->option('branch');
        $isDryRun = $this->option('dry-run');
        $replaceExisting = $this->option('replace-existing') === 'true';
        $withSpecializations = $this->option('with-specializations') !== 'false';

        // Validate level option
        if (!in_array($level, ['all', 'primary', 'secondary', 'admin'])) {
            $this->error('Invalid level. Use: all, primary, secondary, or admin');
            return 1;
        }

        $this->info('🇸🇴 Seeding Authentic Somaliland Curriculum Teachers');
        $this->line("   📚 Level: " . ucfirst($level));
        if ($count) {
            $this->line("   📊 Count: {$count} teachers");
        }
        $this->line("   🏢 Branch ID: {$branchId}");
        $this->line("   🔍 Mode: " . ($isDryRun ? 'DRY RUN (Preview Only)' : 'LIVE EXECUTION'));
        $this->line("   🔄 Replace Existing: " . ($replaceExisting ? 'Yes' : 'No'));
        $this->line("   🎯 Subject Specializations: " . ($withSpecializations ? 'Enabled' : 'Disabled'));

        if ($isDryRun) {
            $this->warn('⚠️  DRY RUN MODE: No data will be saved to database');
        }

        $this->newLine();

        // Validate prerequisites
        if (!$this->validatePrerequisites()) {
            return 1;
        }

        $this->newLine();

        try {
            DB::beginTransaction();

            $seeder = new GeneralTeacherSeeder();
            $seeder->setCommand($this);
            $seeder->setOptions([
                'level' => $level,
                'count' => $count,
                'branch_id' => $branchId,
                'dry_run' => $isDryRun,
                'replace_existing' => $replaceExisting,
                'with_specializations' => $withSpecializations
            ]);

            $seeder->run();

            if (!$isDryRun) {
                DB::commit();
                $this->newLine();
                $this->info('✅ Successfully seeded Somaliland curriculum teachers!');
                $this->displaySuccessGuidance();
            } else {
                DB::rollBack();
                $this->newLine();
                $this->info('🔍 DRY RUN completed - no changes made to database');
                $this->displayDryRunGuidance();
            }

        } catch (\Exception $e) {
            DB::rollBack();
            $this->error('❌ Error occurred: ' . $e->getMessage());
            $this->line('   Stack trace: ' . $e->getTraceAsString());
            return 1;
        }

        $this->displayUsageExamples();
        return 0;
    }

    private function validatePrerequisites(): bool
    {
        $this->info('🔍 Validating system prerequisites...');
        
        // Check if required reference data exists
        if (Gender::count() === 0) {
            $this->error('❌ No gender records found. Please run: php artisan db:seed --class=GenderSeeder');
            return false;
        }
        $this->line('   ✓ Gender reference data available');

        if (Role::count() === 0) {
            $this->error('❌ No role records found. Please run: php artisan db:seed --class=RoleSeeder');
            return false;
        }
        $this->line('   ✓ Role reference data available');

        // Check if Teacher role exists (role_id = 5)
        $teacherRole = Role::find(5);
        if (!$teacherRole) {
            $this->error('❌ Teacher role (ID: 5) not found. Please ensure RoleSeeder has been run.');
            return false;
        }
        $this->line("   ✓ Teacher role available: {$teacherRole->name}");

        // Check basic department and designation setup
        $departmentCount = Department::count();
        $this->line("   ✓ Found {$departmentCount} departments (will create subject-specific ones as needed)");

        $designationCount = Designation::count();
        $this->line("   ✓ Found {$designationCount} designations (will create teaching roles as needed)");

        $this->info('✅ All prerequisites validated successfully');
        return true;
    }

    private function displaySuccessGuidance(): void
    {
        $this->info('🎯 Next Steps for End-to-End Testing:');
        $this->line('');
        $this->line('1. 📚 Ensure subjects are seeded:');
        $this->line('   php artisan subjects:seed-general --level=all');
        $this->line('');
        $this->line('2. 👥 Generate student data:');
        $this->line('   php artisan students:seed-somaliland --count=100 --with-parents=true');
        $this->line('');
        $this->line('3. 🔗 Create subject assignments to link teachers with subjects and classes');
        $this->line('');
        $this->line('4. 🧪 Test teacher functionality:');
        $this->line('   - Teacher login with email/password: 123456');
        $this->line('   - View assigned subjects and classes');
        $this->line('   - Manage student attendance and grades');
        $this->line('');
        $this->info('💡 All teachers have specialized subject expertise matching the curriculum!');
    }

    private function displayDryRunGuidance(): void
    {
        $this->info('💡 To execute the seeding (without dry-run):');
        $command = 'php artisan teachers:seed-general';
        
        if ($this->option('level') !== 'all') {
            $command .= ' --level=' . $this->option('level');
        }
        if ($this->option('count')) {
            $command .= ' --count=' . $this->option('count');
        }
        if ($this->option('branch') !== 1) {
            $command .= ' --branch=' . $this->option('branch');
        }
        if ($this->option('replace-existing') === 'true') {
            $command .= ' --replace-existing=true';
        }
        
        $this->line($command);
    }

    private function displayUsageExamples(): void
    {
        $this->newLine();
        $this->line('💡 Usage examples:');
        $this->line('   # Preview all teachers');
        $this->line('   php artisan teachers:seed-general --dry-run');
        $this->line('');
        $this->line('   # Seed only primary level teachers');
        $this->line('   php artisan teachers:seed-general --level=primary');
        $this->line('');
        $this->line('   # Seed first 10 teachers only');
        $this->line('   php artisan teachers:seed-general --count=10');
        $this->line('');
        $this->line('   # Replace existing teachers');
        $this->line('   php artisan teachers:seed-general --replace-existing=true');
        $this->line('');
        $this->line('   # Seed for specific branch');
        $this->line('   php artisan teachers:seed-general --branch=2');
        $this->line('');
        $this->line('📧 Default login credentials:');
        $this->line('   Email: [teacher_email] | Password: 123456');
        $this->line('');
        $this->info('🇸🇴 All data uses authentic Somaliland demographics and curriculum alignment!');
    }
}