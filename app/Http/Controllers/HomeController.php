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
        //     dd($isToday);
        // }

        $weekDayOfSelectDate = getWeekDayName($selectedDate);

        $weekdayInPortuguese = getDayOfWeekInPortuguese($weekDayOfSelectDate);

        //Rodrigo
        // dd($weekdayInPortuguese);

        $labelOverview = $isToday ? 'Agenda de hoje' : "Agenda de $weekdayInPortuguese";

        $currentUserID = Auth::id();

        $currentUserReminders = Reminder::whereNotNull('user_id')->where('user_id', $currentUserID)->get();

        $isThereAnyReminder = $currentUserReminders->isNotEmpty();

        // $currentUserTasksBuilder = Task::with('participants')->whereHas('participants', function ($query) use ($currentUserID) {

        //     $query->where('user_id', $currentUserID)
        //         ->where('status', 'accepted');
        // })->orWhere('created_by', $currentUserID);

        $selectedCurrentUserTasks = Task::with([

            'participants',
            'reminder',
            'reminder.recurring',
            'reminder.notificationTimes',
            'durations'

        ])->whereHas('participants', function ($query) use ($currentUserID) {

            $query->where(function ($query) use ($currentUserID) {
                $query->where('user_id', $currentUserID)
                    ->where('status', 'accepted');
            })->orWhere('created_by', $currentUserID);
        })->whereHas('reminder', function ($query) use ($selectedDate, $weekDayOfSelectDate) {

            $query->whereHas('recurring', function ($query) use ($selectedDate, $weekDayOfSelectDate) {

                $query->where(function ($query) use ($selectedDate, $weekDayOfSelectDate) {

                    $query->where('specific_date', $selectedDate)->where('specific_date_weekday', $weekDayOfSelectDate);
                })->orWhere($weekDayOfSelectDate, 'true');
            });
        })->get();


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

        // dd($selectedCurrentUserTasks);

        // dd($selectedCurrentUserTasks);

        // else {
        //     $selectedCurrentUserTasks = getRecurringTasks($recurrencePattern, $currentUserTasksBuilder)->get();
        // }

        return view('home', compact('isThereAnyReminder', 'selectedCurrentUserTasks', 'currentUserReminders', 'labelOverview'));

        // dd($selectedCurrentUserTasks);

        // $myTasksToday = $currentUserTasksBuilder->whereHas('reminder', function ($query) use ($today, $dayOfWeek) {

        //     $query->whereHas('recurring', function ($recurringQuery) use ($today, $dayOfWeek) {

        //         $recurringQuery->where('specific_date', $today)
        //             ->orWhere($dayOfWeek, true);
        //     });
        // })->get();

        // $currentUserTasks = $currentUserTasksBuilder->get();

        // foreach ($currentUserTasks as $task) {

        //     // rodrigo
        //     // dd($task);

        //     $duration = Duration::where('task_id', $task->id)->where('user_id', $currentUserID)->first();

        //     $durationExist = $duration !== null;

        //     // rodrigo
        //     // dd($duration->start);

        //     $task['start'] = $durationExist ? Carbon::parse($duration->start)->format('H:i') : null;

        //     $task['end'] = $durationExist ? Carbon::parse($duration->end)->format('H:i') : null;

        //     $currentTime = Carbon::now();

        //     // $timeDifference = $currentTime->diffForHumans($duration->start, [
        //     //     'parts' => 2,
        //     //     'join' => true,
        //     //     'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
        //     // ]);
        //     // $task['time_difference'] = $timeDifference;
        // }

        // foreach ($myTasksToday as $task) {

        //     // Rodrigo
        //     // dd($task->reminder->recurring);

        //     $duration = Duration::where('task_id', $task->id)->where('user_id', $currentUserID)->first();

        //     $durationExist = $duration !== null;

        //     // Rodrigo
        //     // dd($duration);

        //     if ($durationExist) {

        //         // rodrigo
        //         // dd(Carbon::parse($duration->start));

        //         $startTime = Carbon::parse($duration->start);

        //         $task['start'] =  $startTime;

        //         $endTime = Carbon::parse($duration->end);

        //         $task['end'] = $endTime;

        //         $currentTime = Carbon::now();

        //         $timeDifferenceInMinutes = $startTime->diffInMinutes($currentTime, false); // Add false to keep the negative value if startTime is in the past

        //         $task['time_difference'] = $timeDifferenceInMinutes;

        //         // dd($task['time_difference']);
        //     }
        // }

        // $startTime = Carbon::parse($task->start);

        // $currentTime = Carbon::now();

        // $timeDifference = $startTime->diffForHumans($currentTime, [
        //     'parts' => 2,
        //     'join' => true,
        //     'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
        // ]);


    }
}
