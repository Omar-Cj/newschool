<?php

namespace Database\Seeders\Accounts;

use Illuminate\Database\Seeder;
use App\Models\Accounts\ExpenseCategory;
use App\Enums\Status;

class ExpenseCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $categories = [
            [
                'name' => 'Office Supplies',
                'code' => 'OFF-SUP',
                'description' => 'Office supplies and stationery',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Utilities',
                'code' => 'UTIL',
                'description' => 'Electricity, water, internet, and other utilities',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Salaries & Wages',
                'code' => 'SAL-WAG',
                'description' => 'Employee salaries and wages',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Maintenance & Repairs',
                'code' => 'MAIN-REP',
                'description' => 'Building and equipment maintenance',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Transportation',
                'code' => 'TRANS',
                'description' => 'Transportation and vehicle expenses',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Marketing & Advertising',
                'code' => 'MARK-ADV',
                'description' => 'Marketing and advertising expenses',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Professional Services',
                'code' => 'PROF-SRV',
                'description' => 'Legal, accounting, and consulting services',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Insurance',
                'code' => 'INS',
                'description' => 'Insurance premiums and coverage',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Rent',
                'code' => 'RENT',
                'description' => 'Building and equipment rent',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Training & Development',
                'code' => 'TRAIN-DEV',
                'description' => 'Staff training and professional development',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Books & Library',
                'code' => 'BOOKS-LIB',
                'description' => 'Books, journals, and library resources',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Equipment & Technology',
                'code' => 'EQUIP-TECH',
                'description' => 'Computer equipment and technology purchases',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Food & Catering',
                'code' => 'FOOD-CAT',
                'description' => 'Food and catering services',
                'status' => Status::ACTIVE,
            ],
            [
                'name' => 'Miscellaneous',
                'code' => 'MISC',
                'description' => 'Other miscellaneous expenses',
                'status' => Status::ACTIVE,
            ],
        ];

        // Get all branches if MultiBranch module is active
        if (hasModule('MultiBranch')) {
            $branches = \Modules\MultiBranch\Entities\Branch::active()->get();

            foreach ($branches as $branch) {
                foreach ($categories as $category) {
                    ExpenseCategory::create([
                        'name' => $category['name'],
                        'code' => $category['code'],
                        'description' => $category['description'],
                        'status' => $category['status'],
                        'branch_id' => $branch->id,
                    ]);
                }
            }
        } else {
            // Create categories for default branch
            foreach ($categories as $category) {
                ExpenseCategory::create([
                    'name' => $category['name'],
                    'code' => $category['code'],
                    'description' => $category['description'],
                    'status' => $category['status'],
                    'branch_id' => 1,
                ]);
            }
        }
    }
}
