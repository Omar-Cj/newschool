<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // This migration converts existing fee data to the new service-based structure
        
        DB::transaction(function () {
            // Step 0: Pre-migration validation and analysis
            $this->validateAndAnalyzeLegacyData();
            
            // Step 1: Populate enhanced fees_types columns based on existing patterns
            $this->enhanceExistingFeeTypes();
            
            // Step 2: Generate student service subscriptions from existing assignments
            $this->generateStudentServiceSubscriptions();
            
            // Step 3: Update fees_collects to use new structure (add fee_type_id)
            $this->updateFeesCollectStructure();
            
            // Step 4: Migrate existing discounts to new service structure
            $this->migrateExistingDiscounts();
            
            // Step 5: Create migration log for audit trail
            $this->createMigrationLog();
        });
    }

    /**
     * Validate and analyze legacy data before migration
     */
    private function validateAndAnalyzeLegacyData(): void
    {
        \Log::info('Starting pre-migration validation and analysis');
        
        // Count total assignments
        $totalAssignments = DB::table('fees_assign_childrens as fac')
            ->join('fees_assigns as fa', 'fac.fees_assign_id', '=', 'fa.id')
            ->join('students as s', 'fac.student_id', '=', 's.id')
            ->where('s.status', 1)
            ->count();
            
        // Count unique combinations (what we'll actually create)
        $uniqueCombinations = DB::table(DB::raw('(
            SELECT fac.student_id, fm.fees_type_id, fa.session_id
            FROM fees_assign_childrens as fac
            INNER JOIN fees_assigns as fa ON fac.fees_assign_id = fa.id
            INNER JOIN fees_masters as fm ON fac.fees_master_id = fm.id
            INNER JOIN students as s ON fac.student_id = s.id
            WHERE s.status = 1
            GROUP BY fac.student_id, fm.fees_type_id, fa.session_id
        ) as unique_combinations'))
        ->count();
            
        // Identify duplicate patterns
        $duplicateAnalysis = DB::table('fees_assign_childrens as fac')
            ->join('fees_assigns as fa', 'fac.fees_assign_id', '=', 'fa.id')
            ->join('fees_masters as fm', 'fac.fees_master_id', '=', 'fm.id')
            ->join('students as s', 'fac.student_id', '=', 's.id')
            ->where('s.status', 1)
            ->select([
                'fac.student_id',
                'fm.fees_type_id', 
                'fa.session_id',
                DB::raw('COUNT(*) as duplicate_count')
            ])
            ->groupBy('fac.student_id', 'fm.fees_type_id', 'fa.session_id')
            ->having('duplicate_count', '>', 1)
            ->get();
            
        // Check for existing student services that would conflict
        $existingServices = 0;
        if (Schema::hasTable('student_services')) {
            $existingServices = DB::table('student_services')->count();
        }
        
        $duplicateCount = $duplicateAnalysis->sum('duplicate_count') - $duplicateAnalysis->count();
        $consolidationRate = $totalAssignments > 0 ? 
            round(($duplicateCount / $totalAssignments) * 100, 2) : 0;
            
        // Log detailed analysis
        $analysisReport = [
            'total_fee_assignments' => $totalAssignments,
            'unique_combinations_to_create' => $uniqueCombinations,
            'duplicate_assignments_found' => $duplicateCount,
            'consolidation_rate_percentage' => $consolidationRate,
            'existing_student_services' => $existingServices,
            'most_duplicated_combinations' => $duplicateAnalysis->sortByDesc('duplicate_count')->take(5)->values()->toArray()
        ];
        
        \Log::info('Pre-migration analysis completed', $analysisReport);
        
        // Validate data integrity
        $issues = [];
        
        // Check for assignments without valid students
        $orphanedAssignments = DB::table('fees_assign_childrens as fac')
            ->leftJoin('students as s', 'fac.student_id', '=', 's.id')
            ->whereNull('s.id')
            ->count();
            
        if ($orphanedAssignments > 0) {
            $issues[] = "Found {$orphanedAssignments} fee assignments with invalid student references";
        }
        
        // Check for assignments without valid fee types
        $invalidFeeTypeAssignments = DB::table('fees_assign_childrens as fac')
            ->join('fees_masters as fm', 'fac.fees_master_id', '=', 'fm.id')
            ->leftJoin('fees_types as ft', 'fm.fees_type_id', '=', 'ft.id')
            ->whereNull('ft.id')
            ->count();
            
        if ($invalidFeeTypeAssignments > 0) {
            $issues[] = "Found {$invalidFeeTypeAssignments} fee assignments with invalid fee type references";
        }
        
        // Check for assignments without valid sessions
        $invalidSessionAssignments = DB::table('fees_assign_childrens as fac')
            ->join('fees_assigns as fa', 'fac.fees_assign_id', '=', 'fa.id')
            ->leftJoin('sessions as sess', 'fa.session_id', '=', 'sess.id')
            ->whereNull('sess.id')
            ->count();
            
        if ($invalidSessionAssignments > 0) {
            $issues[] = "Found {$invalidSessionAssignments} fee assignments with invalid session references";
        }
        
        if (!empty($issues)) {
            \Log::warning('Data integrity issues found during pre-migration validation', [
                'issues' => $issues,
                'recommendation' => 'Consider cleaning up data before migration'
            ]);
            
            // Don't fail the migration, but log warnings
            foreach ($issues as $issue) {
                \Log::warning("Data integrity warning: {$issue}");
            }
        }
        
        // Save analysis to migration log
        if (Schema::hasTable('fee_system_migration_logs')) {
            DB::table('fee_system_migration_logs')->insertOrIgnore([
                'migration_name' => 'pre_migration_analysis',
                'status' => 'completed',
                'migration_details' => json_encode($analysisReport),
                'notes' => !empty($issues) ? implode('; ', $issues) : 'No data integrity issues found',
                'migration_date' => now(),
                'created_at' => now(),
                'updated_at' => now()
            ]);
        }
    }

    /**
     * Enhance existing fee types with academic level and other new fields
     */
    private function enhanceExistingFeeTypes(): void
    {
        // Get all existing fee types
        $feeTypes = DB::table('fees_types')->get();
        
        foreach ($feeTypes as $feeType) {
            // Determine academic level based on fee type name patterns
            $academicLevel = $this->determineAcademicLevelFromName($feeType->name);
            
            // Determine category from fee type name
            $category = $this->determineCategoryFromName($feeType->name);
            
            // Get average amount from fee masters for this type
            $averageAmount = $this->getAverageAmountForFeeType($feeType->id);
            
            // Update the fee type with new fields
            DB::table('fees_types')
                ->where('id', $feeType->id)
                ->update([
                    'academic_level' => $academicLevel,
                    'amount' => $averageAmount,
                    'due_date_offset' => 30, // Default 30 days
                    'is_mandatory_for_level' => $this->isMandatoryService($feeType->name),
                    'category' => $category,
                    'updated_at' => now()
                ]);
        }
    }

    /**
     * Generate student service subscriptions from existing fee assignments
     */
    private function generateStudentServiceSubscriptions(): void
    {
        // Get all active fee assignments with consolidation to handle duplicates
        $assignments = DB::table('fees_assign_childrens as fac')
            ->join('fees_assigns as fa', 'fac.fees_assign_id', '=', 'fa.id')
            ->join('fees_masters as fm', 'fac.fees_master_id', '=', 'fm.id')
            ->join('students as s', 'fac.student_id', '=', 's.id')
            ->where('s.status', 1) // Active students only
            ->select([
                'fac.student_id',
                'fm.fees_type_id',
                'fa.session_id as academic_year_id',
                DB::raw('MAX(fm.amount) as amount'), // Use max amount if multiple
                DB::raw('MAX(fm.due_date) as due_date'), // Use latest due date
                DB::raw('MAX(fac.created_at) as created_at'), // Use latest creation date
                DB::raw('COUNT(*) as assignment_count') // Track how many assignments were consolidated
            ])
            ->groupBy('fac.student_id', 'fm.fees_type_id', 'fa.session_id')
            ->get();

        // Track consolidation statistics
        $totalOriginalAssignments = DB::table('fees_assign_childrens as fac')
            ->join('fees_assigns as fa', 'fac.fees_assign_id', '=', 'fa.id')
            ->join('students as s', 'fac.student_id', '=', 's.id')
            ->where('s.status', 1)
            ->count();

        $consolidatedCount = 0;
        $subscriptionsToInsert = [];
        $processedKeys = []; // Track processed combinations to prevent duplicates within batch
        
        foreach ($assignments as $assignment) {
            // Create unique key for this combination
            $uniqueKey = $assignment->student_id . '-' . $assignment->fees_type_id . '-' . $assignment->academic_year_id;
            
            // Skip if already processed in this batch (extra safety)
            if (isset($processedKeys[$uniqueKey])) {
                continue;
            }
            
            // Check if subscription already exists in database
            $existing = DB::table('student_services')
                ->where('student_id', $assignment->student_id)
                ->where('fee_type_id', $assignment->fees_type_id)
                ->where('academic_year_id', $assignment->academic_year_id)
                ->exists();
                
            if (!$existing) {
                // Build notes with consolidation info
                $notes = 'Migrated from existing fee assignment';
                if ($assignment->assignment_count > 1) {
                    $notes .= " (consolidated {$assignment->assignment_count} duplicate assignments)";
                    $consolidatedCount += $assignment->assignment_count - 1;
                }
                
                $subscriptionsToInsert[] = [
                    'student_id' => $assignment->student_id,
                    'fee_type_id' => $assignment->fees_type_id,
                    'academic_year_id' => $assignment->academic_year_id,
                    'amount' => $assignment->amount ?? 0,
                    'due_date' => $assignment->due_date ?? now()->addDays(30),
                    'discount_type' => 'none',
                    'discount_value' => 0,
                    'final_amount' => $assignment->amount ?? 0,
                    'subscription_date' => $assignment->created_at ?? now(),
                    'is_active' => true,
                    'notes' => $notes,
                    'created_by' => 1, // System migration
                    'created_at' => now(),
                    'updated_at' => now()
                ];
                
                // Mark this combination as processed
                $processedKeys[$uniqueKey] = true;
                
                // Insert in batches of 500 to avoid memory issues (reduced from 1000 for safety)
                if (count($subscriptionsToInsert) >= 500) {
                    try {
                        DB::table('student_services')->insert($subscriptionsToInsert);
                    } catch (\Exception $e) {
                        // Enhanced error handling with more details
                        \Log::error('Batch insert failed during migration', [
                            'batch_size' => count($subscriptionsToInsert),
                            'error' => $e->getMessage(),
                            'sample_records' => array_slice($subscriptionsToInsert, 0, 3)
                        ]);
                        throw $e;
                    }
                    $subscriptionsToInsert = [];
                }
            }
        }
        
        // Insert remaining subscriptions
        if (!empty($subscriptionsToInsert)) {
            try {
                DB::table('student_services')->insert($subscriptionsToInsert);
            } catch (\Exception $e) {
                \Log::error('Final batch insert failed during migration', [
                    'batch_size' => count($subscriptionsToInsert),
                    'error' => $e->getMessage()
                ]);
                throw $e;
            }
        }
        
        // Log migration statistics
        \Log::info('Student service subscription migration completed', [
            'total_original_assignments' => $totalOriginalAssignments,
            'unique_subscriptions_created' => $assignments->count(),
            'duplicate_assignments_consolidated' => $consolidatedCount,
            'consolidation_percentage' => $totalOriginalAssignments > 0 ? 
                round(($consolidatedCount / $totalOriginalAssignments) * 100, 2) : 0
        ]);
    }

    /**
     * Update fees_collects structure to support new fee_type_id
     */
    private function updateFeesCollectStructure(): void
    {
        // Track which columns are being added for logging
        $columnsAdded = [];
        
        // Add columns individually with proper existence checks
        Schema::table('fees_collects', function (Blueprint $table) use (&$columnsAdded) {
            
            if (!Schema::hasColumn('fees_collects', 'fee_type_id')) {
                $table->foreignId('fee_type_id')
                      ->nullable()
                      ->after('fees_assign_children_id')
                      ->constrained('fees_types')
                      ->nullOnDelete()
                      ->comment('Direct reference to fee type (new structure)');
                $columnsAdded[] = 'fee_type_id';
            }
            
            if (!Schema::hasColumn('fees_collects', 'academic_year_id')) {
                $table->foreignId('academic_year_id')
                      ->nullable() 
                      ->after('session_id')
                      ->constrained('sessions')
                      ->nullOnDelete()
                      ->comment('Academic year for this fee');
                $columnsAdded[] = 'academic_year_id';
            }
            
            if (!Schema::hasColumn('fees_collects', 'generation_method')) {
                $table->string('generation_method')
                      ->nullable()
                      ->after('fine_amount')
                      ->comment('How this fee was generated: bulk, manual, automated, service_based');
                $columnsAdded[] = 'generation_method';
            }
            
            if (!Schema::hasColumn('fees_collects', 'generation_batch_id')) {
                $table->string('generation_batch_id')
                      ->nullable()
                      ->after('generation_method')
                      ->comment('Batch ID for bulk generated fees');
                $columnsAdded[] = 'generation_batch_id';
            }
            
            if (!Schema::hasColumn('fees_collects', 'late_fee_applied')) {
                $table->decimal('late_fee_applied', 16, 2)
                      ->default(0)
                      ->after('fine_amount')
                      ->comment('Late fee amount applied');
                $columnsAdded[] = 'late_fee_applied';
            }
            
            if (!Schema::hasColumn('fees_collects', 'discount_applied')) {
                $table->decimal('discount_applied', 16, 2)
                      ->default(0)
                      ->after('late_fee_applied')
                      ->comment('Total discount applied to this fee');
                $columnsAdded[] = 'discount_applied';
            }
            
            if (!Schema::hasColumn('fees_collects', 'discount_notes')) {
                $table->text('discount_notes')
                      ->nullable()
                      ->after('discount_applied')
                      ->comment('Notes about discount applied');
                $columnsAdded[] = 'discount_notes';
            }
        });
        
        // Log which columns were added vs skipped
        $existingColumns = array_filter([
            'fee_type_id' => Schema::hasColumn('fees_collects', 'fee_type_id'),
            'academic_year_id' => Schema::hasColumn('fees_collects', 'academic_year_id'),
            'generation_method' => Schema::hasColumn('fees_collects', 'generation_method'),
            'generation_batch_id' => Schema::hasColumn('fees_collects', 'generation_batch_id'),
            'late_fee_applied' => Schema::hasColumn('fees_collects', 'late_fee_applied'),
            'discount_applied' => Schema::hasColumn('fees_collects', 'discount_applied'),
            'discount_notes' => Schema::hasColumn('fees_collects', 'discount_notes'),
        ]);
        
        \Log::info('fees_collects table structure update completed', [
            'columns_added' => $columnsAdded,
            'columns_skipped' => array_keys($existingColumns),
            'migration_is_idempotent' => count($columnsAdded) > 0 ? 'partial' : 'complete'
        ]);

        // Populate fee_type_id for existing records (only if columns exist and records need updating)
        if (Schema::hasColumn('fees_collects', 'fee_type_id') && 
            Schema::hasColumn('fees_collects', 'academic_year_id') && 
            Schema::hasColumn('fees_collects', 'generation_method')) {
            
            // Count records that need fee_type_id updating (preserve existing generation_method)
            $recordsToUpdate = DB::table('fees_collects as fc')
                ->join('fees_assign_childrens as fac', 'fc.fees_assign_children_id', '=', 'fac.id')
                ->join('fees_masters as fm', 'fac.fees_master_id', '=', 'fm.id')
                ->whereNull('fc.fee_type_id')
                ->count();
            
            // Count records with no generation_method that need 'legacy' assignment    
            $recordsNeedingLegacyMethod = DB::table('fees_collects as fc')
                ->join('fees_assign_childrens as fac', 'fc.fees_assign_children_id', '=', 'fac.id')
                ->join('fees_masters as fm', 'fac.fees_master_id', '=', 'fm.id')
                ->whereNull('fc.fee_type_id')
                ->where(function($query) {
                    $query->whereNull('fc.generation_method')
                          ->orWhere('fc.generation_method', '=', '');
                })
                ->count();
                
            if ($recordsToUpdate > 0) {
                // Update fee_type_id and academic_year_id for all records that need it
                $updatedRecords = DB::update("
                    UPDATE fees_collects fc
                    JOIN fees_assign_childrens fac ON fc.fees_assign_children_id = fac.id
                    JOIN fees_masters fm ON fac.fees_master_id = fm.id
                    SET 
                        fc.fee_type_id = fm.fees_type_id,
                        fc.academic_year_id = fc.session_id,
                        fc.updated_at = NOW()
                    WHERE fc.fee_type_id IS NULL
                ");
                
                // Separately update generation_method ONLY for records that don't have it set
                $legacyMethodUpdated = 0;
                if ($recordsNeedingLegacyMethod > 0) {
                    $legacyMethodUpdated = DB::update("
                        UPDATE fees_collects fc
                        JOIN fees_assign_childrens fac ON fc.fees_assign_children_id = fac.id
                        JOIN fees_masters fm ON fac.fees_master_id = fm.id
                        SET 
                            fc.generation_method = 'legacy',
                            fc.updated_at = NOW()
                        WHERE fc.fee_type_id IS NOT NULL
                          AND (fc.generation_method IS NULL OR fc.generation_method = '')
                    ");
                }
                
                // Count records with existing generation_method values that were preserved
                $preservedRecords = $recordsToUpdate - $recordsNeedingLegacyMethod;
                
                \Log::info('fees_collects legacy data migration completed', [
                    'total_records_identified' => $recordsToUpdate,
                    'records_updated_fee_type_id' => $updatedRecords,
                    'records_needing_legacy_method' => $recordsNeedingLegacyMethod,
                    'records_updated_generation_method' => $legacyMethodUpdated,
                    'records_with_preserved_generation_method' => $preservedRecords,
                    'migration_method' => 'selective_legacy_data_population'
                ]);
            } else {
                \Log::info('fees_collects legacy data migration skipped - no records need updating');
            }
        } else {
            \Log::warning('fees_collects legacy data migration skipped - required columns missing');
        }
    }

    /**
     * Migrate existing discount systems to new service structure
     */
    private function migrateExistingDiscounts(): void
    {
        // Migrate AssignFeesDiscount records
        $assignDiscounts = DB::table('assign_fees_discounts as afd')
            ->join('fees_assign_childrens as fac', 'afd.fees_assign_children_id', '=', 'fac.id')
            ->join('fees_masters as fm', 'fac.fees_master_id', '=', 'fm.id')
            ->join('fees_assigns as fa', 'fac.fees_assign_id', '=', 'fa.id')
            ->select([
                'fac.student_id',
                'fm.fees_type_id', 
                'fa.session_id as academic_year_id',
                'afd.*'
            ])
            ->get();

        foreach ($assignDiscounts as $discount) {
            // Find corresponding student service
            DB::table('student_services')
                ->where('student_id', $discount->student_id)
                ->where('fee_type_id', $discount->fees_type_id)
                ->where('academic_year_id', $discount->academic_year_id)
                ->update([
                    'discount_type' => $this->convertDiscountType($discount),
                    'discount_value' => $this->convertDiscountValue($discount),
                    'final_amount' => DB::raw('amount - ' . ($this->convertDiscountValue($discount) ?? 0)),
                    'notes' => 'Migrated from assign fees discount',
                    'updated_at' => now()
                ]);
        }

        // Migrate SiblingFeesDiscount records (if they exist)
        if (Schema::hasTable('sibling_fees_discounts')) {
            $siblingDiscounts = DB::table('sibling_fees_discounts')->get();
            
            foreach ($siblingDiscounts as $discount) {
                // Apply sibling discount to all services for this student
                DB::table('student_services')
                    ->where('student_id', $discount->student_id)
                    ->where('academic_year_id', $discount->academic_year_id ?? session('session'))
                    ->update([
                        'discount_type' => 'percentage',
                        'discount_value' => $discount->discount_percentage ?? 0,
                        'final_amount' => DB::raw('amount * (1 - (' . ($discount->discount_percentage ?? 0) . ' / 100))'),
                        'notes' => 'Sibling discount - ' . ($discount->discount_percentage ?? 0) . '%',
                        'updated_at' => now()
                    ]);
            }
        }
    }

    /**
     * Create migration log for audit trail
     */
    private function createMigrationLog(): void
    {
        // Gather comprehensive statistics
        $feeTypesCount = DB::table('fees_types')->count();
        $studentServicesCount = DB::table('student_services')->count();
        $feesCollectsUpdated = DB::table('fees_collects')->whereNotNull('fee_type_id')->count();
        
        // Count discount migrations
        $discountsMigrated = DB::table('student_services')
            ->where('discount_type', '!=', 'none')
            ->count();
            
        // Get consolidation statistics from logs
        $consolidationStats = \Log::getHandlers()[0] ?? null;
        
        $migrationDetails = [
            'enhanced_fees_types' => $feeTypesCount,
            'student_services_created' => $studentServicesCount,
            'fees_collects_enhanced' => $feesCollectsUpdated,
            'discounts_migrated' => $discountsMigrated,
            'migration_completed_at' => now()->toISOString(),
            'migration_method' => 'automated_with_consolidation',
            'data_safety_measures' => [
                'duplicate_consolidation_enabled' => true,
                'pre_migration_validation_performed' => true,
                'batch_processing_used' => true,
                'transaction_wrapped' => true
            ]
        ];
        
        DB::table('fee_system_migration_logs')->insertOrIgnore([
            'migration_name' => 'migrate_existing_fee_data_to_service_structure',
            'status' => 'completed',
            'migration_details' => json_encode($migrationDetails),
            'fee_types_migrated' => $feeTypesCount,
            'student_services_created' => $studentServicesCount,
            'fees_collects_updated' => $feesCollectsUpdated,
            'discounts_migrated' => $discountsMigrated,
            'notes' => 'Migration completed successfully with duplicate consolidation and data validation',
            'migration_date' => now(),
            'created_at' => now(),
            'updated_at' => now()
        ]);
        
        \Log::info('Migration completed successfully', $migrationDetails);
    }

    /**
     * Determine academic level from fee type name
     */
    private function determineAcademicLevelFromName(string $name): string
    {
        $lowerName = strtolower($name);
        
        if (preg_match('/kg|kindergarten|nursery|pre-?school/', $lowerName)) {
            return 'kg';
        }
        
        if (preg_match('/primary|elementary|class\s*[1-5]|grade\s*[1-5]/', $lowerName)) {
            return 'primary';
        }
        
        if (preg_match('/secondary|middle|class\s*([6-9]|10)|grade\s*([6-9]|10)/', $lowerName)) {
            return 'secondary';
        }
        
        if (preg_match('/high|senior|class\s*(11|12)|grade\s*(11|12)/', $lowerName)) {
            return 'high_school';
        }
        
        return 'all'; // Default fallback
    }

    /**
     * Determine category from fee type name
     */
    private function determineCategoryFromName(string $name): string
    {
        $lowerName = strtolower($name);
        
        if (preg_match('/tuition|academic|examination|admission/', $lowerName)) {
            return 'academic';
        }
        
        if (preg_match('/transport|bus|van|vehicle/', $lowerName)) {
            return 'transport';
        }
        
        if (preg_match('/meal|lunch|food|canteen/', $lowerName)) {
            return 'meal';
        }
        
        if (preg_match('/hostel|accommodation|boarding/', $lowerName)) {
            return 'accommodation';
        }
        
        if (preg_match('/sports|activity|club|extra/', $lowerName)) {
            return 'activity';
        }
        
        return 'other';
    }

    /**
     * Get average amount for a fee type from fee masters
     */
    private function getAverageAmountForFeeType(int $feeTypeId): float
    {
        $avgAmount = DB::table('fees_masters')
            ->where('fees_type_id', $feeTypeId)
            ->avg('amount');
            
        return $avgAmount ?? 0;
    }

    /**
     * Determine if a service should be mandatory based on name
     */
    private function isMandatoryService(string $name): bool
    {
        $lowerName = strtolower($name);
        
        // Tuition and academic fees are typically mandatory
        return preg_match('/tuition|academic|admission|examination/', $lowerName);
    }

    /**
     * Convert legacy discount type to new format
     */
    private function convertDiscountType($discount): string
    {
        // This is a placeholder - adjust based on your existing discount structure
        if (isset($discount->discount_percentage) && $discount->discount_percentage > 0) {
            return 'percentage';
        }
        
        if (isset($discount->discount_amount) && $discount->discount_amount > 0) {
            return 'fixed';
        }
        
        return 'none';
    }

    /**
     * Convert legacy discount value to new format
     */
    private function convertDiscountValue($discount): float
    {
        if (isset($discount->discount_percentage) && $discount->discount_percentage > 0) {
            return $discount->discount_percentage;
        }
        
        if (isset($discount->discount_amount) && $discount->discount_amount > 0) {
            return $discount->discount_amount;
        }
        
        return 0;
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        // This is a data migration - reversal should be done carefully
        // In production, you might want to create a separate rollback script
        
        \Log::warning('Starting rollback of fee system migration - this will remove enhanced fee system data');
        
        DB::transaction(function () {
            $rollbackStats = [];
            
            // Remove added columns from fees_collects individually (safer than bulk drop)
            Schema::table('fees_collects', function (Blueprint $table) use (&$rollbackStats) {
                
                // Drop foreign keys first (only if they exist)
                if (Schema::hasColumn('fees_collects', 'fee_type_id')) {
                    try {
                        $table->dropForeign(['fee_type_id']);
                        $rollbackStats['dropped_foreign_keys'][] = 'fee_type_id';
                    } catch (\Exception $e) {
                        \Log::warning('Failed to drop fee_type_id foreign key: ' . $e->getMessage());
                    }
                }
                
                if (Schema::hasColumn('fees_collects', 'academic_year_id')) {
                    try {
                        $table->dropForeign(['academic_year_id']);
                        $rollbackStats['dropped_foreign_keys'][] = 'academic_year_id';
                    } catch (\Exception $e) {
                        \Log::warning('Failed to drop academic_year_id foreign key: ' . $e->getMessage());
                    }
                }
                
                // Drop columns individually
                $columnsToCheck = [
                    'fee_type_id', 'academic_year_id', 'generation_method',
                    'generation_batch_id', 'late_fee_applied', 'discount_applied', 'discount_notes'
                ];
                
                foreach ($columnsToCheck as $column) {
                    if (Schema::hasColumn('fees_collects', $column)) {
                        $table->dropColumn($column);
                        $rollbackStats['dropped_columns'][] = $column;
                    }
                }
            });
            
            // Clear student services (they can be regenerated)
            if (Schema::hasTable('student_services')) {
                $servicesCount = DB::table('student_services')->count();
                DB::table('student_services')->truncate();
                $rollbackStats['student_services_cleared'] = $servicesCount;
            }
            
            // Reset fees_types enhanced fields (only if they exist)
            $feeTypesCount = 0;
            if (Schema::hasColumn('fees_types', 'academic_level')) {
                $feeTypesCount = DB::table('fees_types')->update([
                    'academic_level' => 'all',
                    'amount' => 0,
                    'due_date_offset' => 30,
                    'is_mandatory_for_level' => false,
                    'category' => 'academic'
                ]);
                $rollbackStats['fees_types_reset'] = $feeTypesCount;
            }
            
            // Log rollback with detailed statistics
            if (Schema::hasTable('fee_system_migration_logs')) {
                DB::table('fee_system_migration_logs')->insert([
                    'migration_name' => 'migrate_existing_fee_data_to_service_structure_rollback',
                    'status' => 'completed',
                    'migration_details' => json_encode($rollbackStats),
                    'notes' => 'Rollback completed - enhanced fee system data removed',
                    'migration_date' => now(),
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }
            
            \Log::info('Fee system migration rollback completed', $rollbackStats);
        });
    }
};