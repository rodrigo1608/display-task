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

    function getRecurringTask(Builder $query, $recurrencePattern, $specificDate = null)
    {
        $weekDayOfSpecificDate = null;

        $hasSpecificDate = $specificDate !== null;


        if ($hasSpecificDate) {

            $weekDayOfSpecificDate =  getWeekDayName($specificDate);
        }

        return $specificDate !== null
            ?
            $query->whereHas('recurring', function ($taskReminderRecurringQuery) use ($specificDate,    $weekDayOfSpecificDate) {

                $taskReminderRecurringQuery->where('specific_date', $specificDate)->orWhere($weekDayOfSpecificDate, 'true');
            })
            :
            $query->whereHas('recurring', function ($taskReminderRecurringQuery) use ($recurrencePattern) {

                $taskReminderRecurringQuery->where('specific_date_weekday',   $recurrencePattern)->orWhere($recurrencePattern, 'true');
            });
    }
}

if (!function_exists('addDurationOverlapQuery')) {

    function addDurationOverlapQuery(Builder $query, $request)
    {
        return $query->where('start', '>=', $request->start)
            ->where('start', '<', $request->end)
            ->orWhere(function ($startOverlapQuery) use ($request) {
                $startOverlapQuery->where('end', '>', $request->start)
                    ->where('end', '<=', $request->end);
            })
            ->orWhere(function ($intervalOverlapQuery) use ($request) {
                $intervalOverlapQuery->where('start', '<=', $request->start)
                    ->where('end', '>=', $request->end);
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

if (!function_exists('getConflictingTask')) {

    function getRecurrencePatterns($request)
    {
        return array_filter(['specific_date', 'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'], function ($pattern) use ($request) {
            return array_key_exists($pattern, $request->all()) && $request->{$pattern} !== null;
        });
    }
}

if (!function_exists('getConflictingTask')) {

    function getConflictingTask($request, $recurrencePattern)
    {

        $userID = auth()->id();

        $conflictingTaskBuilder =  Task::with(['reminder.recurring', 'participants'])
            ->where(function ($query) use ($userID) {
                $query->where('created_by', $userID)->orWhereHas('participants', function ($query) use ($userID) {

                    $query->where('user_id', $userID);
                });
            })->whereHas('reminder', function ($taskReminderQuery) use ($recurrencePattern, $request) {

                $recurrencePattern === 'specific_date'
                    ? getRecurringTask($taskReminderQuery,  $recurrencePattern, $request->specific_date)
                    : getRecurringTask($taskReminderQuery,  $recurrencePattern);
            })->whereHas('durations', function ($taskRecurringsDurtionQuery) use ($request) {
                addDurationOverlapQuery($taskRecurringsDurtionQuery, $request);
            });

        $conflictingTask = $conflictingTaskBuilder->first() ?? null;

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
