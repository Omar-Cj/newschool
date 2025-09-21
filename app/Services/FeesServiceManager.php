<?php

namespace App\Services;

use App\Models\Fees\FeesGeneration;
use App\Models\Fees\FeesCollect;
use App\Models\StudentService;
use App\Services\FeesGenerationService;
use App\Services\EnhancedFeesGenerationService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;

class FeesServiceManager
{
    private FeesGenerationService $legacyService;
    private EnhancedFeesGenerationService $enhancedService;

    public function __construct(
        FeesGenerationService $legacyService,
        EnhancedFeesGenerationService $enhancedService
    ) {
        $this->legacyService = $legacyService;
        $this->enhancedService = $enhancedService;
    }

    /**
     * Get the active fee generation service based on configuration
     */
    public function getActiveService(): FeesGenerationService|EnhancedFeesGenerationService
    {
        // Check if enhanced fee system is enabled
        if ($this->isEnhancedSystemEnabled()) {
            return $this->enhancedService;
        }

        return $this->legacyService;
    }

    /**
     * Check if enhanced fee system is enabled
     */
    public function isEnhancedSystemEnabled(): bool
    {
        // Check setting from database or environment
        return setting('use_enhanced_fee_system', false) || 
               config('fees.use_enhanced_system', false);
    }

    /**
     * Switch to enhanced fee system
     */
    public function enableEnhancedSystem(): void
    {
        setting(['use_enhanced_fee_system' => true]);
        
        // Log the change
        \Log::info('Enhanced fee system enabled', [
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);
    }

    /**
     * Switch back to legacy fee system
     */
    public function enableLegacySystem(): void
    {
        setting(['use_enhanced_fee_system' => false]);
        
        // Log the change
        \Log::info('Legacy fee system enabled (rollback)', [
            'user_id' => auth()->id(),
            'timestamp' => now()
        ]);
    }

    /**
     * Generate fees using the active service
     */
    public function generateFees(array $data): FeesGeneration
    {
        return $this->getActiveService()->generateFees($data);
    }

    /**
     * Generate preview using the active service
     */
    public function generatePreview(array $filters): array
    {
        if ($this->isEnhancedSystemEnabled()) {
            return $this->enhancedService->generateServiceBasedPreview($filters);
        }
        
        return $this->legacyService->generatePreview($filters);
    }

    /**
     * Get system compatibility report
     */
    public function getSystemCompatibilityReport(): array
    {
        $report = [
            'current_system' => $this->isEnhancedSystemEnabled() ? 'enhanced' : 'legacy',
            'enhanced_system_ready' => $this->isEnhancedSystemReady(),
            'migration_ready' => $this->isEnhancedSystemReady(), // For frontend compatibility
            'migration_status' => $this->getMigrationStatus(),
            'data_compatibility' => $this->checkDataCompatibility(),
            'recommendations' => []
        ];

        // Add recommendations based on status
        if (!$report['enhanced_system_ready']) {
            $report['recommendations'][] = 'Run pending migrations before switching to enhanced system';
        }

        if ($report['data_compatibility']['has_legacy_data'] && !$report['data_compatibility']['is_migrated']) {
            $report['recommendations'][] = 'Migrate existing fee data before enabling enhanced system';
        }

        return $report;
    }

    /**
     * Check if enhanced system is ready for use
     */
    private function isEnhancedSystemReady(): bool
    {
        // Check if required tables exist
        $requiredTables = [
            'student_services',
            'academic_level_configs',
            'fee_system_migration_logs'
        ];

        foreach ($requiredTables as $table) {
            if (!\Schema::hasTable($table)) {
                return false;
            }
        }

        // Check if fees_types table has enhanced columns
        if (!\Schema::hasColumn('fees_types', 'academic_level') ||
            !\Schema::hasColumn('fees_types', 'category')) {
            return false;
        }

        return true;
    }

    /**
     * Get migration status for enhanced system
     */
    private function getMigrationStatus(): array
    {
        // This would check the actual migration status
        // For now, returning a basic structure
        return [
            'total_migrations' => 5,
            'completed_migrations' => 0, // This would be calculated
            'pending_migrations' => 5,   // This would be calculated
            'is_complete' => false
        ];
    }

    /**
     * Check data compatibility between systems
     */
    private function checkDataCompatibility(): array
    {
        return [
            'has_legacy_data' => \DB::table('fees_assigns')->exists(),
            'has_enhanced_data' => \Schema::hasTable('student_services') && 
                                 \DB::table('student_services')->exists(),
            'is_migrated' => \Schema::hasTable('fee_system_migration_logs') && 
                           \DB::table('fee_system_migration_logs')
                              ->where('status', 'completed')
                              ->exists()
        ];
    }

    /**
     * Validate system before switching
     */
    public function validateSystemSwitch(string $targetSystem): array
    {
        $errors = [];
        $warnings = [];

        if ($targetSystem === 'enhanced') {
            if (!$this->isEnhancedSystemReady()) {
                $errors[] = 'Enhanced system is not ready. Please run migrations first.';
            }

            $compatibility = $this->checkDataCompatibility();
            if ($compatibility['has_legacy_data'] && !$compatibility['is_migrated']) {
                $warnings[] = 'Legacy fee data exists but has not been migrated. Some data may not be visible in the enhanced system.';
            }
        }

        return [
            'is_valid' => empty($errors),
            'errors' => $errors,
            'warnings' => $warnings
        ];
    }

    /**
     * Get service usage statistics
     */
    public function getUsageStatistics(?int $branchId = null): array
    {
        $branchId ??= auth()->user()->branch_id ?? null;

        $hasStudentServiceBranch = Schema::hasColumn('student_services', 'branch_id');
        $hasFeesCollectBranch = Schema::hasColumn('fees_collects', 'branch_id');
        $hasFeesGenerationBranch = Schema::hasColumn('fees_generations', 'branch_id');

        $studentServiceQuery = StudentService::query()
            ->when($branchId, function ($query) use ($branchId, $hasStudentServiceBranch) {
                if ($hasStudentServiceBranch) {
                    $query->where('branch_id', $branchId);
                } else {
                    $query->whereHas('student', function ($studentQuery) use ($branchId) {
                        $studentQuery->where('branch_id', $branchId);
                    });
                }
            });

        $activeServices = (clone $studentServiceQuery)
            ->where('is_active', true)
            ->count();

        $studentsWithServices = (clone $studentServiceQuery)
            ->distinct('student_id')
            ->count('student_id');

        $totalServices = (clone $studentServiceQuery)->count();

        $feesGenerationQuery = FeesGeneration::query()
            ->when($branchId, function ($query) use ($branchId, $hasFeesGenerationBranch) {
                if ($hasFeesGenerationBranch) {
                    $query->where('branch_id', $branchId);
                } else {
                    $query->whereHas('feesCollects.student', function ($studentQuery) use ($branchId) {
                        $studentQuery->where('branch_id', $branchId);
                    });
                }
            });

        $totalGenerations = (clone $feesGenerationQuery)->count();

        $feesCollectQuery = FeesCollect::query()
            ->when($branchId, function ($query) use ($branchId, $hasFeesCollectBranch) {
                if ($hasFeesCollectBranch) {
                    $query->where('branch_id', $branchId);
                } else {
                    $query->whereHas('student', function ($studentQuery) use ($branchId) {
                        $studentQuery->where('branch_id', $branchId);
                    });
                }
            });

        $legacyCollections = (clone $feesCollectQuery)
            ->where(function ($query) {
                $query->whereNull('generation_method')
                    ->orWhere('generation_method', 'legacy');
            })
            ->count();

        $enhancedCollections = (clone $feesCollectQuery)
            ->whereIn('generation_method', ['service_based', 'automated'])
            ->count();

        return [
            'active_system' => $this->isEnhancedSystemEnabled() ? 'Enhanced' : 'Legacy',
            'students_with_services' => $studentsWithServices,
            'total_active_services' => $activeServices,
            'legacy_system' => [
                'total_generations' => $totalGenerations,
                'total_collections' => $legacyCollections,
            ],
            'enhanced_system' => [
                'total_services' => $totalServices,
                'total_collections' => $enhancedCollections,
            ],
        ];
    }
}
