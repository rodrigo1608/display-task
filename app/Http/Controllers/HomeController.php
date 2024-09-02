<?php

namespace App\Http\Controllers;

use App\Models\NotificationTime;
use App\Models\Reminder;
use App\Models\Task;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        // //-----------------------------------------------------------------------------------------------Teste

        //------------------------------------------------------------------------------------------termina o teste

        $now = getCarbonNow()->format('H:i');

        $today = getToday();

        $selectedDate = $request->input('specific_date') ?? $today;

        $isToday = checkIsToday($selectedDate);

        $weekdayOfSelectDate = getDayOfWeek(getCarbonDate($selectedDate));

        $weekdayInPortuguese = getDayOfWeek(getCarbonDate($selectedDate), 'pt-br');

        $currentUserID = Auth::id();

        $daysOfWeek = getDaysOfWeek();

        $userRemindersByWeekDay = getRemindersByWeekday($daysOfWeek);

        $isThereAnyReminder = !empty($userRemindersByWeekDay);

        $orderedReminders = getWeekDaysStartingFromToday($userRemindersByWeekDay);

        $selectedUserTasksBuilder = Task::with([

            'participants',
            'reminder',
            'reminder.recurring',
            'durations'

        ])->where(function ($query) use ($currentUserID) {

            $query->where('created_by', $currentUserID)->orWhereHas('participants', function ($query) use ($currentUserID) {
                $query->where('user_id', $currentUserID)->where('status', 'accepted');
            });
        })->whereHas('reminder', function ($query) use ($selectedDate, $weekdayOfSelectDate) {

            $query->whereHas('recurring', function ($query) use ($selectedDate, $weekdayOfSelectDate) {

                $query->where(function ($query) use ($selectedDate, $weekdayOfSelectDate) {

                    $query->where('specific_date', $selectedDate)->where('specific_date_weekday', $weekdayOfSelectDate);
                })->orWhere($weekdayOfSelectDate, 'true');
            });
        });

        $selectedUserTasks = $selectedUserTasksBuilder->get();

        $selectedUserTasks = $selectedUserTasks->sortBy(function ($task) use ($currentUserID) {
            return $task->durations->where('user_id', $currentUserID)->first()->start ?? '23:59:59';
        });

        $labelOverview = "";

        if ($selectedUserTasks->isEmpty()) {

            $labelOverview = $isToday ? "Nenhuma tarefa agendada para hoje, " . getFormatedDateBR($today) : " Nenhuma tarefa agendada para  " . getFormatedDateBR($selectedDate) . ",  $weekdayInPortuguese.";
        } else {
            $labelOverview = $isToday ? "Agenda de hoje, " . getFormatedDateBR($today) : "Agenda de " . getFormatedDateBR($selectedDate) . ",  $weekdayInPortuguese.";
        }

        foreach ($selectedUserTasks as $task) {

            $taskID = $task->id;

            $creatorOrParticipant  = $task->created_by == $currentUserID ? 'creator' : 'participant';

            $notificationTime = null;

            $notificationTime = $task->reminder->notificationTimes()->where('user_id', $currentUserID)->first()?->getAttributes();

            // if ($creatorOrParticipant == 'creator') {

            //     $notificationTime = NotificationTime::whereHas('reminder', function ($query) use ($currentUserID, $taskID) {

            //         $query->whereHas('task', function ($query) use ($currentUserID, $taskID) {

            //             $query->where('created_by', $currentUserID)->where('id', $taskID);
            //         });
            //     })->first()->getAttributes();
            // } else {
            //     $notificationTime = NotificationTime::where('user_id', $currentUserID)->whereHas('reminder', function ($query) use ($taskID) {

            //         $query->whereHas('task', function ($query) use ($taskID) {

            //             $query->where('task_id', $taskID);
            //         });
            //     })->first()->getAttributes();
            // }

            if ($task->participants->isEmpty()) {

                $task->emailsParticipants = "Nenhum participante";
            } else {

                $task->emailsParticipants = $task->participants->pluck('email')->implode(', ');
            }

            $duration = $task->durations()->where('user_id', $currentUserID)->where('task_id', $task->id)->first();

            $task->start = substr($duration->start, 0, 5);
            $task->end =  substr($duration->end, 0, 5);

            $task->recurringMessage = getRecurringMessage($task->reminder->recurring);

            $start = isset($task->start) ? getCarbonTime($task->start) : null;
            $end = isset($task->end) ? getCarbonTime($task->end) : null;

            $recurring = $task->reminder->recurring;

            $task->status = $duration->status;

            // if (isset($recurring->specific_date)) {

            //     $specificDate = getCarbonDate($recurring->specific_date);

            //     $isPast = $specificDate->isBefore($today);
            //     $isTodaySpecificDate = checkIsToday($specificDate);

            //     if ($isPast) {

            //         $task->status = 'finished';
            //     } elseif ($isTodaySpecificDate) {

            //         $task->status = getTaskStatus($duration);
            //     } else {

            //         $task->status = 'starting';
            //     }
            // } else {

            //     $todayWeekday = getDayOfWeek($today);

            //     $repeatingDays = getRepeatingDays($recurring);

            //     foreach ($repeatingDays as $day) {

            //         if ($day ===  $todayWeekday) {

            //             $task->status = getTaskStatus($duration);
            //         } else {
            //             $task->status = 'finished';
            //         }
            //     }
            // }
        }

        return view('home', compact('isThereAnyReminder', 'selectedUserTasks', 'orderedReminders', 'labelOverview'));
    }
}
