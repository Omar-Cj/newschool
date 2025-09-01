<?php

namespace Database\Seeders\Academic;

use App\Models\Academic\Subject;
use App\Enums\SubjectType;
use Illuminate\Database\Seeder;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class GeneralSubjectSeeder extends Seeder
{
    protected ?Command $customCommand = null;
    private array $options = [];
    private int $createdCount = 0;
    private int $skippedCount = 0;
    private int $replacedCount = 0;

    /**
     * Authentic Somaliland Primary Curriculum (7 subjects)
     */
    private array $primarySubjects = [
        ['name' => 'English', 'code' => '101', 'type' => SubjectType::THEORY],
        ['name' => 'Arabic', 'code' => '102', 'type' => SubjectType::THEORY],
        ['name' => 'Islamic Studies', 'code' => '103', 'type' => SubjectType::THEORY],
        ['name' => 'Science', 'code' => '104', 'type' => SubjectType::THEORY],
        ['name' => 'Social Studies', 'code' => '105', 'type' => SubjectType::THEORY],
        ['name' => 'Mathematics', 'code' => '106', 'type' => SubjectType::THEORY],
        ['name' => 'Somali', 'code' => '107', 'type' => SubjectType::THEORY],
    ];

    /**
     * Authentic Somaliland Secondary Curriculum (10 subjects)
     */
    private array $secondarySubjects = [
        ['name' => 'English', 'code' => '201', 'type' => SubjectType::THEORY],
        ['name' => 'Mathematics', 'code' => '202', 'type' => SubjectType::THEORY],
        ['name' => 'Chemistry', 'code' => '203', 'type' => SubjectType::PRACTICAL],
        ['name' => 'Physics', 'code' => '204', 'type' => SubjectType::PRACTICAL],
        ['name' => 'Biology', 'code' => '205', 'type' => SubjectType::PRACTICAL],
        ['name' => 'Islamic Studies', 'code' => '206', 'type' => SubjectType::THEORY],
        ['name' => 'Somali', 'code' => '207', 'type' => SubjectType::THEORY],
        ['name' => 'History', 'code' => '208', 'type' => SubjectType::THEORY],
        ['name' => 'Geography', 'code' => '209', 'type' => SubjectType::THEORY],
        ['name' => 'Arabic', 'code' => '210', 'type' => SubjectType::THEORY],
    ];

    public function setCommand(Command $command): void
    {
        $this->customCommand = $command;
    }

    public function setOptions(array $options): void
    {
        $this->options = $options;
    }

    private function info(string $message): void
    {
        if ($this->customCommand) {
            $this->customCommand->info($message);
        }
    }

    private function line(string $message): void
    {
        if ($this->customCommand) {
            $this->customCommand->line($message);
        }
    }

    private function warn(string $message): void
    {
        if ($this->customCommand) {
            $this->customCommand->warn($message);
        }
    }

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $level = $this->options['level'] ?? 'all';
        $isDryRun = $this->options['dry_run'] ?? false;
        $replaceExisting = $this->options['replace_existing'] ?? false;

        // Determine which subjects to seed based on level
        $subjectsToSeed = [];
        
        switch ($level) {
            case 'primary':
                $subjectsToSeed = $this->primarySubjects;
                $this->info('📚 Seeding Primary Education Subjects (7 subjects)');
                break;
                
            case 'secondary':
                $subjectsToSeed = $this->secondarySubjects;
                $this->info('🎓 Seeding Secondary Education Subjects (10 subjects)');
                break;
                
            case 'all':
                $subjectsToSeed = array_merge($this->primarySubjects, $this->secondarySubjects);
                $this->info('🏫 Seeding All Education Levels (17 subjects total)');
                $this->line('   📚 Primary: 7 subjects');
                $this->line('   🎓 Secondary: 10 subjects');
                break;
        }

        $this->line('');

        // Check for existing subjects
        if (!$isDryRun) {
            $existingSubjects = Subject::whereIn('code', array_column($subjectsToSeed, 'code'))->get();
            if ($existingSubjects->isNotEmpty() && !$replaceExisting) {
                $this->warn('⚠️  Found ' . $existingSubjects->count() . ' existing subjects with matching codes:');
                foreach ($existingSubjects as $subject) {
                    $this->line("   - {$subject->name} (Code: {$subject->code})");
                }
                $this->line('   Use --replace-existing=true to replace them');
                $this->line('');
            }
        }

        // Seed subjects
        $this->seedSubjects($subjectsToSeed, $isDryRun, $replaceExisting);

        // Summary
        if ($isDryRun) {
            $this->info("🔍 DRY RUN SUMMARY:");
            $this->line("   📝 Would create: {$this->createdCount} subjects");
            $this->line("   ⏭️  Would skip: {$this->skippedCount} existing subjects");
            if ($replaceExisting) {
                $this->line("   🔄 Would replace: {$this->replacedCount} subjects");
            }
        } else {
            $this->info("✅ SEEDING SUMMARY:");
            $this->line("   📝 Created: {$this->createdCount} subjects");
            $this->line("   ⏭️  Skipped: {$this->skippedCount} existing subjects");
            $this->line("   🔄 Replaced: {$this->replacedCount} subjects");
        }
    }

    private function seedSubjects(array $subjects, bool $isDryRun, bool $replaceExisting): void
    {
        foreach ($subjects as $subjectData) {
            $existingSubject = null;
            
            if (!$isDryRun) {
                $existingSubject = Subject::where('code', $subjectData['code'])->first();
            }

            if ($existingSubject && !$replaceExisting) {
                $this->line("   ⏭️  Skipped: {$subjectData['name']} (Code: {$subjectData['code']}) - already exists");
                $this->skippedCount++;
                continue;
            }

            if ($isDryRun) {
                $typeText = $subjectData['type'] === SubjectType::THEORY ? 'Theory' : 'Practical';
                $this->line("   📝 Would create: {$subjectData['name']} (Code: {$subjectData['code']}, Type: {$typeText})");
                $this->createdCount++;
                continue;
            }

            // Create or replace subject
            if ($existingSubject && $replaceExisting) {
                $existingSubject->update([
                    'name' => $subjectData['name'],
                    'type' => $subjectData['type'],
                    'status' => 1
                ]);
                $typeText = $subjectData['type'] === SubjectType::THEORY ? 'Theory' : 'Practical';
                $this->line("   🔄 Replaced: {$subjectData['name']} (Code: {$subjectData['code']}, Type: {$typeText})");
                $this->replacedCount++;
            } else {
                Subject::create([
                    'name' => $subjectData['name'],
                    'code' => $subjectData['code'],
                    'type' => $subjectData['type'],
                    'status' => 1
                ]);
                $typeText = $subjectData['type'] === SubjectType::THEORY ? 'Theory' : 'Practical';
                $this->line("   ✅ Created: {$subjectData['name']} (Code: {$subjectData['code']}, Type: {$typeText})");
                $this->createdCount++;
            }
        }
    }
}