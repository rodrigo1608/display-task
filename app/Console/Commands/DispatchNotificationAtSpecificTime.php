<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DispatchNotificationAtSpecificTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dispatch:notification-at-specific-time';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatches a job to send notifications to users about reminders and tasks scheduled for a specific time.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {

            NotificationAtSpecificTime::dispatch();

            $this->info('The command has been executed successfully.');
        } catch (\Exception $e) {
            $this->error('Something went wrong: ' . $e->getMessage());
            return 1;
        }
    }
}
