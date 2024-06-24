<?php

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

    function setTask()

    {
    }
}
