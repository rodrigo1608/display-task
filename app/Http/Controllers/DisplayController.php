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


    public function displayDay(Request $request)
    {

        $userID = auth()->id();
        $date =  empty($request->all()) ? getToday() : getCarbonDate($request->date);

        $currentDayOfWeek = getDayOfWeek($date);

        $hasAnytaskToday = getSelectedUserTasksBuilder($date)->exists();

        $now = getCarbonNow();

        $startOfDay = $now->copy()->startOfDay();

        // Calcula o número de minutos desde o início do dia
        $minutesSinceStartOfDay = $startOfDay->diffInMinutes($now);

        $position = 50 - ($minutesSinceStartOfDay *  0.185);

        // $tasksToday = $tasksToday->sortBy(function ($task) use ($userID) {
        //     return $task->durations->where('user_id', $userID)->first()->start ?? '23:59:59';
        // });

        return view('timelines/day', compact('position', 'hasAnytaskToday', 'date'));
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

    public function displayMonth(Request $request)
    {
        $now = getCarbonNow();

        $currentDate = '';
        $selectedMonth =  $now->format('F');
        $selectedYear = $now->format('Y');

        if (!empty($request->all())) {

            $currentDate = $request->year . '-0' . getCarbonDate($request->month)->month;
            $selectedMonth = $request->month;
            $selectedYear  = $request->year;
        }

        $isCurrentDate = empty($request->all()) ||  $currentDate === $now->format('Y-m');

        $date = $isCurrentDate ? $now : getCarbonDate($currentDate);

        $firstDayOfMonth = $date->copy()->startOfMonth()->timezone('America/Sao_Paulo');
        $lastDayOfMonth = $date->copy()->endOfMonth()->timezone('America/Sao_Paulo');

        $daysInMonth = $date->daysInMonth;

        $daysArray = [];

        for ($day = 1; $day <= $daysInMonth; $day++) {

            $daysArray[] = getCarbonDate($date->format('Y-m') . '-' . $day);
        }

        $startDayIndex = $firstDayOfMonth->dayOfWeek ?: 7;

        $emptyDaysBefore = array_fill(0, $startDayIndex, '');

        $daysWithEmpty = array_merge($emptyDaysBefore, $daysArray);

        $totalDaysToShow = 42;

        $daysToFill = $totalDaysToShow - count($daysWithEmpty);

        for ($i = 0; $i < $startDayIndex; $i++) {

            $daysWithEmpty[$i] = $firstDayOfMonth->copy()->subDays($startDayIndex - $i);
        }

        for ($i = 1; $i <= $daysToFill; $i++) {

            $daysWithEmpty[] = $lastDayOfMonth->copy()->addDays($i);
        }

        $startOfWeek = $date->copy()->startOfWeek(Carbon::SUNDAY);

        $carbonWeekDays = [];

        for ($i = 0; $i < 7; $i++) {

            $carbonWeekDays[] = $startOfWeek->copy()->addDays($i);
        }

        $months = getMonths();
        $selectedMOnthInPortuguese = $months[$selectedMonth];

        // dd($daysWithEmpty);
        return view('timelines/month', compact('daysWithEmpty', 'carbonWeekDays', 'months', 'selectedMOnthInPortuguese', 'selectedYear'));
    }
}
