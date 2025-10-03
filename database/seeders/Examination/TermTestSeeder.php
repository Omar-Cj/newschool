<?php

namespace Database\Seeders\Examination;

use App\Models\Examination\Term;
use App\Models\Examination\TermDefinition;
use App\Models\Session;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class TermTestSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        // Create term definitions if they don't exist
        $definitions = [
            [
                'name' => 'First Term',
                'code' => 'TERM1',
                'sequence' => 1,
                'typical_duration_weeks' => 12,
                'typical_start_month' => 9,
                'description' => 'First academic term',
                'is_active' => true,
            ],
            [
                'name' => 'Second Term',
                'code' => 'TERM2',
                'sequence' => 2,
                'typical_duration_weeks' => 12,
                'typical_start_month' => 1,
                'description' => 'Second academic term',
                'is_active' => true,
            ],
            [
                'name' => 'Third Term',
                'code' => 'TERM3',
                'sequence' => 3,
                'typical_duration_weeks' => 10,
                'typical_start_month' => 4,
                'description' => 'Third academic term',
                'is_active' => true,
            ],
        ];

        foreach ($definitions as $definition) {
            TermDefinition::firstOrCreate(
                ['code' => $definition['code']],
                $definition
            );
        }

        // Get or create a session
        $session = Session::firstOrCreate(
            ['name' => '2024-2025'],
            [
                'session' => '2024-2025',
                'status' => 1,
            ]
        );

        // Get term definitions
        $firstTerm = TermDefinition::where('code', 'TERM1')->first();
        $secondTerm = TermDefinition::where('code', 'TERM2')->first();
        $thirdTerm = TermDefinition::where('code', 'TERM3')->first();

        // Create terms for the session
        $terms = [
            [
                'term_definition_id' => $firstTerm->id,
                'session_id' => $session->id,
                'start_date' => Carbon::create(2024, 9, 1),
                'end_date' => Carbon::create(2024, 11, 30),
                'status' => 'closed',
                'opened_by' => 1,
                'opened_at' => Carbon::create(2024, 9, 1),
                'closed_at' => Carbon::create(2024, 11, 30),
                'notes' => 'First term completed successfully',
            ],
            [
                'term_definition_id' => $secondTerm->id,
                'session_id' => $session->id,
                'start_date' => Carbon::create(2025, 1, 5),
                'end_date' => Carbon::create(2025, 3, 31),
                'status' => 'active',
                'opened_by' => 1,
                'opened_at' => Carbon::create(2025, 1, 5),
                'notes' => 'Currently active term',
            ],
            [
                'term_definition_id' => $thirdTerm->id,
                'session_id' => $session->id,
                'start_date' => Carbon::create(2025, 4, 15),
                'end_date' => Carbon::create(2025, 6, 30),
                'status' => 'upcoming',
                'opened_by' => 1,
                'notes' => 'Scheduled for next period',
            ],
        ];

        foreach ($terms as $term) {
            Term::firstOrCreate(
                [
                    'term_definition_id' => $term['term_definition_id'],
                    'session_id' => $term['session_id'],
                ],
                $term
            );
        }

        $this->command->info('Term test data seeded successfully!');
    }
}