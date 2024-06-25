<?php

use Carbon\Carbon;

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
