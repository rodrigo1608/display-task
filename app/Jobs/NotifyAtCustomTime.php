<?php

namespace App\Jobs;

use App\Mail\TaskNotify;
use App\Mail\ReminderNotify;

use Illuminate\Support\Facades\Mail;

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

        $customNotificationTimes = getNotificationtimes('custom_time');

        if ($customNotificationTimes->isEmpty()) {

            Log::info('Job NotifyAtCustomTime: Não foi encontrado nenhuma notificação com horário customizado.');
        } else {

            foreach ($customNotificationTimes as $notificationTime) {

                $now = getCarbonNow();

                //Se houver um user_id setado na instancia de um Reminder, é lembrete
                //Se houver um task_id setado na instancia de um Reminder, é uma tarefa
                $isTask = is_null($notificationTime->reminder->user_id);

                $hasSpecificDate = !is_null($notificationTime->reminder->recurring->specific_date);

                $userToNotify =  $notificationTime->user;

                if ($hasSpecificDate) {

                    Log::info('Job NotifyAtCustomTime: A condição que verifica se é uma tarefa com data específica, foi atendida.');

                    $specificDate = getCarbonDate($notificationTime->reminder->recurring->specific_date);

                    $customTime = getCarbonTime($notificationTime->custom_time);

                    $isNotToday = !$specificDate->isToday();

                    $isBeforeCustomTime = $specificDate->isToday() && ($now->format('H:i') < $customTime->format('H:i'));

                    $isNotificationTime = $specificDate->isToday() && ($now->format('H:i') == $customTime->format('H:i'));

                    $isAfterCustomTime = $specificDate->isToday() && ($now->format('H:i') > $customTime->format('H:i'));

                    if ($isNotToday) {
                        Log::info("Job NotifyAtCustomTime: A notificação (ID: $notificationTime->id) não está programada para hoje.");

                        Log::info('Data atual: ' . getToday()->format('d/m/Y'));
                        Log::info('Data programada: ' . getCarbonDate($specificDate)->format('d/m/Y'));
                    } elseif ($isBeforeCustomTime) {

                        Log::info("Job NotifyAtCustomTime: A notificação (ID: $notificationTime->id) está programada para hoje.");
                        Log::info('Horário atual: ' . $now);
                        Log::info('Horário programado:' . $customTime->format('H:i'));
                    } elseif ($isAfterCustomTime) {

                        Log::info("Job NotifyAtCustomTime: O horário da notificação (ID: $notificationTime->id) já passou.");

                        Log::info('Job NotifyAtCustomTime: Horário atual: ' . $now->format('H:i'));
                        Log::info('Job NotifyAtCustomTime: Horário programado:' . $customTime->format('H:i'));
                    } elseif ($isNotificationTime && $isTask) {

                        Log::info('Job NotifyAtCustomTime: A condição que verifica se o horário da notificação da tarefa é agora, foi atendida.');

                        $task = $notificationTime->reminder->task;

                        $start = getStartDuration($task, $userToNotify->id);

                        $notificationMessage = getTaskNotificationMessage($task->title, $customTime, $start);

                        $taskData = $task->getAttributes();

                        $taskData['start'] = $start;
                        $taskData['message'] =  $notificationMessage;


                        Mail::to($userToNotify->email)->send(new TaskNotify($taskData));

                        Log::info("Job NotifyAtCustomTime: A notificação de tarefa foi enviada $userToNotify->email");
                    } elseif ($isNotificationTime) {
                        Log::info('Job NotifyAtCustomTime: A condição de verificar se o horário do lembrete é agora, foi atendida.');
                        $reminder = $notificationTime->reminder;

                        Mail::to($userToNotify->email)->send(new ReminderNotify($reminder));
                        Log::info("Job NotifyAtCustomTime: O lembrete foi enviado para  $userToNotify->email");
                    }
                }
            }
        }

        Log::info('Job NotifyAtCustomTime: FIM');
        Log::info('Job NotifyAtCustomTime:');
    }
}
