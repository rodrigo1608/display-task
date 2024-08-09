<?php

namespace App\Jobs;

use App\Models\NotificationTime;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

class NotifyAtCustomTime implements ShouldQueue
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
        Log::info('Job NotifyAtCustomTime:');
        Log::info('Job NotifyAtCustomTime: INÍCIO');

        $notificationTimes = NotificationTime::all();

        if ($notificationTimes->isEmpty()) {

            Log::info('Job NotifyAtCustomTime: Não foi encontrado nenhuma notificação programada');
        } else {

            foreach ($notificationTimes as $notificationTime) {
                Log::info("Job NotifyAtCustomTime: Início da iteração referente às notificações programadas");

                $recurring = $notificationTime->reminder->recurring;

                $hasSpecificDate = !is_null($recurring->specific_date);

                $specificDate = $hasSpecificDate
                    ? getCarbonDate($recurring->specific_date)
                    : null;


                $isTask = is_null($notificationTime->reminder->user_id);

                $notificationData = [

                    'is_task' => $isTask,

                    'notification_time' => $notificationTime,

                    'recurring' => $recurring,

                    'specific_date' => $specificDate,

                    'has_specific_date' => $hasSpecificDate,
                ];

                if ($hasSpecificDate) {

                    $notificationPrefix = $isTask
                        ? 'A tarefa em análise está configurada'
                        : 'O lembrete em análise está configurado';

                    Log::info('Job NotifyAtCustomTime: ' . $notificationPrefix . ' para uma data específica - Recurring ID: ' . $recurring->id);

                    $isValidAlertDay =checkValidAlertDay
                    $isToday = checkIsToday($specificDate);

                    if (!$isToday) {

                        getNotTodayNotifyDateLog($notificationData);
                    } else {

                        $isNotificationTime = logNotify($notificationData, $isToday);

                        if ($isNotificationTime) {

                            notify($notificationData, $isToday);
                        }
                    }
                } else {

                    Log::info('Job NotifyAtCustomTime: A tarefa em análise possui recorrencia(s) - Recurring ID: ' . $recurring->id);

                    $recurring = $notificationTime->reminder->recurring;

                    $recurringDays = getRepeatingDays($recurring);

                    $recurringMessage = getRecurringMessage($recurring);

                    Log::info('Job NotifyAtCustomTime: ' . $recurringMessage);

                    foreach ($recurringDays as $day) {

                        $isToday = checkIsToday($day);

                        if (!$isToday) {

                            getNotTodayNotifyDateLog($notificationTime, $day);
                        } elseif ($isToday) {

                            // $isToday = checkIsToday($day);

                            $isBeforeCustomTime = $isToday && ($now->format('H:i') < $customTime->format('H:i'));

                            $isNotificationTime = $isToday && ($now->format('H:i') == $customTime->format('H:i'));

                            $isAfterCustomTime = $isToday && ($now->format('H:i') > $customTime->format('H:i'));;
                        }
                    }
                }
                Log::info("Job NotifyAtCustomTime: Fim da iteração referente a hora de notificação");
            }
        }

        Log::info('Job NotifyAtCustomTime: FIM');
    }
}
