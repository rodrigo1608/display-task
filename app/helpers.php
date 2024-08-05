<?php

use Carbon\Carbon;
use App\Models\Task;
use App\Models\NotificationTime;
use Illuminate\Database\Eloquent\Builder;

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
        $carbonDate = getCarbonDate($date);

        $dayName = strtolower($carbonDate->englishDayOfWeek);

        $daysOfWeek = getDaysOfWeek();

        $isPTBR = $language == 'pt-br';

        return $isPTBR ? $daysOfWeek[$dayName] : $dayName;
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

                $recurringMessage = 'Todos os dias.';
            } else {

                if ($numberOfRepeatingDays > 1) {

                    $lastDay = array_pop($repeatingDays);

                    $recurringMessage = 'Irá se repetir a cada ' . implode(', ', $repeatingDays);

                    $recurringMessage .= ' e ' . $lastDay . '.';
                } else {

                    $recurringMessage .=    ($repeatingDays[0]) === "sábado" || ($repeatingDays[0]) === 'domingo'
                        ? 'Todos os ' . ($repeatingDays[0]) . 's'
                        : 'Todas as ' . $repeatingDays[0] . 's';
                }
            }
        } else {

            $dayOfWeekInPortuguese = getDayOfWeek($recurring->specific_date, 'pt-br');

            $formatedDate = '<strong>' . Carbon::parse($recurring->specific_date)->format('d/m/Y') . '</strong>';
            $recurringMessage = "Ocorrerá exclusivamente no dia: $formatedDate, $dayOfWeekInPortuguese.";
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

        $date = $inputData['specific_date'];

        $dayOfWeek = $hasSpecificDate ? getDayOfWeek($date) : null;

        return $hasSpecificDate
            ?
            $query->whereHas('recurring', function ($taskReminderRecurringQuery) use ($date, $dayOfWeek) {
                // dd($taskReminderRecurringQuery);
                $taskReminderRecurringQuery->where('specific_date', $date)->orWhere($dayOfWeek, "true");
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

// if (!function_exists('getNotificationTime')) {

//     function getNotificationTime($creatorOrParticipant, $currentUserID, $taskID)
//     {
//         return NotificationTime::whereHas('reminder', function ($query) use ($creatorOrParticipant, $currentUserID, $taskID) {
//             getNotificationQuery($creatorOrParticipant, $query, $currentUserID, $taskID);
//         })->first()->get();
//     }
// }

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

        return [

            'specific_date' => $request->specific_date ?? null,

            'specific_date_weekday' => $isSpecificDayPattern ? getDayOfWeek($request->specific_date) : null,

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

if (!function_exists('getNotificationTimes')) {

    function getNotificationTimes($notificationPattern)
    {
        $isASpecificNotificationtime = $notificationPattern == 'custom_time';

        return $isASpecificNotificationtime
            ?
            NotificationTime::with(['user', 'reminder', 'reminder.task'])->whereNotNull('custom_time')->get()
            :
            NotificationTime::with(['user', 'reminder', 'reminder.task'])->where($notificationPattern, 'true')->get();
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

if (!function_exists('getStartDuration')) {

    function getStartDuration($task, $userID)
    {

        return getCarbonTime((
                $task->durations()
                ->where('user_id', $userID)
                ->where('task_id', $task->id))
                ->first()
                ->start
        );
    }
}

if (!function_exists('checkIfNotificationTimeIsNow')) {

    function checkIfNotificationTimeIsNow($notificationTime, $start = null)
    // function getAlertOptions()
    {
        $now = getCarbonNow();

        $isCustomTime = !is_null($notificationTime->custom_time);

        if ($isCustomTime) {

            $customTime = getCarbonTime($notificationTime->custom_time);

            return ($customTime->format('H:i:s') == $now->format('H:i:s'));
        } else {

            $predefinedAlerts = getPredefinedAlerts($notificationTime);

            $start = getCarbonTime($start);

            foreach ($predefinedAlerts as $alert) {

                if ($alert == 'half_an_hour_before') {

                    $halfAnHourBefore = $start->copy()->subMinutes(30);

                    return $halfAnHourBefore->format('H:i') === $now->format('H:i');
                } elseif ($alert == 'one_hour_before') {
                    $oneHourBefore = $start->copy()->subMinutes(60);

                    return $oneHourBefore->format('H:i') === $now->format('H:i');
                } elseif ($alert == 'two_hours_before') {
                    $twoHoursBefore = $start->copy()->subMinutes(120);

                    return $twoHoursBefore->format('H:i') === $now->format('H:i');
                } elseif ($alert == 'one_day_earlier') {

                    $oneDayEarlier = $start->copy()->subDay();

                    return $oneDayEarlier->format('H:i') === $now->format('H:i');
                }
            }
        }

        // $isNotificationTime = $specificDate->isToday() && ($now !== $customTime);

        return [

            'half_an_hour_before' => 'Meia hora antes',

            'one_hour_before' => 'Uma hora antes',

            'two_hours_before' => 'Duas horas antes',

            'one_day_earlier' => 'Um dia antes'

        ];
    }
}
