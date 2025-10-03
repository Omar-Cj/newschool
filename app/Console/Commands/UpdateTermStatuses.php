<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\Academic\TermService;
use App\Repositories\Academic\TermRepository;

class UpdateTermStatuses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'terms:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update term statuses based on dates (activate upcoming terms, close expired terms)';

    protected $termService;
    protected $termRepository;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->termRepository = app(TermRepository::class);
        $this->termService = app(TermService::class);

        $this->info('Updating term statuses...');

        try {
            // Update term statuses
            $this->termService->updateTermStatuses();

            $this->info('Term statuses updated successfully.');

            // Log the updates
            $activeTerm = $this->termRepository->getActiveTerm();
            if ($activeTerm) {
                $this->info('Active term: ' . $activeTerm->getDisplayName());
            }

            $upcomingTerms = $this->termRepository->getUpcomingTerms();
            if ($upcomingTerms->count() > 0) {
                $this->info('Upcoming terms: ' . $upcomingTerms->count());
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('Error updating term statuses: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}