<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\JobHandleDurationsStatus;

class DispatchJobHandleDurationsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:job-handle-durations-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches the JobHandleDurationsStatus job';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            JobHandleDurationsStatus::dispatch();

            $this->info('The command has been executed successfully.');
        } catch (\Exception $e) {
            $this->error('Something went wrong: ' . $e->getMessage());
            return 1;
        }
    }
}
