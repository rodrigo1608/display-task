<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Task;

class DisplayController extends Controller
{
    public function displayDay()
    {
        $userID = auth()->id();

        $today = getToday();

        $currentDayOfWeek = getDayOfWeek($today);


        $tasksTodayBuilder = Task::with([

            'participants',
            'reminder',
            'reminder.recurring',
            'durations'

        ])->where('concluded', 'false')->where(function ($query) use ($userID) {

            $query->where('created_by', $userID)->orWhereHas('participants', function ($query) use ($userID) {
                $query->where('user_id', $userID)->where('status', 'accepted');
            });
        })->whereHas('reminder', function ($query) use ($today, $currentDayOfWeek) {

            $query->whereHas('recurring', function ($query) use ($today, $currentDayOfWeek) {

                $query->where(function ($query) use ($today, $currentDayOfWeek) {

                    $query->where('specific_date', $today)->where('specific_date_weekday', $currentDayOfWeek);
                })->orWhere($currentDayOfWeek, 'true');
            });
        });

        $tasksToday = $tasksTodayBuilder->get();

        $tasksToday = $tasksToday->sortBy(function ($task) use ($userID) {
            return $task->durations->where('user_id', $userID)->first()->start ?? '23:59:59';
        });

        return view('day', compact('tasksToday'));
    }
}
