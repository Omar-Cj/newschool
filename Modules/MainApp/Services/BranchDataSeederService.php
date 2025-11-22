<?php

namespace Modules\MainApp\Services;

use App\Models\Academic\Section;
use App\Models\Academic\Shift;
use App\Models\Fees\FeesType;
use App\Models\Session;
use App\Models\StudentInfo\StudentCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class BranchDataSeederService
{
    /**
     * Seed all default data for a branch
     *
     * @param int $schoolId
     * @param int $branchId
     * @return void
     */
    public function seedBranchData(int $schoolId, int $branchId): void
    {
        try {
            $this->seedStudentCategories($schoolId, $branchId);
            $this->seedFeeTypes($schoolId, $branchId);
            $this->seedAcademicSession($schoolId, $branchId);
            $this->seedSections($schoolId, $branchId);
            $this->seedShifts($schoolId, $branchId);

            Log::info('Branch data seeded successfully', [
                'school_id' => $schoolId,
                'branch_id' => $branchId
            ]);
        } catch (\Throwable $th) {
            Log::error('Failed to seed branch data', [
                'school_id' => $schoolId,
                'branch_id' => $branchId,
                'error' => $th->getMessage(),
                'trace' => $th->getTraceAsString()
            ]);

            // Re-throw to trigger transaction rollback
            throw $th;
        }
    }

    /**
     * Seed student categories for a branch
     *
     * @param int $schoolId
     * @param int $branchId
     * @return void
     */
    protected function seedStudentCategories(int $schoolId, int $branchId): void
    {
        $categories = [
            [
                'name' => 'Normal',
                'status' => 1,
            ],
            [
                'name' => 'Scholarship',
                'status' => 1,
            ],
        ];

        foreach ($categories as $category) {
            DB::table('student_categories')->insertOrIgnore([
                'name' => $category['name'],
                'status' => $category['status'],
                'school_id' => $schoolId,
                'branch_id' => $branchId,
                'is_fee_exempt' => ($category['name'] === 'Scholarship' ? 1 : 0),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Log::info('Student categories seeded', [
            'school_id' => $schoolId,
            'branch_id' => $branchId,
            'count' => count($categories)
        ]);
    }

    /**
     * Seed fee types for a branch
     *
     * @param int $schoolId
     * @param int $branchId
     * @return void
     */
    protected function seedFeeTypes(int $schoolId, int $branchId): void
    {
        $feeTypes = [
            [
                'code' => 'TUITION_SECONDARY',
                'name' => 'Secondary Tuition',
                'description' => 'Lacagta Bisha ardayda Secondaryga',
                'academic_level' => 'secondary',
                'amount' => 30.00,
                'category' => 'academic',
                'status' => 1,
                'is_mandatory_for_level' => 1,
            ],
            [
                'code' => 'BUS_FEE',
                'name' => 'Bus',
                'description' => 'Lacagta Baska',
                'academic_level' => 'all',
                'amount' => 15.00,
                'category' => 'transport',
                'status' => 1,
                'is_mandatory_for_level' => 0,
            ],
            [
                'code' => 'TUITION_PRIMARY',
                'name' => 'Primary Tuition',
                'description' => 'Lacagta Bisha Primaryga',
                'academic_level' => 'primary',
                'amount' => 15.00,
                'category' => 'academic',
                'status' => 1,
                'is_mandatory_for_level' => 1,
            ],
            [
                'code' => 'TUITION_KG',
                'name' => 'KG Tuition',
                'description' => 'Lacagta Bisha ardayda KG',
                'academic_level' => 'kg',
                'amount' => 15.00,
                'category' => 'academic',
                'status' => 1,
                'is_mandatory_for_level' => 1,
            ],
        ];

        foreach ($feeTypes as $feeType) {
            DB::table('fees_types')->insertOrIgnore([
                'code' => $feeType['code'],
                'name' => $feeType['name'],
                'description' => $feeType['description'],
                'academic_level' => $feeType['academic_level'],
                'amount' => $feeType['amount'],
                'category' => $feeType['category'],
                'status' => $feeType['status'],
                'is_mandatory_for_level' => $feeType['is_mandatory_for_level'],
                'school_id' => $schoolId,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        Log::info('Fee types seeded', [
            'school_id' => $schoolId,
            'branch_id' => $branchId,
            'count' => count($feeTypes)
        ]);
    }

    /**
     * Seed academic session for a branch
     *
     * @param int $schoolId
     * @param int $branchId
     * @return void
     */
    protected function seedAcademicSession(int $schoolId, int $branchId): void
    {
        $currentYear = date('Y');

        // Check if session already exists for this school/branch/year
        $exists = DB::table('sessions')
            ->where('school_id', $schoolId)
            ->where('branch_id', $branchId)
            ->where('name', $currentYear)
            ->exists();

        if (!$exists) {
            DB::table('sessions')->insert([
                'name' => $currentYear,
                'start_date' => $currentYear . '-01-01',
                'end_date' => $currentYear . '-12-31',
                'status' => 1,
                'school_id' => $schoolId,
                'branch_id' => $branchId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            Log::info('Academic session seeded', [
                'school_id' => $schoolId,
                'branch_id' => $branchId,
                'session_name' => $currentYear
            ]);
        }
    }

    /**
     * Seed sections for a branch
     *
     * @param int $schoolId
     * @param int $branchId
     * @return void
     */
    protected function seedSections(int $schoolId, int $branchId): void
    {
        $sections = ['A', 'B', 'C'];

        foreach ($sections as $sectionName) {
            // Check if section already exists for this school/branch
            $exists = DB::table('sections')
                ->where('school_id', $schoolId)
                ->where('branch_id', $branchId)
                ->where('name', $sectionName)
                ->exists();

            if (!$exists) {
                DB::table('sections')->insert([
                    'name' => $sectionName,
                    'status' => 1,
                    'school_id' => $schoolId,
                    'branch_id' => $branchId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Log::info('Sections seeded', [
            'school_id' => $schoolId,
            'branch_id' => $branchId,
            'sections' => $sections
        ]);
    }

    /**
     * Seed shifts for a branch
     *
     * @param int $schoolId
     * @param int $branchId
     * @return void
     */
    protected function seedShifts(int $schoolId, int $branchId): void
    {
        $shifts = ['Morning', 'Afternoon'];

        foreach ($shifts as $shiftName) {
            // Check if shift already exists for this school/branch
            $exists = DB::table('shifts')
                ->where('school_id', $schoolId)
                ->where('branch_id', $branchId)
                ->where('name', $shiftName)
                ->exists();

            if (!$exists) {
                DB::table('shifts')->insert([
                    'name' => $shiftName,
                    'status' => 1,
                    'school_id' => $schoolId,
                    'branch_id' => $branchId,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        Log::info('Shifts seeded', [
            'school_id' => $schoolId,
            'branch_id' => $branchId,
            'shifts' => $shifts
        ]);
    }
}
