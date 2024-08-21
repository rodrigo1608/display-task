<?php

use App\Models\NotificationTime;
use App\Models\Task;
use App\Mail\TaskNotify;
use App\Mail\ReminderNotify;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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

            // rodrigo
            // @dd($profilePictureName);

            return  $image->storeAs('profile_pictures', $profilePictureName);
        }

        return 'default_user_icon.jpg';
    }
}

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


// if (!function_exists('checkValidDayAlert')) {

//     function checkValidDayAlert($date)
//     {
//         $today = getToday();

//         $dayOfWeekToday = getDayOfWeek($today);

//         if ($date instanceof Carbon) {

//             $dayBeforeSpecificDate = $date->copy()->subDay();

//             return   $dayBeforeSpecificDate->isToday() || $date->isToday();
//         } else {

//             $carbonDate = Carbon::parse($date);

//             $dayBeforeRecurringDate = $carbonDate->subDay()->format('l');

//             return $dayOfWeekToday ===  strtolower($dayBeforeRecurringDate) || $dayOfWeekToday === $date;
//         }
//     }
// }


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

if (!function_exists('getSelectedNotificationTimes')) {

    function getSelectedNotificationTimes($notificationTime)
    {

        $defaultTimes = [

            'half_an_hour_before',

            'one_hour_before',

            'two_hours_before',

            'one_day_earlier',
        ];

        $selectedTimes = [];

        foreach ($defaultTimes as $defaultTime) {

            if ($notificationTime->$defaultTime === 'true') {

                $selectedTimes[] = $defaultTime;
            }
        }
        return $selectedTimes;
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

if (!function_exists('getPredefinedAlerts')) {

    function getPredefinedAlerts($notificationTime, $language = 'en')
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

if (!function_exists('getRecurringMessage')) {

    function getRecurringMessage($recurring)
    {
        if (is_null($recurring->specific_date)) {

            $recurringMessage = '';

            // $daysOfWeek = getDaysOfWeekInPortuguese();

            // $repeatingDays = getRepeatingDays($daysOfWeek, $recurring);

            $repeatingDays = getRepeatingDays($recurring, 'pt-br');

            $numberOfRepeatingDays = count($repeatingDays);

            if ($numberOfRepeatingDays == 7) {

                $recurringMessage = 'Todos os dias';
            } else {

                if ($numberOfRepeatingDays > 1) {

                    $lastDay = array_pop($repeatingDays);

                    $recurringMessage = 'Irá se repetir a cada ' . implode(', ', $repeatingDays);

                    $recurringMessage .= ' e ' . $lastDay;
                } else {

                    $recurringMessage .=    ($repeatingDays[0]) === "sábado" || ($repeatingDays[0]) === 'domingo'
                        ? 'Todos os ' . ($repeatingDays[0]) . 's'
                        : 'Todas as ' . $repeatingDays[0] . 's';
                }
            }
        } else {

            $date = getCarbonDate($recurring->specific_date);

            $dayOfWeekInPortuguese = getDayOfWeek($date, 'pt-br');

            $formatedDate = '<strong>' . Carbon::parse($recurring->specific_date)->format('d/m/Y') . '</strong>';
            $recurringMessage = "Ocorrerá exclusivamente no dia: $formatedDate, $dayOfWeekInPortuguese.";
        }

        return $recurringMessage;
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

            // $daysOfWeek = getDaysOfWeekInPortuguese();

            // $repeatingDays = getRepeatingDays($daysOfWeek, $recurring);

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

            $formatedDate =   Carbon::parse($recurring->specific_date)->format('d/m/Y');

            $recurringMessage = $notificationSnippetContext . ' para ocorrer exclusivamente no dia: ' . $formatedDate . ', ' . $dayOfWeekInPortuguese;
        }

        return $recurringMessage;
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

if (!function_exists('getRecurringTask')) {

    function getRecurringTask(Builder $query, $recurrencePattern, $inputData = null)
    {
        //rodrigo
        // dd($inputData);

        $hasSpecificDate = !is_null($inputData['specific_date']);

        $specificDate = $inputData['specific_date'];

        $date = getCarbonDate($specificDate);

        $dayOfWeek = $hasSpecificDate ? getDayOfWeek($date) : null;

        return $hasSpecificDate
            ?
            $query->whereHas('recurring', function ($taskReminderRecurringQuery) use ($specificDate, $dayOfWeek) {
                // dd($taskReminderRecurringQuery);
                $taskReminderRecurringQuery->where('specific_date',  $specificDate)->orWhere($dayOfWeek, "true");
            })
            :
            $query->whereHas('recurring', function ($taskReminderRecurringQuery) use ($recurrencePattern) {
                // dd($recurrencePattern);
                $taskReminderRecurringQuery->where('specific_date_weekday', $recurrencePattern)->orWhere($recurrencePattern, 'true');
            });
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

if (!function_exists('getConflitingTaskData')) {

    function getConflitingTaskData($conflitingTask)
    {

        $conflitingTaskData =  $conflitingTask->toArray();

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

if (!function_exists('getConflictingTask')) {

    function getConflictingTask($inputData, $recurrencePattern, $currentTaskID = null)
    {
        $userID = auth()->id();

        // Primeiramente, a consulta deve ignorar a tarefa que já foi criada para, no caso de algum usuário aceitá-la, não gerar conflito de sobreposição
        $conflictingTaskBuilder =  Task::with(['reminder.recurring', 'participants'])->where('id', '!=', $currentTaskID)

            // Consulta que verifica se a tarefa pertence ao usuário logado ou se o usuário está participando de alguma tarefa
            ->where(function ($query) use ($userID) {

                $query->where('created_by', $userID)->orWhereHas('participants', function ($query) use ($userID) {

                    $query->where('user_id', $userID)->where('status', 'accepted');
                });

                // Como as recorrências são vinculadas aos lembretes, a consulta passará pela tabela reminders antes de acessar a tabela recurrings
            })->whereHas('reminder', function ($taskReminderQuery) use ($recurrencePattern, $inputData) {

                // Método que lida com a lógica das recorrências
                getRecurringTask($taskReminderQuery,  $recurrencePattern, $inputData);

                // Depois que a recorrência foi verificada, o código abaixo é responsável por verificar se as durações estão se sobrepondo
            })->whereHas('durations', function ($taskRecurringsDurtionQuery) use ($inputData) {
                addDurationOverlapQuery($taskRecurringsDurtionQuery, $inputData);
            });

        // dd($conflictingTaskBuilder->toSql(), $conflictingTaskBuilder->getBindings());

        $conflictingTask = $conflictingTaskBuilder->first();

        $hasConflictingTask = $conflictingTaskBuilder->exists();

        //rodrigo
        // dd($hasConflictingTask);

        if ($hasConflictingTask) {

            $conflitingTaskData = getConflitingTaskData($conflictingTask);

            //rodrigo
            // dd($conflitingTaskData);

            session()->flash('conflictingTask',  $conflitingTaskData);

            return redirect()->back()->withErrors([

                'conflictingDuration' =>
                $conflictingTask->title,

            ])->withInput();
        }
    }
}

if (!function_exists('getRecurringTasks')) {

    function getRecurringTasks($pattern, $query)
    {
        return $query->with('reminder', 'reminder.recurring')->whereHas('reminder', function ($reminderQuery) use ($pattern) {
            $reminderQuery->whereHas('recurring', function ($reminderRecurringQuery) use ($pattern) {
                $reminderRecurringQuery->where($pattern, 'true');
            });
        });
    }
}

if (!function_exists('getNotificationQuery')) {

    function getNotificationQuery($creatorOrParticipant, $query, $currentUserID, $taskID)
    {
        if ($creatorOrParticipant === 'creator') {
            return $query->where('created_by', $currentUserID);
        } else {
            return $query->where('id', $taskID)->whereHas('participants', function ($query) use ($currentUserID,  $taskID) {

                $query->where('user_id', $currentUserID)->where('task_id', $taskID)->where('status', 'accepted');
            });
        };
    }
}

if (!function_exists('getCurrentUserTasks')) {

    function getCurrentUserTasks($creatorOrParticipant, $currentUserID, $taskID)
    {
        return NotificationTime::whereHas('reminder', function ($query) use ($creatorOrParticipant, $currentUserID, $taskID) {
            getNotificationQuery($creatorOrParticipant, $query, $currentUserID, $taskID);
        })->first()->get();
    }
}

if (!function_exists('getRecurringData')) {

    function getRecurringData($request, $isSpecificDayPattern, $reminder)
    {
        $specificDate = getCarbonDate($request->specific_date);

        return [

            'specific_date' => $request->specific_date ?? null,

            'specific_date_weekday' => $isSpecificDayPattern ? getDayOfWeek($specificDate) : null,

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

// if (!function_exists('checkIfNotificationTime')) {

//     function checkIfNotificationTime($notificationTime)
//     // function getAlertOptions()
//     {
//         $now = getCarbonNow();

//         $isCustomTime = !is_null($notificationTime->custom_time);

//         if ($isCustomTime) {

//             $customTime = getCarbonTime($notificationTime->custom_time);

//             return ($customTime->format('H:i:s') == $now->format('H:i:s'));
//         } else {

//             $predefinedAlerts = getPredefinedAlerts($notificationTime);

//             $task = $notificationTime->

//             $start =  getStartDuration($task, $userID);



//             foreach ($predefinedAlerts as $alert) {

//                 if ($alert == 'half_an_hour_before') {

//                     $halfAnHourBefore = $start->copy()->subMinutes(30);

//                     return $halfAnHourBefore->format('H:i') === $now->format('H:i');
//                 } elseif ($alert == 'one_hour_before') {
//                     $oneHourBefore = $start->copy()->subMinutes(60);

//                     return $oneHourBefore->format('H:i') === $now->format('H:i');
//                 } elseif ($alert == 'two_hours_before') {
//                     $twoHoursBefore = $start->copy()->subMinutes(120);

//                     return $twoHoursBefore->format('H:i') === $now->format('H:i');
//                 } elseif ($alert == 'one_day_earlier') {

//                     $oneDayEarlier = $start->copy()->subDay();

//                     return $oneDayEarlier->format('H:i') === $now->format('H:i');
//                 }
//             }
//         }

//         // $isNotificationTime = $specificDate->isToday() && ($now !== $customTime);

//         return [

//             'half_an_hour_before' => 'Meia hora antes',

//             'one_hour_before' => 'Uma hora antes',

//             'two_hours_before' => 'Duas horas antes',

//             'one_day_earlier' => 'Um dia antes'

//         ];
//     }
// }

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
        // dd($notificationPattern);

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
        }
    }
}

// if (!function_exists('logNotify')) {

//     function logNotify($data)
//     {
//         $notificationTime = $data['notification_time'];

//         $now = getCarbonNow();

//         $customTime = $notificationTime->custom_time;

//         $isBeforeTime = null;

//         $isNotificationTime = null;

//         $isAfterTime = null;

//         // $start = getStartDuration($task, $userToNotify->id);

//         if (is_null($customTime)) {

//             $selectedNotificationTimes = getSelectedNotificationTimes($notificationTime);

//             $task = $notificationTime->reminder->task;

//             $userID = $notificationTime->user->id;

//             $start = getStartDuration($task, $userID);

//             foreach ($selectedNotificationTimes as $selectedTime) {

//                 // $isToday = checkIsToday($specificDate);


//                 $time  = getCustomTimeAlert($selectedTime, $start);

//                 $isBeforeTime = $isToday && ($now->format('H:i') < $time->format('H:i'));

//                 $isNotificationTime = $isToday && ($now->format('H:i') == $time->format('H:i'));

//                 //aqui
//                 $isAfterTime = $isToday && ($now->format('H:i') > $time->format('H:i'));
//             }
//         } else {

//             $time = getCarbonTime($notificationTime->custom_time);

//             $isBeforeTime = $isToday && ($now->format('H:i') < $time->format('H:i'));

//             $isNotificationTime = $isToday && ($now->format('H:i') == $time->format('H:i'));

//             $isAfterTime = $isToday && ($now->format('H:i') > $time->format('H:i'));
//         }

//         $notifyTimeData = [

//             'is_before_time' => $isBeforETime,
//             'is_notification_time' => $isNotificationTime,
//             'is_after_time' => $isAfteRTime,
//             'notification_time' => $notificationTime,
//             'time' => $time,
//             'now' => $now

//         ];

//         if ($isBeforeTime) {

//             logNotificationTime($notifyTimeData);
//             return false;
//         } elseif ($isAfterTime) {

//             logNotificationTime($notifyTimeData);
//             return false;
//         } elseif ($isNotificationTime) {

//             logNotificationTime($notifyTimeData);
//             return true;
//         }
//     }
// }


// if (!function_exists('logNotify')) {

//     function logNotify($data)
//     {
//         $notificationTime = $data['notification_time'];

//         $now = getCarbonNow();

//         $customTime = $notificationTime->custom_time;

//         $isBeforeTime = null;

//         $isNotificationTime = null;

//         $isAfterTime = null;

//         // $start = getStartDuration($task, $userToNotify->id);

//         if (is_null($customTime)) {

//             $selectedNotificationTimes = getSelectedNotificationTimes($notificationTime);

//             $task = $notificationTime->reminder->task;

//             $userID = $notificationTime->user->id;

//             $start = getStartDuration($task, $userID);

//             foreach ($selectedNotificationTimes as $selectedTime) {

//                 // $isToday = checkIsToday($specificDate);


//                 $time  = getCustomTimeAlert($selectedTime, $start);

//                 $isBeforeTime = $isToday && ($now->format('H:i') < $time->format('H:i'));

//                 $isNotificationTime = $isToday && ($now->format('H:i') == $time->format('H:i'));

//                 //aqui
//                 $isAfterTime = $isToday && ($now->format('H:i') > $time->format('H:i'));
//             }
//         } else {

//             $time = getCarbonTime($notificationTime->custom_time);

//             $isBeforeTime = $isToday && ($now->format('H:i') < $time->format('H:i'));

//             $isNotificationTime = $isToday && ($now->format('H:i') == $time->format('H:i'));

//             $isAfterTime = $isToday && ($now->format('H:i') > $time->format('H:i'));
//         }

//         $notifyTimeData = [

//             'is_before_time' => $isBeforETime,
//             'is_notification_time' => $isNotificationTime,
//             'is_after_time' => $isAfteRTime,
//             'notification_time' => $notificationTime,
//             'time' => $time,
//             'now' => $now

//         ];

//         if ($isBeforeTime) {

//             logNotificationTime($notifyTimeData);
//             return false;
//         } elseif ($isAfterTime) {

//             logNotificationTime($notifyTimeData);
//             return false;
//         } elseif ($isNotificationTime) {

//             logNotificationTime($notifyTimeData);
//             return true;
//         }
//     }
// }


// if (!function_exists('logDefaultNotificationTime')) {

//     function logDefaultNotificationTime($data, $isToday)
//     {
//         $notificationTime = $data['notification_time'];

//         $selectedNotificationTimes = getSelectedNotificationTimes($notificationTime);

//         $task = $notificationTime->reminder->task;

//         $userID = $notificationTime->user->id;

//         $start = getStartDuration($task, $userID);

//         foreach ($selectedNotificationTimes as $selectedTime) {

//             $time  = getDefaultTimeAlert($selectedTime, $start);

//             $isBeforeTime = $isToday && ($now->format('H:i') < $time->format('H:i'));

//             $isNotificationTime = $isToday && ($now->format('H:i') == $time->format('H:i'));

//             $isAfterTime = $isToday && ($now->format('H:i') > $time->format('H:i'));
//         }
//     }
// }

// if (!function_exists('logCustomNotificationTime')) {

//     function logCustomNotificationTime($data, $isToday)
//     {
//         $notificationTime = $data['notification_time'];

//         $now = getCarbonNow();

//         $customTime = getCarbonTime($notificationTime->custom_time);

//         $isBeforeTime = null;

//         $isAfterTime = null;

//         // $start = getStartDuration($task, $userToNotify->id);

//         $time = getCarbonTime($notificationTime->custom_time);

//         $isBeforeTime = $isToday && ($now->format('H:i') < $time->format('H:i'));

//         $isNotificationTime = $isToday && ($now->format('H:i') == $time->format('H:i'));

//         $isAfterTime = $isToday && ($now->format('H:i') > $time->format('H:i'));

//         $notifyTimeData = [

//             'is_before_time' => $isBeforeTime,
//             'is_notification_time' => $isNotificationTime,
//             'is_after_time' => $isAfterTime,
//             'notification_time' => $notificationTime,
//             'time' => $customTime,
//             'now' => $now

//         ];

//         if ($isBeforeTime) {

//             logNotificationTime($notifyTimeData, 'custom_time');
//             return false;
//         } elseif ($isAfterTime) {

//             logNotificationTime($notifyTimeData, 'custom_time');
//             return false;
//         } elseif ($isNotificationTime) {

//             logNotificationTime($notifyTimeData, 'custom_time');
//             return true;
//         }
//     }
// }




// if ($isBeforeTime) {

//     logNotificationTime($notifyTimeData, 'custom_time');
//     return false;
// } elseif ($isAfterTime) {

//     logNotificationTime($notifyTimeData, 'custom_time');
//     return false;
// } elseif ($isNotificationTime) {

//     logNotificationTime($notifyTimeData, 'custom_time');
//     return true;
// }

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

// $notificationTime = $data['notification_time'];

// $customTime = $notificationTime->custom_time;

// $isToday = checkIsToday()

// if (is_null($customTime)) {

//     $selectedNotificationTimes = getSelectedNotificationTimes($data['notification_time']);

//     foreach ($selectedNotificationTimes as $selectedTime) {

//         dd($selectedTime);
//     }
// } else {

//     $isBeforeCustomTime = $isToday && ($now->format('H:i') < $time->format('H:i'));

//     $isNotificationTime = $isToday && ($now->format('H:i') == $time->format('H:i'));

//     $isAfterCustomTime = $isToday && ($now->format('H:i') > $time->format('H:i'));
// }


// dd($selectedNotificationTimes);

// $now = getCarbonNow();

// $isBeforeCustomTime = null;

// $isNotificationTime = null;

// $isAfterCustomTime = null;

// $isToday = checkIsToday($now);
