<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;
use Carbon\Carbon;

class DisplayController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }


    public function displayDay()
    {
        $userID = auth()->id();

        $today = getToday();

        $currentDayOfWeek = getDayOfWeek($today);

        // $tasksTodayBuilder = Task::with([

        //     'participants',
        //     'reminder',
        //     'reminder.recurring',
        //     'durations'

        // ])->where('concluded', 'false')->where(function ($query) use ($userID) {

        //     $query->where('created_by', $userID)->orWhereHas('participants', function ($query) use ($userID) {
        //         $query->where('user_id', $userID)->where('status', 'accepted');
        //     });
        // })->whereHas('reminder', function ($query) use ($today, $currentDayOfWeek) {

        //     $query->whereHas('recurring', function ($query) use ($today, $currentDayOfWeek) {

        //         $query->where(function ($query) use ($today, $currentDayOfWeek) {

        //             $query->where('specific_date', $today)->where('specific_date_weekday', $currentDayOfWeek);
        //         })->orWhere($currentDayOfWeek, 'true');
        //     });
        // });

        // $tasksToday = $tasksTodayBuilder->get();

        $now = getCarbonNow();


        $startOfDay = $now->copy()->startOfDay();

        // Calcula o número de minutos desde o início do dia
        $minutesSinceStartOfDay = $startOfDay->diffInMinutes($now);

        $position = 50 - ($minutesSinceStartOfDay *  0.185);

        // $tasksToday = $tasksToday->sortBy(function ($task) use ($userID) {
        //     return $task->durations->where('user_id', $userID)->first()->start ?? '23:59:59';
        // });

        return view('timelines/day', compact('position'));
    }

    public function displayWeek()
    {
        $now = getCarbonNow();
        $startOfWeek = $now->copy()->startOfWeek(Carbon::SUNDAY);

        $carbonWeekDays = [];

        for ($i = 0; $i < 7; $i++) {
            $carbonWeekDays[] = $startOfWeek->copy()->addDays($i);
        }

        return view('timelines/week', compact('carbonWeekDays', 'now'));
    }

    public function displayMonth()
    {
        $now = getCarbonNow();

        $currentMonth = $now->month;
        $currentYear = $now->year;

        $firstDayOfMonth = getCarbonDate($now->format('Y-m') . '-1');
        $lastDayOfMonth = getCarbonDate($now->format('Y-m') . '-' . $now->daysInMonth);

        $daysInMonth = $now->daysInMonth;

        $daysArray = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {

            $daysArray[] = getCarbonDate($now->format('Y-m') . '-' . $day);
        }

        $startDayIndex = $firstDayOfMonth->dayOfWeek;

        $emptyDaysBefore = array_fill(0, $startDayIndex, '');

        $daysWithEmpty = array_merge($emptyDaysBefore, $daysArray);

        $totalDaysToShow = 42;

        $daysToFill = $totalDaysToShow - count($daysWithEmpty);

        for ($i = 0; $i < $startDayIndex; $i++) {

            $daysWithEmpty[$i] = $firstDayOfMonth->copy()->subDays($startDayIndex - $i);
        }

        for ($i = 0; $i < $daysToFill; $i++) {

            $daysWithEmpty[] = $lastDayOfMonth->copy()->addDays($i);
        }

        $startOfWeek = $now->copy()->startOfWeek(Carbon::SUNDAY);

        $carbonWeekDays = [];

        for ($i = 0; $i < 7; $i++) {

            $carbonWeekDays[] = $startOfWeek->copy()->addDays($i);
        }

        $monthsInPortuguese = [
            'Jan' => 'Jan',
            'Feb' => 'Fev',
            'Mar' => 'Mar',
            'Apr' => 'Abr',
            'May' => 'Mai',
            'Jun' => 'Jun',
            'Jul' => 'Jul',
            'Aug' => 'Ago',
            'Sep' => 'Set',
            'Oct' => 'Out',
            'Nov' => 'Nov',
            'Dec' => 'Dez',
        ];

        return view('timelines/month', compact('daysWithEmpty', 'carbonWeekDays', 'monthsInPortuguese'));
    }
}
