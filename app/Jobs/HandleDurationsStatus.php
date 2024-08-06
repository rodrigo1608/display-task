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

class HandleDurationsStatus implements ShouldQueue
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
        Log::info('Job HandleDurationsStatus:');
        Log::info('Job HandleDurationsStatus: INÍCIO');

        $now = Carbon::now('America/Sao_Paulo');

        $tasks = Task::all();

        $currentDayOfWeek = getDayOfWeek($now->toDateString());

        foreach ($tasks as $task) {

            $isRecurrentTask = $task->reminder->recurring->specific_date == null;

            if ($isRecurrentTask) {

                Log::info("Job HandleDurationsStatus: Foi verificado que é uma tarefa  recorrente - (ID: $task->id)");

                $isTodayRecurringDay = $task->reminder->recurring->$currentDayOfWeek === 'true';

                if ($isTodayRecurringDay) {

                    Log::info('Job HandleDurationsStatus: A recorrência foi confirmada para ocorrer hoje.');

                    handleDurationStatus($task, $now, 'recurring');
                }
            } else {

                Log::info("Job HandleDurationsStatus: Foi verificado que é uma tarefa  com data específica - (ID: $task->id)");

                $specificDate = getCarbonDate($task->reminder->recurring->specific_date);

                $isSpecificDateToday =  $specificDate->isSameDay($now);

                if ($isSpecificDateToday) {

                    handleDurationStatus($task, $now);
                }
            }
        }

        Log::info('Job HandleDurationsStatus: FIM');
        Log::info('Job HandleDurationsStatus');
    }
}
