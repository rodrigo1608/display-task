<?php

namespace App\Jobs;

use App\Models\Duration;
use App\Models\User;
use App\Models\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

use Carbon\Carbon;

class JobHandleDurationsStatus implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('job iniciado');
        $now = Carbon::now('America/Sao_Paulo');

        Log::info($now);

        $tasks = Task::all();

        foreach ($tasks as $task) {

            $isRecurrentTask = $task->reminder->recurring->specific_date == null;

            if ($isRecurrentTask) {

                foreach ($task->durations as $duration) {

                    $start =  getCarbonTime($duration->start);
                    $end =  getCarbonTime($duration->end);

                    $isInProgress = $start->lessThanOrEqualTo($now) && $end->greaterThanOrEqualTo($now);
                    $isFinished = $end->lessThan($now);
                    $isStarting = $start->greaterThan($now);

                    if ($isInProgress) {

                        $duration->update(['status' => 'in_progress']);
                    } elseif ($isFinished) {

                        $duration->update(['status' => 'finished']);
                    } elseif ($isStarting) {

                        $duration->update(['status' => 'starting']);
                    }
                }
            } else {

                foreach ($task->durations as $duration) {

                    $start =  getCarbonTime($duration->start);
                    $end =  getCarbonTime($duration->end);

                    $isInProgress = ($start->lessThanOrEqualTo($now) && $end->greaterThanOrEqualTo($now)) && $duration->stauts == 'starting';
                    $isFinished = $end->lessThan($now) && $duration->stauts == 'starting';

                    if ($isInProgress) {
                        $duration->update(['status' => 'in_progress']);
                    } elseif ($end->lessThan($now)) {

                        $duration->update(['status' => 'finished']);
                    }
                }
            }
        }

        Log::info('job encerrado');
    }
}
