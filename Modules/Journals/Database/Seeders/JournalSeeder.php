<?php

namespace Modules\Journals\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Eloquent\Model;
use Modules\Journals\Entities\Journal;

class JournalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();

        $journals = [
            [
                'name' => 'General Journal',
                'branch' => 'Main Branch',
                'description' => 'General purpose journal for all transactions',
                'status' => 'active',
                'created_by' => 1, // Assuming admin user with ID 1 exists
            ],
            [
                'name' => 'Fee Collection Journal',
                'branch' => 'Main Branch',
                'description' => 'Dedicated journal for fee collection transactions',
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'name' => 'Cash Journal',
                'branch' => 'Main Branch',
                'description' => 'Journal for cash transactions',
                'status' => 'active',
                'created_by' => 1,
            ],
            [
                'name' => 'Digital Payment Journal',
                'branch' => 'Main Branch',
                'description' => 'Journal for Zaad and Edahab transactions',
                'status' => 'active',
                'created_by' => 1,
            ],
        ];

        foreach ($journals as $journal) {
            Journal::create($journal);
        }
    }
}