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
                Log::info("Job NotifyAtCustomTime: Início da iteração referente a hora de notificação");

                $recurring = $notificationTime->reminder->recurring;

                $task = $notificationTime->reminder->task;

                $userToNotify =  $notificationTime->user;

                $start = getStartDuration($task, $userToNotify->id);

                // $customTime = getCarbonTime($notificationTime->custom_time);

                // $now = getCarbonNow();

                //Se houver um user_id setado na instancia de um Reminder, é lembrete
                //Se houver um task_id setado na instancia de um Reminder, é uma tarefa
                $isTask = is_null($notificationTime->reminder->user_id);

                $hasSpecificDate = !is_null($recurring->specific_date);

                $specificDate = $hasSpecificDate
                    ? getCarbonDate($notificationTime->reminder->recurring->specific_date)
                    : null;

                $notificationData = [

                    // 'customTime' => $customTime,

                    // 'half_an_hour_before' =>  $halfAnHourBefore,

                    // 'has_specific_date' => $hasSpecificDate,

                    // 'notification_time' => $notificationTime,

                    // 'one_day_earlier' => $oneDayEarlier,

                    // 'one_hour_before' => $oneHourBefore,

                    'recurring' => $recurring,

                    'specific_date' => $specificDate,

                    'start' => $start,

                    'task' => $task,

                    // 'two_hours_before' => $twoHoursBefore,

                    'user_to_notify' => $userToNotify,

                ];

                if ($hasSpecificDate) {

                    $notificationType = $isTask ? 'A tarefa em análise está configurada' : 'O lembrete em análise está configurado';

                    Log::info('Job NotifyAtCustomTime: ' . $notificationType . '  para uma data específica - Recurring ID: ' . $recurring->id);


                    $isToday = checkIsToday($specificDate);

                    if (!$isToday) {

                        getNotTodayNotifyDateLog($notificationData);
                    } elseif ($isToday) {

                        $isNotificationTime = getNotifyLog($notificationData);

                        if ($isNotificationTime) {

                            notify($notificationData);
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

                            $isToday = checkIsToday($day);

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
