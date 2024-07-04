<?php

use Carbon\Carbon;
use App\Models\Task;
use Illuminate\Database\Eloquent\Builder;

if (!function_exists('human_case')) {

    function getFormatedTelephone($user)
    {
        return  $user->telephone = '(' . substr($user->telephone, 0, 2) . ') ' . substr($user->telephone, 2, 1) . ' ' . substr($user->telephone, 3);
    }
}

if (!function_exists('getDaysOfWeekInPortuguese')) {

    function getDaysOfWeekInPortuguese()
    {
        return [

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

    function getRepeatingDays($daysOfWeek, $recurring)
    {
        $repeatingDays = [];

        foreach ($daysOfWeek as $key => $day) {

            if ($recurring->$key === 'true') {

                $repeatingDays[] = $day;
            }
        }
        return $repeatingDays;
    }
}

if (!function_exists('getRecurringMessage')) {

    function getRecurringMessage($recurring)
    {
        if (is_null($recurring->specific_date)) {

            $recurringMessage = '';

            $daysOfWeek = getDaysOfWeekInPortuguese();

            $repeatingDays = getRepeatingDays($daysOfWeek, $recurring);

            $numberOfRepeatingDays = count($repeatingDays);

            //rodrigo
            // dd($repeatingDays);

            if ($numberOfRepeatingDays == 7) {

                $recurringMessage = 'Todos os dias.';
            } else {

                // rodrigo
                // dd($recurringMessage);

                if ($numberOfRepeatingDays > 1) {

                    $lastDay = array_pop($repeatingDays);

                    $recurringMessage = 'Irá se repetir a cada ' . implode(', ', $repeatingDays);

                    $recurringMessage .= ' e ' . $lastDay . '.';
                } else {

                    $recurringMessage .= 'Todas as ' . $repeatingDays[0] . 's';
                }
            }
        } else {

            $weekDaysInPortuguese = getDaysOfWeekInPortuguese();

            $weekday = $weekDaysInPortuguese[getWeekDayName($recurring->specific_date)];

            $formatedDate = '<strong>' . Carbon::parse($recurring->specific_date)->format('d/m/Y') . '</strong>';
            $recurringMessage = "Ocorrerá exclusivamente no dia: $formatedDate, $weekday.";
        }

        return $recurringMessage;
    }
}

if (!function_exists('getParticipantsEmail')) {

    function getParticipantsEmail($request)
    {

        $participants = [];

        foreach ($request->all() as $key => $value) {
            if (strpos($key, 'participant') === 0) {
                $participants[] = $value;
            }
        }

        return   $participants;
    }
}

if (!function_exists('getWeekDayName')) {

    function getWeekDayName($date)
    {
        $carbonDate = Carbon::parse($date);

        $dayName = strtolower($carbonDate->englishDayOfWeek);

        return $dayName;
    }
}

if (!function_exists('getRecurringTask')) {

    function getRecurringTask(Builder $query, $recurrencePattern, $inputData = null)
    {
        //rodrigo
        // dd($inputData);

        $weekDayOfSpecificDate = null;

        $hasSpecificDate = $inputData['specific_date'] !== null;

        $date = $inputData['specific_date'];

        $weekDayOfSpecificDate = $hasSpecificDate ? getWeekDayName($date) : null;

        // dd($date);

        return $hasSpecificDate
            ?
            $query->whereHas('recurring', function ($taskReminderRecurringQuery) use ($date, $weekDayOfSpecificDate) {
                // dd($taskReminderRecurringQuery);
                $taskReminderRecurringQuery->where('specific_date', $date)->orWhere($weekDayOfSpecificDate, "true");
            })
            :
            $query->whereHas('recurring', function ($taskReminderRecurringQuery) use ($recurrencePattern, $weekDayOfSpecificDate) {
                // dd($recurrencePattern);
                $taskReminderRecurringQuery->where('specific_date_weekday',   $recurrencePattern)->orWhere($recurrencePattern, 'true');
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

if (!function_exists('getTaskInArray')) {

    function getTaskInArray($conflitingTask)
    {

        $conflitingTaskToArray =  $conflitingTask->toArray();

        $conflitingTaskToArray['owner'] = $conflitingTask->creator->name . ' ' . $conflitingTask->creator->lastname;

        $conflitingTaskToArray['owner_telehpone'] =  getFormatedTelephone($conflitingTask->creator);

        $conflitingTaskToArray['owner_email'] =  $conflitingTask->creator->email;

        $conflictingDuration =  $conflitingTask->durations->first();

        $conflitingTaskToArray['start'] = date('H:i', strtotime($conflictingDuration->start));

        $conflitingTaskToArray['end'] =  date('H:i', strtotime($conflictingDuration->end));

        $conflitingTaskToArray['recurringMessage'] = getRecurringMessage($conflitingTask->reminder->recurring);

        return   $conflitingTaskToArray;
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

        // dd($inputData);

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

            $conflictingTaskInArray = getTaskInArray($conflictingTask);

            //rodrigo
            // dd($conflictingTaskInArray);

            session()->flash('conflictingTask',  $conflictingTaskInArray);

            return redirect()->back()->withErrors([

                'conflictingDuration' =>
                $conflictingTask->title,

            ])->withInput();
        }
    }
}
