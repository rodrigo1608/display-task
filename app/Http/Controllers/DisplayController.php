<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

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

        return view('day', compact('position'));
    }
}
