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

        Log::info('Job NotifyAtCustomTime: Iniciado, Tentativa de notificar o usuário sobre a tarefa com um horário customizado.');

        $customNotificationTimes = getNotificationtimes('custom_time');

        if($customNotificationTimes->isEmpty()){

            Log::info('Job NotifyAtCustomTime: Não foi encontrado nenhuma notificação com horário customizado.');

        }else{

            foreach ($customNotificationTimes as $notificationTime) {

                $now = getCarbonNow();

                //Se houver um user_id setado na instancia de um Reminder, é lembrete
                //Se houver um task_id setado na instancia de um Reminder, é uma tarefa
                $isTask = is_null($notificationTime->reminder->user_id);

                $hasSpecificDate = !is_null($notificationTime->reminder->recurring->specific_date);

                $userToNotify =  $notificationTime->user;

                if ($hasSpecificDate) {

                    Log::info('Job NotifyAtCustomTime: A condição de notificação para uma data específica foi atendida.');

                    $specificDate = getCarbonDate($notificationTime->reminder->recurring->specific_date);

                    $customTime = getCarbonTime($notificationTime->custom_time);


                    $isBeforeCustomTime = $specificDate->isToday() && ($now->format('H:i') < $customTime->format('H:i'));

                    $isNotificationTime = $specificDate->isToday() && ($now->format('H:i') == $customTime->format('H:i'));

                    $isAfterCustomTime = $specificDate->isToday() && ($now->format('H:i') > $customTime->format('H:i'));


                    if( $isBeforeCustomTime){

                        Log::info("Job NotifyAtCustomTime: A notificação (id: $notificationTime->id) está programada para hoje.");
                        Log::info('Horário atual: ' . $now);
                        Log::info('Horário customizado:' . $customTime->format('H:i'));

                    }elseif($specificDate->isToday() && ($now->format('H:i') > $customTime->format('H:i')))


                    ? "Job NotifyAtCustomTime: A notificação (id: $notificationTime->id) está programada para hoje."
                    : "Job NotifyAtCustomTime: A notificação (id: $notificationTime->id) não está programada para hoje.");

                   if($isNotification)

                    if ($isNotificationTime && $isTask) {

                        Log::info('job NotifyAtCustomTime entrou no if que confirma que é uma tarefa e está na hora');

                        $task = $notificationTime->reminder->task;

                        $start = getStartDuration($task, $userToNotify->id);

                        $notificationMessage = getTaskNotificationMessage($task->title, $customTime, $start);

                        $taskData = $task->getAttributes();

                        $taskData['start'] = $start;
                        $taskData['message'] =  $notificationMessage;

                        Log::info('job NotifyAtCustomTime entrou no if de enviar tarefa');

                        Mail::to($userToNotify->email)->send(new TaskNotify($taskData));

                        Log::info('job NotifyAtCustomTime finalizado');
                    } elseif ($isNotificationTime) {

                        $reminder = $notificationTime->reminder;

                        Log::info('job NotifyAtCustomTime entrou no if de enviar recado');

                        Mail::to($userToNotify->email)->send(new ReminderNotify($reminder));
                    }
                }
            }

        }



        Log::info('job NotifyAtCustomTime finalizado');
    }
}
