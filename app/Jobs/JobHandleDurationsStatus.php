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


        Log::info($now->format('H:i'));

        $tasks = Task::all();

        $todayWeekDay = getWeekDayName($now->toDateString());

        foreach ($tasks as $task) {

            $isRecurrentTask = $task->reminder->recurring->specific_date == null;

            $isTodayRecurring = $task->reminder->recurring->$todayWeekDay == 'true';

            $isSpecificDateEqualToToday = $task->reminder->recurring->specific_date_weekday == $todayWeekDay;

            $isToday =  $isTodayRecurring  || $isSpecificDateEqualToToday;

            if ($isRecurrentTask && $isToday) {

                foreach ($task->durations as $duration) {

                    $start =  getCarbonTime($duration->start);
                    $end =    getCarbonTime($duration->end);

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
            } elseif ($isToday) {

                foreach ($task->durations as $duration) {

                    $start =  getCarbonTime($duration->start);
                    $end =  getCarbonTime($duration->end);

                    $isInProgress = ($start->lessThanOrEqualTo($now) && $end->greaterThanOrEqualTo($now));
                    $isFinished = $end->lessThan($now);

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
