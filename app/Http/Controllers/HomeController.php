<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Duration;
use App\Models\Reminder;
use App\Models\Recurring;
use App\Models\Task;
use App\Models\User;

use Carbon\Carbon;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */

    public function index(Request $request)
    {

        $selectedDate = $request->input('specific_date');

        $today = Carbon::today()->format('Y-m-d');

        //Rodrigo
        // dd($today);

        if (is_null($selectedDate)) {

            $selectedDate = $today;
        }

        $isToday = $selectedDate == $today;

        //Rodrigo
        // if ($selectedDate != $today) {
        //     dd($isToday);er
        // }

        $weekDayOfSelectDate = getWeekDayName($selectedDate);

        $weekdayInPortuguese = getDayOfWeekInPortuguese($weekDayOfSelectDate);

        //Rodrigo
        // dd($weekdayInPortuguese);



        $currentUserID = Auth::id();

        $currentUserReminders = Reminder::whereNotNull('user_id')->where('user_id', $currentUserID)->get();

        $isThereAnyReminder = $currentUserReminders->isNotEmpty();

        $selectedCurrentUserTasks = Task::with([

            'participants',
            'reminder',
            'reminder.recurring',
            'reminder.notificationTimes',
            'durations'

        ])->where(function ($query) use ($currentUserID) {
            $query->where('created_by', $currentUserID)->orWhereHas('participants', function ($query) use ($currentUserID) {
                $query->where('user_id', $currentUserID)->where('status', 'accepted');
            });
        })->whereHas('reminder', function ($query) use ($selectedDate, $weekDayOfSelectDate) {

            $query->whereHas('recurring', function ($query) use ($selectedDate, $weekDayOfSelectDate) {

                $query->where(function ($query) use ($selectedDate, $weekDayOfSelectDate) {

                    $query->where('specific_date', $selectedDate)->where('specific_date_weekday', $weekDayOfSelectDate);
                })->orWhere($weekDayOfSelectDate, 'true');
            });
        })->get();

        // rodrigo
        // dd($selectedCurrentUserTasks, $selectedDate, $weekDayOfSelectDate);

        $labelOverview = "";

        if ($selectedCurrentUserTasks->isEmpty()) {

            $labelOverview = $isToday ? "Nenhuma tarefa agendada para hoje, " . getFormatedDateBR($today) : " Nenhuma tarefa agendada para  " . getFormatedDateBR($selectedDate) . ",  $weekdayInPortuguese.";
        } else {
            $labelOverview = $isToday ? "Agenda de hoje,  " . getFormatedDateBR($today) : "Agenda de " . getFormatedDateBR($selectedDate) . ",  $weekdayInPortuguese.";
        }

        foreach ($selectedCurrentUserTasks as $task) {

            $notificationTimes =  $task->reminder->notificationTimes->getAttributes();

            $isNotificationTimeMissing = empty($notificationTimes['specific_notification_time']) &&
                $notificationTimes['half_an_hour_before'] === "false" &&
                $notificationTimes['one_hour_before'] === "false" &&
                $notificationTimes['two_hours_before'] === "false" &&
                $notificationTimes['one_day_earlier'] === "false";

            $task->isNotificationTimeMissing = $isNotificationTimeMissing;

            if ($task->participants->isEmpty()) {

                $task->emailsParticipants = "Nenhum participante";
            } else {
                // Concatena os e-mails dos participantes
                $task->emailsParticipants = $task->participants->pluck('email')->implode(', ');
            }

            $task->start = substr($task->durations[0]->start, 0, 5);
            $task->end =  substr($task->durations[0]->end, 0, 5);

            $task->recurringMessage = getRecurringMessage($task->reminder->recurring);
        }

        return view('home', compact('isThereAnyReminder', 'selectedCurrentUserTasks', 'currentUserReminders', 'labelOverview'));
    }
}
