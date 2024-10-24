<?php

use App\Mail\TaskNotify;
use App\Mail\ReminderNotify;
use App\Models\Task;
use App\Models\Reminder;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

if (!function_exists('getFormatedDateBR')) {

    function getFormatedDateBR($date)
    {
        return Carbon::parse($date)->format('d/m/Y');
    }
}

if (!function_exists('getCarbonNow')) {

    function getCarbonNow()
    {
        return Carbon::now('America/Sao_Paulo');
    }
}

if (!function_exists('getCarbonTime')) {

    function getCarbonTime($stringTime)
    {
        return Carbon::parse($stringTime, 'America/Sao_Paulo');
    }
}

if (!function_exists('getCarbonDate')) {

    function getCarbonDate($date)
    {
        return Carbon::parse($date)->timezone('America/Sao_Paulo');
    }
}

if (!function_exists('getToday')) {

    function getToday()
    {

        return Carbon::today('America/Sao_Paulo');
    }
}

if (!function_exists('getDaysOfWeek')) {

    function getDaysOfWeek()
    {
        return  [

            'sunday' => 'domingo',

            'monday' => 'segunda',

            'tuesday' => 'terça',

            'wednesday' => 'quarta',

            'thursday' => 'quinta',

            'friday' => 'sexta',

            'saturday' => 'sábado',

        ];
    }
}

if (!function_exists('getDayOfWeek')) {

    function getDayOfWeek($date, $language = 'en')
    {
        $dayName = strtolower($date->englishDayOfWeek);

        $daysOfWeek = getDaysOfWeek();

        $isPTBR = $language == 'pt-br';

        return $isPTBR ? $daysOfWeek[$dayName] : $dayName;
    }
}

if (!function_exists('checkIsToday')) {

    function checkIsToday($date)
    {
        $today = getToday();

        $dayOfWeekToday = getDayOfWeek($today);

        if ($date instanceof Carbon) {

            return $date->isToday();
        } else {

            return $dayOfWeekToday === $date;
        }
    }
}

if (!function_exists('checkIsDayBefore')) {

    function checkIsDayBefore($date)
    {

        $today = getToday();

        $dayOfWeekToday  = getDayOfWeek($today);

        if ($date instanceof Carbon) {

            $dayBefore =  $date->copy()->subDay();

            return $dayBefore->isToday();
        } else {

            $carbonDate = Carbon::parse($date);

            $dayBefore = $carbonDate->subDay()->format('l');

            return   $dayOfWeekToday === strtolower($dayBefore);
        }
    }
}

if (!function_exists('getMonths')) {

    function getMonths($abbreviated = false)
    {

        return  [
            'January' => 'Janeiro',
            'February' => 'Fevereiro',
            'March' => 'Março',
            'April' => 'Abril',
            'May' => 'Maio',
            'June' => 'Junho',
            'July' => 'Julho',
            'August' => 'Agosto',
            'September' => 'Setembro',
            'October' => 'Outubro',
            'November' => 'Novembro',
            'December' => 'Dezembro',
        ];
    }
}

if (!function_exists('getFormatedTelephone')) {

    function getFormatedTelephone($user)
    {
        return  $user->telephone = '(' . substr($user->telephone, 0, 2) . ') ' . substr($user->telephone, 2, 1) . ' ' . substr($user->telephone, 3);
    }
}

if (!function_exists('getProfilePicturePath')) {

    function getProfilePicturePath($image, $email)
    {
        $hasUserUploadedPicture = isset($image);

        if ($hasUserUploadedPicture) {

            $emailWithoutDotCom = str_replace('.com', '', $email);

            $profilePictureName = $emailWithoutDotCom . '-' . time() . '-icon.' . $image->getClientOriginalExtension();

            return  $image->storeAs('profile_pictures', $profilePictureName);
        }

        return 'default_user_icon.jpg';
    }
}

if (!function_exists('getParticipantsEmail')) {

    function getParticipantsEmail($request)
    {
        $participants = [];

        foreach ($request->all() as $attribute => $value) {

            if (strpos($attribute, 'participant') === 0) {

                $participants[] = $value;
            }
        }

        return $participants;
    }
}

if (!function_exists('setTask')) {

    function setTask($task)
    {

        $createdBy =  $task->creator ?? null;

        $task['creator'] = $task->creator->name . '' . $task->creator->lastname;

        $task['telephone'] =  getFormatedTelephone($createdBy->telephone);

        $task['description'] = $task->feedbacks->first()->feedback;

        $task['attachments'] = $task->feedbacks->first()->attachments->all();

        $duration = $task->durations->first();

        $task['start'] =  $duration->start ? date('H:i', strtotime($duration->start)) : null;

        $task['end'] =  $duration->end ? date('H:i', strtotime($duration->end)) : null;
    }
}

if (!function_exists('getConflictingTask')) {

    function getConflictingTask($inputData, $recurrencePattern, $currentTaskID = null)
    {
        $userID = auth()->id();

        // Primeiramente, a consulta deve ignorar a tarefa que já foi criada para, no caso de algum usuário aceitá-la, não gerar conflito de sobreposição
        $conflictingTaskBuilder =  Task::with(['reminder.recurring', 'participants'])->where('concluded', 'false')->where('id', '!=', $currentTaskID)

            // Consulta que verifica se a tarefa pertence ao usuário logado ou se o usuário está participando de alguma tarefa
            ->where(function ($query) use ($userID) {

                $query->where('created_by', $userID)->orWhereHas('participants', function ($query) use ($userID) {

                    $query->where('user_id', $userID)->where('status', 'accepted');
                });

                // Como as recorrências são vinculadas aos lembretes, a consulta passará pela tabela reminders antes de acessar a tabela recurrings
            })->whereHas('reminder', function ($taskReminderQuery) use ($recurrencePattern, $inputData) {

                // Método que lida com a lógica das recorrências
                getTaskOccurrences($taskReminderQuery,  $recurrencePattern, $inputData);

                // Depois que a recorrência foi verificada, o código abaixo é responsável por verificar se as durações estão se sobrepondo
            })->whereHas('durations', function ($taskRecurringsDurtionQuery) use ($inputData) {
                addDurationOverlapQuery($taskRecurringsDurtionQuery, $inputData);
            });


        $conflictingTask = $conflictingTaskBuilder->first();

        $hasConflictingTask = $conflictingTaskBuilder->exists();

        if ($hasConflictingTask) {

            $conflitingTaskData = getConflitingTaskData($conflictingTask);

            session()->flash('conflictingTask',  $conflitingTaskData);

            return redirect()->back()->withErrors([

                'conflictingDuration' =>
                $conflictingTask->title,

            ])->withInput();
        }
    }
}

if (!function_exists('getConflitingTaskData')) {

    function getConflitingTaskData($conflitingTask)
    {

        $conflitingTaskData = $conflitingTask->toArray();

        $conflitingTaskData['owner'] = $conflitingTask->creator->name . ' ' . $conflitingTask->creator->lastname;

        $conflitingTaskData['owner_telehpone'] =  getFormatedTelephone($conflitingTask->creator);

        $conflitingTaskData['owner_email'] =  $conflitingTask->creator->email;

        $conflictingDuration =  $conflitingTask->durations->first();

        $conflitingTaskData['start'] = date('H:i', strtotime($conflictingDuration->start));

        $conflitingTaskData['end'] =  date('H:i', strtotime($conflictingDuration->end));

        $conflitingTaskData['recurringMessage'] = getRecurringMessage($conflitingTask->reminder->recurring);

        return $conflitingTaskData;
    }
}

if (!function_exists('getRecurrencePatterns')) {

    function getRecurrencePatterns($taskDetails)
    {
        $recurrenceKeys = ['specific_date', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];

        return array_filter($taskDetails, function ($value, $key) use ($recurrenceKeys) {
            return in_array($key, $recurrenceKeys) && ($value === "true" || ($key === 'specific_date' && $value !== null));
        }, ARRAY_FILTER_USE_BOTH);
    }
}
if (!function_exists('getRecurringMessage')) {

    function getRecurringMessage($recurring)
    {
        if (is_null($recurring->specific_date)) {

            $recurringMessage = '';

            $repeatingDays = getRepeatingDays($recurring, 'pt-br');

            $formattedDays = array_map(function ($day) {
                return '<span class="roboto fs-5">' . $day . '</span>';
            }, $repeatingDays);

            $numberOfRepeatingDays = count($repeatingDays);

            if ($numberOfRepeatingDays == 7) {

                $recurringMessage = 'Todos os dias';
            } else {

                if ($numberOfRepeatingDays > 1) {

                    $lastDay = array_pop($formattedDays);

                    $recurringMessage = '<span class="roboto-light">Irá se repetir a cada ' . implode(', ', $formattedDays);

                    $recurringMessage .= ' e ' . $lastDay;
                } else {
                    $recurringMessage .= (stripos($formattedDays[0], "sábado") !== false || stripos($formattedDays[0], "domingo") !== false)
                    ? 'Todos os ' . $formattedDays[0] . '<span class="roboto fs-5">s</span>'
                    : 'Todas as ' . $formattedDays[0] . '<span class="roboto fs-5">s</span>';
                }
            }
        } else {

            $date = getCarbonDate($recurring->specific_date);

            $dayOfWeekInPortuguese = getDayOfWeek($date, 'pt-br');

            $formatedDate = '<span class="fs-4 roboto">' . $date ->format('d/m/Y') . '</span>';
            $recurringMessage = "Ocorrerá exclusivamente no dia $formatedDate, $dayOfWeekInPortuguese.";
        }

        return $recurringMessage;
    }
}

if (!function_exists('getRepeatingDays')) {

    function getRepeatingDays($recurring, $language = 'en')
    {

        $daysOfWeek = getDaysOfWeek();

        $repeatingDays = [];

        $isPtBr = $language  == 'pt-br';

        foreach ($daysOfWeek as $key => $day) {

            if ($recurring->$key === 'true') {

                $repeatingDays[] = $isPtBr ? $day : $key;
            }
        }
        return $repeatingDays;
    }
}

if (!function_exists('getRecurringLog')) {

    function getRecurringLog($notificationTime)
    {

        $recurring = $notificationTime->reminder->recurring;
        $isTask = !is_null($notificationTime->reminder->task);

        $notificationSnippetContext  = getNotificationContextSnippet($isTask);

        if (is_null($recurring->specific_date)) {

            $recurringMessage = '';

            $repeatingDays = getRepeatingDays($recurring, 'pt-br');

            $numberOfRepeatingDays = count($repeatingDays);

            if ($numberOfRepeatingDays == 7) {

                $recurringMessage = $notificationSnippetContext . ' para ocorrer todos os dias da semana';
            } else {

                if ($numberOfRepeatingDays > 1) {

                    $lastDay = array_pop($repeatingDays);

                    $recurringMessage =  $notificationSnippetContext . ' para se repetir a cada ' . implode(', ', $repeatingDays);

                    $recurringMessage .= ' e ' . $lastDay . '.';
                } else {

                    $recurringMessage .=    ($repeatingDays[0]) === "sábado" || ($repeatingDays[0]) === 'domingo'
                        ?  $notificationSnippetContext . 'para se repetir todos os ' . ($repeatingDays[0]) . 's'
                        :  $notificationSnippetContext . 'para se repetir todas as ' . $repeatingDays[0] . 's';
                }
            }
        } else {

            $date = getCarbonDate($recurring->specific_date);

            $dayOfWeekInPortuguese = getDayOfWeek($date, 'pt-br');

            $formatedDate =  $date->format('d/m/Y');

            $recurringMessage = $notificationSnippetContext . ' para ocorrer exclusivamente no dia: ' . $formatedDate . ', ' . $dayOfWeekInPortuguese;
        }

        return $recurringMessage;
    }
}

if (!function_exists('getTaskOccurrences')) {

    function getTaskOccurrences(Builder $query, $recurrencePattern, $inputData = null)
    {

        $specificDate = $inputData['specific_date'];

        $date = getCarbonDate($specificDate);

        $dayOfWeek = isset($specificDate) ? getDayOfWeek($date) : null;

        return isset($specificDate)
            ?
            $query->whereHas('recurring', function ($query) use ($specificDate, $dayOfWeek) {
                $query->where('specific_date',  $specificDate)->orWhere($dayOfWeek, "true");
            })
            :
            $query->whereHas('recurring', function ($query) use ($recurrencePattern) {
                $query->where('specific_date_weekday', $recurrencePattern)->orWhere($recurrencePattern, 'true');
            });
    }
}


if (!function_exists('getAlertOptions')) {

    function getAlertOptions()
    {
        return [

            'half_an_hour_before' => 'Meia hora antes',

            'one_hour_before' => 'Uma hora antes',

            'two_hours_before' => 'Duas horas antes',

            'one_day_earlier' => 'Um dia antes'

        ];
    }
}

if (!function_exists('getSelectedPredefinedAlerts')) {

    function getSelectedPredefinedAlerts($notificationTime, $language = 'en')
    {
        $getAlertOptions = getAlertOptions();

        $predefinedAlerts = [];

        $isPtBr = $language  == 'pt-br';

        foreach ($getAlertOptions as $key => $option) {

            if ($notificationTime->$key === 'true') {

                $predefinedAlerts[] = $isPtBr ? $option : $key;
            }
        }

        return $predefinedAlerts;
    }
}

if (!function_exists('formatAlertDays')) {

    function formatAlertDays($recurringDays)
    {

        $recurringMessage = '';

        $numberOfRepeatingDays = count($recurringDays);

        if ($numberOfRepeatingDays == 7) {

            $recurringMessage =  'Todos os dias da semana';
        } else {

            if ($numberOfRepeatingDays > 1) {

                $lastDay = array_pop($recurringDays);

                $recurringMessage =  implode(', ', $recurringDays);

                $recurringMessage .= ' e ' . $lastDay . '.';
            } else {

                $recurringMessage .=   $recurringDays[0];
            }
        }

        return $recurringMessage;
    }
}


if (!function_exists('addDurationOverlapQuery')) {

    function addDurationOverlapQuery(Builder $query, $inputData)
    {

        return $query->where('start', '>=', $inputData['start'])
            ->where('start', '<', $inputData['end'])
            ->orWhere(function ($startOverlapQuery) use ($inputData) {
                $startOverlapQuery->where('end', '>', $inputData['start'])
                    ->where('end', '<=', $inputData['end']);
            })
            ->orWhere(function ($intervalOverlapQuery) use ($inputData) {
                $intervalOverlapQuery->where('start', '<=', $inputData['start'])
                    ->where('end', '>=', $inputData['end']);
            });
    }
}

if (!function_exists('getTaskOccurrencess')) {

    function getTaskOccurrencess($pattern, $query)
    {
        return $query->with('reminder', 'reminder.recurring')->whereHas('reminder', function ($reminderQuery) use ($pattern) {
            $reminderQuery->whereHas('recurring', function ($reminderRecurringQuery) use ($pattern) {
                $reminderRecurringQuery->where($pattern, 'true');
            });
        });
    }
}

if (!function_exists('getNotificationQuery')) {

    function getNotificationQuery($creatorOrParticipant, $query, $userID, $taskID)
    {
        if ($creatorOrParticipant === 'creator') {
            return $query->where('created_by', $userID);
        } else {
            return $query->where('id', $taskID)->whereHas('participants', function ($query) use ($userID,  $taskID) {

                $query->where('user_id', $userID)->where('task_id', $taskID)->where('status', 'accepted');
            });
        };
    }
}


if (!function_exists('getRecurringData')) {


    function getRecurringData($request, $reminder)
    {

        $hasSpecificDay = filled($request->specific_date);

        $specificDate = $hasSpecificDay ? getCarbonDate($request->specific_date) : null;

        return [

            'specific_date' => $request->specific_date,

            'specific_date_weekday' => $hasSpecificDay ? getDayOfWeek($specificDate) : null,

            'sunday' => $request->sunday ?? 'false',

            'monday' => $request->monday ?? 'false',

            'tuesday' => $request->tuesday ?? 'false',

            'wednesday' => $request->wednesday ?? 'false',

            'thursday' => $request->thursday ?? 'false',

            'friday' => $request->friday ?? 'false',

            'saturday' => $request->saturday ?? 'false',

            'reminder_id' => $reminder->id,

        ];
    }
}

if (!function_exists('getPluralOrSingularTime')) {

    function getPluralOrSingularTime($time, $measurementUnit)
    {
        $isSingular = $time == 1 || $time == 60.0;

        $measurementUnitInSingular = rtrim($measurementUnit, 's');

        return $isSingular
            ? "$time $measurementUnitInSingular"
            : "$time $measurementUnit";
    }
}

if (!function_exists('getStartDuration')) {

    function getStartDuration($task, $user)
    {

        return getCarbonTime((
                $task->durations()
                ->where('user_id', $user->id)
                ->where('task_id', $task->id))
                ->first()
                ->start
        );
    }
}

if (!function_exists('logChangeStatusDuration')) {

    function logChangeStatusDuration($logData, $status)
    {
        Log::info('Job HandleDurationsStatus: O status da duração da tarefa pertencente ao usuário' . $logData['user_email'] . ', foi alterado para ' . $status . '. - Duration ID: ' . $logData['duration_id']);
    }
}

if (!function_exists('getLogStatus')) {
    function getLogStatus($status)
    {

        switch ($status) {
            case 'starting':
                return ' irá começar';

            case 'in_progress':
                return ' está sendo realizada';

            case 'finished':
                return ' foi finalizada';

            default:
                return 'status desconhecido';
        }
    }
}

if (!function_exists('logStatusDuration')) {

    function logStatusDuration($logData, $status)
    {
        $statusLog = getLogStatus($status);
        Log::info('Job HandleDurationsStatus: A tarefa referente ao usuário ' . $logData['user_email'] . ',' . $statusLog . ' - Duration ID: ' . $logData['duration_id']);
    }
}

if (!function_exists('logDuration')) {

    function logDuration($logData)
    {
        Log::info("Job HandleDurationsStatus: Horário de início: " . $logData['start']);
        Log::info("Job HandleDurationsStatus: Horário de término: " . $logData['end']);
        Log::info("Job HandleDurationsStatus: Horário atual: " . $logData['now']);
    }
}

if (!function_exists('getNotificationContextSnippet')) {

    function getNotificationTimeContextSnippet($isTask)
    {

        return $isTask
            ? 'Há uma notificação relativa a tarefa em análise programada para'
            : 'Há uma notificação relativa ao lembrete em análise programado para';
    }
}

if (!function_exists('getNotificationContextSnippet')) {

    function getNotificationContextSnippet($isTask)
    {

        return $isTask
            ? 'A tarefa em análise está configurada'
            : 'O lembrete em análise está configurado';
    }
}

if (!function_exists('logNotificationNotScheduledForToday')) {

    function logNotificationNotScheduledForToday($data)
    {

        $isSpecificDate =  $data['has_specific_date'];

        $notificationTime = $data['notification_time'];

        $scheduledDate = $data['scheduled_date'] ?? null;

        $isTask  = !is_null($data['task']);

        $today = getToday();

        $currentDayOfWeek = getDayOfWeek($today, 'pt-br');
        $notificationContext = getNotificationTimeContextSnippet($isTask);

        Log::info('Job NotifyAtCustomTime: ' . $notificationContext . ' para outro dia ' .  ' - NotificationTime ID: ' . $notificationTime->id);
        Log::info('Job NotifyAtCustomTime: Data programada: ' . ($isSpecificDate ? getCarbonDate($scheduledDate)->format('d/m/Y') : formatAlertDays($scheduledDate)));
        Log::info('Job NotifyAtCustomTime: Data atual: ' . ($isSpecificDate ? $today->format('d/m/Y') :  $currentDayOfWeek));
    }
}

if (!function_exists('logNotificationTime')) {

    function logNotificationTime($data)
    {
        $alertTime = $data['alert_time'];

        $isAfterTime = $data['is_after_time'];

        $isBeforeTime =  $data['is_before_time'];

        $isNotificationTime = $data['is_notification_time'];

        $notificationTime = $data['notification_time'];

        $reminder = $notificationTime->reminder;

        $isTask = !is_null($notificationTime->reminder->task);

        $now = $data['now'];

        $contextString = getNotificationTimeContextSnippet($isTask);

        if ($isBeforeTime) {

            Log::info('Job NotifyAtCustomTime: ' . $contextString  . ' hoje - NotificationTime ID: ' . $notificationTime->id);
        } elseif ($isNotificationTime) {

            Log::info('Job NotifyAtCustomTime: ' . $contextString . ' hoje e o horário de notificação é agora');
            return true;
        } else {

            Log::info('Job NotifyAtCustomTime: ' . $contextString . ' hoje, mas o horário de notificação já ocorreu' . ' - NotificationTime ID: ' . $notificationTime->id);

            if (!$isTask && isset($reminder->recurring->specific_date)) {

                $reminder->available = 'false';
                $reminder->save();
            }
        }

        if ($isBeforeTime ||  $isAfterTime) {

            Log::info('Job NotifyAtCustomTime: Horário programado: ' . $alertTime->format('H:i'));
            Log::info('Job NotifyAtCustomTime: Horário atual: ' . $now->format('H:i'));
            return false;
        }
    }
}

if (!function_exists('getDefaultTimeAlert')) {

    function getDefaultTimeAlert($notificationPattern, $start)
    {
        switch ($notificationPattern) {

            case 'half_an_hour_before':
                return $start->copy()->subMinutes(30);

            case 'one_hour_before':
                return  $start->copy()->subMinutes(60);

            case 'two_hours_before':
                return $start->copy()->subMinutes(120);

            case 'one_day_earlier':
                return  $start->copy()->subday();
        }
    }
}

if (!function_exists('getNotificationTimeData')) {

    function getNotificationTimeData($notificationTime, $notificationPattern)
    {

        $now = getCarbonNow();

        $task = $notificationTime->reminder->task;

        $userToNotify = $notificationTime->user;

        $start = !is_null($task)
            ? getStartDuration($task, $userToNotify)
            : $notificationTime->custom_time;

        $alertTime = $notificationPattern === 'custom_time'
            ? getCarbonTime($notificationTime->custom_time)
            : getDefaultTimeAlert($notificationPattern, $start);

        $isBeforeTime =  $now->format('H:i') < $alertTime->format('H:i');

        $isNotificationTime =  $now->format('H:i') == $alertTime->format('H:i');

        $isAfterTime =  $now->format('H:i') > $alertTime->format('H:i');

        return [

            'is_before_time' => $isBeforeTime,

            'is_notification_time' => $isNotificationTime,

            'is_after_time' => $isAfterTime,

            'notification_pattern' => $notificationPattern,

            'notification_time' => $notificationTime,

            'alert_time' => $alertTime,

            'now' => $now

        ];
    }
}

if (!function_exists('getTaskNotificationMessage')) {

    function getTaskNotificationMessage($title, $time, $start)
    {

        $diffInMinutes = $time->diffInMinutes($start);

        $diffInHours = intdiv($diffInMinutes, 60);
        $remainingMinutes = $diffInMinutes % 60;
        $isLessThanAnHour = $diffInMinutes < 60;

        $greaterThanAnHourMessage =
            $remainingMinutes == 0

            ? "A tarefa **\"$title\"**, está programada para iniciar em " .
            getPluralOrSingularTime($diffInHours, 'horas') . " após o envio desta notificação."

            : "A tarefa **\"$title\"**, está programada para iniciar em " .
            getPluralOrSingularTime($diffInHours, 'horas') . " e " .
            getPluralOrSingularTime($remainingMinutes, 'minutos') . " após o envio desta notificação.";

        return  $isLessThanAnHour
            ? "A tarefa **\"$title\"**, está programada para iniciar em " .
            getPluralOrSingularTime($diffInMinutes, 'minutos') . " após o envio desta notificação."

            : $greaterThanAnHourMessage;
    }
}

if (!function_exists('notify')) {

    function notify($notificationTime, $time)
    {

        $userToNotify = $notificationTime->user;

        $task = $notificationTime->reminder->task;

        if (!is_null($task)) {

            $start =  getStartDuration($task, $userToNotify);

            $notificationMessage = getTaskNotificationMessage($task->title, $time, $start);

            $taskData = $task->getAttributes();

            $taskData['start'] = $start;
            $taskData['message'] =  $notificationMessage;

            Mail::to($userToNotify->email)->send(new TaskNotify($taskData));

            Log::info("Job NotifyAtCustomTime: A notificação da tarefa em análise foi enviada para: $userToNotify->email");
        } else {

            $reminder = $notificationTime->reminder;

            Mail::to($userToNotify->email)->send(new ReminderNotify($reminder));
            Log::info("Job NotifyAtCustomTime: O lembrete foi enviado para  $userToNotify->email");

            if (isset($reminder->recurring->specific_date)) {
                $reminder->delete();
            }
        }
    }
}

if (!function_exists('handleDurationStatus')) {

    function handleDurationStatus($task, $now)
    {

        $durations = $task->durations;

        foreach ($durations as $duration) {

            $start =  getCarbonTime($duration->start);
            $end =    getCarbonTime($duration->end);

            $isInProgress = $start->lessThanOrEqualTo($now) && $end->greaterThanOrEqualTo($now);
            $isFinished = $end->lessThan($now);
            $isStarting = $start->greaterThan($now);

            $shouldChangeStatusToFinished =  $duration->status  !==  'finished';
            $shouldChangeStatusToStarting  =  $duration->status  !== 'starting';
            $shouldChangeStatusToInProgress =  $duration->status  !==  'in_progress';

            $logData = [
                'duration_id' => $duration->id,
                'user_email' => $duration->user->email,
                'now' => $now->format('H:i'),
                'start' => $start->format('H:i'),
                'end' => $end->format('H:i'),
            ];

            if ($isInProgress && $shouldChangeStatusToInProgress) {

                $duration->update(['status' => 'in_progress']);
                logStatusDuration($logData, 'in_progress');
            } elseif ($isInProgress) {

                logStatusDuration($logData, 'in_progress');
                logDuration($logData);
            } elseif ($isFinished &&  $shouldChangeStatusToFinished) {

                $duration->update(['status' => 'finished']);
                logStatusDuration($logData, 'finished');
            } elseif ($isFinished) {

                logStatusDuration($logData, 'finished');
                logDuration($logData);
            } elseif ($isStarting && $shouldChangeStatusToStarting) {

                $duration->update(['status' => 'starting']);
                logStatusDuration($logData, 'starting');
            } elseif ($isStarting) {
                logStatusDuration($logData, 'starting');
                logDuration($logData);
            }
        }
    }
}

if (!function_exists('sortStartingFromToday')) {

    function sortStartingFromToday($weekDay, $language = 'en')
    {
        $now = getCarbonNow();

        $today = getDayOfWeek($now, $language);

        $week = array_keys($weekDay);

        $startIndex = array_search($today, $week);

        $partAfterToday = array_slice($week, $startIndex);

        $partBeforeStartIndex = array_slice($week, 0, $startIndex);

        $reorderedDaysOfWeek = array_merge($partAfterToday, $partBeforeStartIndex);

        $reorderedWeekDay = [];

        foreach ($reorderedDaysOfWeek as $day) {

            $reorderedWeekDay[$day] = $weekDay[$day];
        }

        return $reorderedWeekDay;
    }
}

if (!function_exists('getUserTasksBuilder')) {
    function getUserTasksBuilder()
    {
        return  Task::with([

            'participants',
            'reminder',
            'reminder.recurring',
            'durations'

        ])
        ->where('concluded', 'false')
        ->where(function ($query)  {

            $query
            ->where('created_by',auth()->id())
            ->orWhereHas('participants', function ($query)  {

                $query
                ->where('user_id',auth()->id())
                ->where('status', 'accepted');
            });

        });
    }

}

if (!function_exists('getTasksByWeekday')) {

    function getTasksByWeekday()
    {

        $tasks = getUserTasksBuilder();

        $daysOfWeek =getDaysOfWeek();

        $weekDayTasks = [];

        $specificDateTasksBuilder =  (clone $tasks)->whereHas('reminder', function($query){

            $query->whereHas('recurring', function($query){

                $query->whereNotNull('specific_date');
            });

        })->whereHas('durations',function($query){

            $query->where('status','<>','finished');

        });

        $recurringTasksBuilder  = (clone $tasks)->whereHas('reminder',function($query){

            $query->whereHas('recurring', function($query){

                $query->whereNull('specific_date');
            });

        });

        $mergedTasks = $specificDateTasksBuilder->union($recurringTasksBuilder)->get();

            foreach ($daysOfWeek as $dayOfWeek => $dayOfWeekPTBR) {

                $isdayOfWeekToday  =checkIsToday($dayOfWeek);

                $weekDayTasks[$dayOfWeekPTBR] = $mergedTasks->filter(function($task) use ($isdayOfWeekToday, $dayOfWeek) {

                    $duration = getDuration($task);

                    $recurring = $task->reminder->recurring;

                    $isFinished =  $duration->status == "finished";

                    $isValidRecurringTask = !($isdayOfWeekToday && $isFinished);

                    $isRecurringDay = $recurring->$dayOfWeek ==='true';

                    $isSpecificDateDay =  $recurring->specific_date_weekday ===  $dayOfWeek;



                $isOcurrenceTodayDay =   $isSpecificDateDay  || ($isRecurringDay && $isValidRecurringTask );

                return $isOcurrenceTodayDay;

            })->sortBy(function($task){

                $duration = getDuration($task);

                return $duration->start;

            });

            // foreach ($daysOfWeek as $dayOfWeek => $dayOfWeekPTBR) {

            //     $weekDayTasks[$dayOfWeekPTBR] = $mergedTasks->filter(function($task) use ($dayOfWeek) {

            //         $recurring = $task->reminder->recurring;

            //         $isRecurringDay = $recurring->specific_date_weekday ===  $dayOfWeek || $recurring-> $dayOfWeek === 'true';

            //         return $isRecurringDay;

            //     })->sortBy(function($task){

            //         $duration = getDuration($task);

            //         return $duration->start;

            //     });


        }

        return  $weekDayTasks;
    }
}

if (!function_exists('getRemindersByWeekday')) {

    function getRemindersByWeekday($daysOfWeek)
    {
        $weekDayReminders = [];

        $userID = auth()->id();

        foreach ($daysOfWeek as $dayOfWeek => $dayOfWeekPTBR) {

            $weekDayReminders[$dayOfWeekPTBR] = Reminder::with('recurring')->where('reminders.user_id', $userID)->where('available', 'true')

                ->whereHas('recurring', function ($query) use ($dayOfWeek) {

                    $query->where($dayOfWeek, true)->orWhere('specific_date_weekday', $dayOfWeek);
                })->join('notification_times', 'reminders.id', '=', 'notification_times.reminder_id')

                ->select('reminders.*', 'notification_times.custom_time')

                ->orderBy('notification_times.custom_time')

                ->get();
        }

        return array_filter($weekDayReminders, function ($reminders) {

            return $reminders->isNotEmpty();
        });
    }
}

if (!function_exists('getDuration')) {

    function getDuration($task)
    {

        return $task->durations()->where('user_id',  $task->created_by)->where('task_id', $task->id)->first();
    }
}

if (!function_exists('getParticipants')) {
    function getParticipants($task)
    {
        return $task
            ->participants()
            ->where('status', 'accepted')
            ->get();
    }
}

if (!function_exists('getSpecificDayAlerts')) {

    function getSpecificDayAlerts()
    {

        return [

            'starting' => 'A tarefa começará em breve (em menos de 30 minutos)',

            'in_progress' => 'A tarefa já está em andamento',

            'finished' => 'A tarefa está expirada',
        ];
    }
}

if (!function_exists('getRecurringAlerts')) {

    function getRecurringAlerts()
    {

        return [

            'starting' => 'A tarefa está prestes a começar. Se o horário de alerta exceder 30 minutos, você será notificado nas próximas recorrências',

            'in_progress' => 'A tarefa está atualmente em andamento. Você receberá uma nova notificação nas próximas recorrências',

            'finished' => 'A tarefa expirou hoje. A notificação será enviada nas próximas recorrências',
        ];
    }
}

if (!function_exists('getAlertAboutNotificationTime')) {

    function getAlertAboutNotificationTime($task)
    {

        $now = getCarbonNow();

        $duration = getDuration($task);

        $start = getCarbonTime($duration->start);

        $recurring = $task->reminder->recurring;

        $currentDayOfWeek = strtolower($now->format('l'));

        $isRecurrenceToday = $recurring->$currentDayOfWeek === 'true';

        $hasSpecificDate = filled($recurring->specific_date);

        $specificDayAlertMessages = getSpecificDayAlerts();

        $recurringAlertMessages  = getRecurringAlerts();

        if ($hasSpecificDate) {

            switch ($duration->status) {

                case 'starting':

                    if ($now->diffInMinutes($start) < 30) {
                        return $specificDayAlertMessages['starting'];
                    }

                    break;

                case 'in_progress':

                    return $specificDayAlertMessages['in_progress'];

                case 'finished':

                    return $specificDayAlertMessages['finished'];
            }
        } elseif ($isRecurrenceToday) {

            switch ($duration->status) {

                case 'starting':
                    if ($now->diffInMinutes($start) < 30) {

                        return $recurringAlertMessages['starting'];;
                    }
                    break;

                case 'in_progress':

                    return $recurringAlertMessages['in_progress'];;

                case 'finished':

                    return $recurringAlertMessages['finished'];;
            }
        }
    }
}

if (!function_exists('getFilteredTasks')) {

    function getFilteredTasks($request)
    {
        $userID = auth()->id();

        $selectedUserTasksBuilder = Task::with([

            'participants',
            'reminder',
            'reminder.recurring',
            'durations'

        ]);

        if ($request->has('filter') && $request->input('filter') === 'concluded') {

            $selectedUserTasksBuilder = $selectedUserTasksBuilder->where('concluded', 'true')->where(function ($query) use ($userID) {

                $query->where('created_by', $userID)->orWhereHas('participants', function ($query) use ($userID) {
                    $query->where('user_id', $userID)->where('status', 'accepted');
                });
            });

        } elseif ($request->has('filter') && $request->input('filter') === 'created') {

            $selectedUserTasksBuilder =  $selectedUserTasksBuilder->where('concluded', 'false')->where(function ($query) use ($userID) {
                $query->where('created_by', $userID);
            });

        } elseif ($request->has('filter') && $request->input('filter') === 'participating') {
            $selectedUserTasksBuilder  = $selectedUserTasksBuilder->where('concluded', 'false')->whereHas('participants', function ($query) use ($userID) {
                $query->where('user_id', $userID)->where('status', 'accepted');
            });
        }

        return $selectedUserTasksBuilder;
    }
}

if (!function_exists('getTasksForDayAndTime')) {

    function getTasksForDayAndTime($time, $day)
    {
        // dd($day);

        $time = getCarbonTime($time);

        $timePlusOneHour = $time->copy()->addHour()->subSecond();

        $userID = auth()->id();

        $currentDayOfWeek = getDayOfWeek($day);

        return Task::with([
            'participants',
            'reminder',
            'reminder.recurring',
            'durations'

        ])->where('concluded', 'false')->where(function ($query) use ($userID) {

            $query->where('created_by', $userID)->orWhereHas('participants', function ($query) use ($userID) {
                $query->where('user_id', $userID)->where('status', 'accepted');
            });
        })->whereHas('reminder', function ($query) use ($day, $currentDayOfWeek) {

            $query->whereHas('recurring', function ($query) use ($day, $currentDayOfWeek) {

                $query->where(function ($query) use ($day, $currentDayOfWeek) {

                    $query->where('specific_date', $day->format('Y-m-d'))->where('specific_date_weekday', $currentDayOfWeek);
                })->orWhere($currentDayOfWeek, 'true');
            });
        })->whereHas('durations', function ($query) use ($userID, $time, $timePlusOneHour) {

            $query->where('user_id', $userID)->whereBetween('start', [$time->format('H:i:s'), $timePlusOneHour->format('H:i:s')]);
        })->get();
    }
}

if (!function_exists('getlabelOverviewForDay')) {

    function getlabelOverviewForDay($day, $tasksExist)
    {
        $labelOverview = "";

        $weekdayInPortuguese = getDayOfWeek($day, 'pt-br');
        if (!$tasksExist) {

            $labelOverview = $day->isToday()
                ? "<span class='fs-5 poppins-extralight'>Nenhuma tarefa para hoje.</span> " . getFormatedDateBR($day) . ", <span class='fs-5 poppins-extralight'> " . $weekdayInPortuguese . "</span> "
                : "<span class='fs-5 poppins-extralight'>Nenhuma tarefa para </span> " . getFormatedDateBR($day) . ", <span class='fs-5 poppins-extralight'> " . $weekdayInPortuguese . "</span> ";
        } elseif ($tasksExist != null) {

            $formatedDate = getFormatedDateBR($day);

            $labelOverview = $day->isToday()
                ? "<span class='fs-5 poppins-extralight'>Hoje, </span>  " . $formatedDate . ". <span class='fs-5 poppins-extralight'> " . ucfirst($weekdayInPortuguese) . "</span> "
                : "<span class='fs-5 poppins-extralight'>" . ucfirst($weekdayInPortuguese) . " . </span>  " . "$formatedDate";
        } else {
            $labelOverview = $day->isToday() ? "Agenda de hoje, " . getFormatedDateBR($day) : "Agenda de " . getFormatedDateBR($day) . " . " . ucfirst($weekdayInPortuguese);
        }

        return $labelOverview;
    }
}

if (!function_exists('getPaneldateLabel')) {

    function getPaneldateLabel($tasksExist)
    {
        $panelLabel = "";

        $day = getCarbonNow();

        $weekdayInPortuguese = getDayOfWeek($day, 'pt-br');

        if (!$tasksExist) {
            $panelLabel =  "<span class='fs-3 poppins-extralight'>Nenhuma tarefa para hoje. </span> " . getFormatedDateBR($day);
        } else {

            $formatedDate = getFormatedDateBR($day);

            $panelLabel = "<span class='fs-4'>" . ucfirst($weekdayInPortuguese) . ", </span> "  . $formatedDate;
        }

        return $panelLabel;
    }
}

if (!function_exists('getSelectedUserTasksBuilder')) {

    function getSelectedUserTasksBuilder($selectedDate)
    {
        $currentUserID =  auth()->id();

        $carbonDate = getCarbonDate($selectedDate);

        $weekdayOfSelectDate = getDayOfWeek($carbonDate);

        $selectedUserTaskBuilder =   Task::with([

            'participants',
            'reminder',
            'reminder.recurring',
            'durations'

        ])->where('concluded', 'false')->where(function ($query) use ($currentUserID) {

            $query->where('created_by', $currentUserID)->orWhereHas('participants', function ($query) use ($currentUserID) {
                $query->where('user_id', $currentUserID)->where('status', 'accepted');
            });
        })->whereHas('reminder', function ($query) use ($selectedDate, $weekdayOfSelectDate) {

            $query->whereHas('recurring', function ($query) use ($selectedDate, $weekdayOfSelectDate) {

                $query->where(function ($query) use ($selectedDate, $weekdayOfSelectDate) {

                    $query->where('specific_date', $selectedDate)->where('specific_date_weekday', $weekdayOfSelectDate);
                })->orWhere($weekdayOfSelectDate, 'true');
            });
        });

        return  $selectedUserTaskBuilder;
    }
}

if (!function_exists('getTasksByStartTime')) {

    function getTasksByStartTime($blockTime)
    {
        $start = getCarbonTime($blockTime);
        $end =  $start->copy()->addHour()->subSecond();

        $today = getCarbonNow()->startOfDay();

        $dayOfWeek = getDayOfWeek($today);

        $formatedToday = $today->format('Y-m-d');

        return Task::with([
            'reminder',
            'reminder.recurring',
            'durations'

        ])->where('concluded', 'false')->whereHas('reminder', function ($query) use ($formatedToday, $dayOfWeek) {

            $query->whereHas('recurring', function ($query) use ($formatedToday, $dayOfWeek) {

                $query->where('specific_date', $formatedToday)->where('specific_date_weekday', $dayOfWeek)->orWhere($dayOfWeek, 'true');

            });

        })->whereHas('durations', function ($query) use ($start, $end) {

            $query->whereBetween('start', [$start->format('H:i:s'), $end->format('H:i:s')]);

        })->get();
    }
}

if (!function_exists('sortByStart')) {

    function sortByStart($builder)
    {
        $collection = $builder->get();
        $currentUserID =  auth()->id();

        return $collection->sortBy(function ($task) use ($currentUserID) {
            return $task->durations->where('user_id', $currentUserID)->first()->start ?? '23:59:59';
        });
    }
}


if (!function_exists('getHourForBlock')) {

    function getHourForBlock(int $index)
    {

        return str_pad($index, 2, '0', STR_PAD_LEFT) . ':00';
    }
}

if (!function_exists('getMinutesSinceStartOfDay')) {

    function getMinutesSinceStartOfDay()
    {
        $now = getCarbonNow();
        $now = getCarbonTime('00:00');

        $startOfDay = $now->copy()->startOfDay();

        return $startOfDay->diffInMinutes($now);
    }
}

if (!function_exists('checkExpiration')) {

    function checkExpiration($stringTime)
    {
        $now = getCarbonNow();
        $expiresTime = getCarbonTime($stringTime);

        return $now->greaterThan($expiresTime);
    }
}

