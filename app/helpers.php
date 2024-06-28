<?php

use Carbon\Carbon;
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
            $formatedDate = '<strong>' . Carbon::parse($recurring->specific_date)->format('d/m/Y') . '</strong>';
            $recurringMessage = "Ocorrerá exclusivamente no dia: $formatedDate ";
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

        $dayName = $carbonDate->englishDayOfWeek;

        return $dayName;
    }
}

if (!function_exists('getRecurringTask')) {

    function getRecurringTask(Builder $query, $weekDay)
    {
        return $query->whereHas('recurring', function ($taskReminderRecurringQuery) use ($weekDay) {

            $taskReminderRecurringQuery->where('specific_date_weekday',  $weekDay)->orWhere($weekDay, 'true');
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

    function getTaskInArray($spercificDateTask)
    {

        $spercificDateTaskToArray =  $spercificDateTask->toArray();

        $spercificDateTaskToArray['owner'] = $spercificDateTask->creator->name . ' ' . $spercificDateTask->creator->lastname;

        $spercificDateTaskToArray['owner_telehpone'] =  getFormatedTelephone($spercificDateTask->creator);

        $spercificDateTaskToArray['owner_email'] =  $spercificDateTask->creator->email;

        $conflictingDuration =  $spercificDateTask->durations->first();

        $spercificDateTaskToArray['start'] = date('H:i', strtotime($conflictingDuration->start));

        $spercificDateTaskToArray['end'] =  date('H:i', strtotime($conflictingDuration->end));

        $spercificDateTaskToArray['recurringMessage'] = getRecurringMessage($spercificDateTask->reminder->recurring);

        return   $spercificDateTaskToArray;
    }
}
