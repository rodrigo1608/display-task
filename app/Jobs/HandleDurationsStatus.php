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

            Log::info('Job HandleDurationsStatus: Início da iteração referente a tarefas');

            $recurring = $task->reminder->recurring;

            $isRecurrentTask =  $recurring->specific_date == null;

            if ($isRecurrentTask) {

                Log::info('Job HandleDurationsStatus: A tarefa em análise é recorrente - Recurring ID: ' . $recurring->id);

                $isTodayRecurringDay = $recurring->$currentDayOfWeek === 'true';

                $daysOfWeek = getDaysOfWeek();

                if ($isTodayRecurringDay) {

                    Log::info('Job HandleDurationsStatus: Existe uma recorrência programada para hoje (' . $daysOfWeek[$currentDayOfWeek] . ')');

                    handleDurationStatus($task, $now, 'recurring');
                } else {

                    $recurringMessage = getRecurringMessage($recurring);

                    Log::info('Job HandleDurationsStatus: A tarefa em análise não está programada para ocorrer hoje');
                    Log::info('Job HandleDurationsStatus: Dia recorrente: ' . $recurringMessage);
                    Log::info('Job HandleDurationsStatus: Dia atual: ' . $daysOfWeek[$currentDayOfWeek]);
                }
            } else {

                Log::info('Job HandleDurationsStatus: Foi verificado que é uma tarefa  com data específica - Recurring ID: ' . $recurring->id);

                $specificDate = getCarbonDate($recurring->specific_date);

                $isSpecificDateToday =  $specificDate->isSameDay($now);

                if ($isSpecificDateToday) {

                    Log::info('Job HandleDurationsStatus: A tarefa foi programada para ocorrer especificamente hoje -' . $now->format('d/m/Y'));

                    handleDurationStatus($task, $now);
                } else {
                    Log::info('Job HandleDurationsStatus: A tarefa não está programada para hoje');
                    Log::info('Job HandleDurationsStatus: Data programada: ' . $specificDate->format('d/m/Y'));
                    Log::info('Job HandleDurationsStatus: Data atual: ' . $now->format('d/m/Y'));
                }
            }
            Log::info('Job HandleDurationsStatus: Fim da iteração referente a tarefas');
        }

        Log::info('Job HandleDurationsStatus: FIM');
        Log::info('Job HandleDurationsStatus');
    }
}
