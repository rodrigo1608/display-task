<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Jobs\HandleDurationsStatus;

class DispatchHandleDurationsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:handle-durations-status';

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

            HandleDurationsStatus::dispatch();

            $this->info('The command has been executed successfully.');
        } catch (\Exception $e) {
            $this->error('Something went wrong: ' . $e->getMessage());
            return 1;
        }
    }
}
