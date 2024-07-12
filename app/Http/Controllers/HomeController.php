<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;
use App\Models\Duration;
use App\Models\NotificationTime;
use App\Models\Reminder;
use App\Models\Recurring;
use App\Models\Task;
use App\Models\User;

use Carbon\Carbon;

use function PHPUnit\Framework\isEmpty;

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

        $selectedCurrentUserTasksBuilder = Task::with([

            'participants',
            'reminder',
            // 'reminder.notificationTimes' => function ($query) use ($currentUserID) {
            //     $query->whereHas('reminder', function ($query) use ($currentUserID) {
            //         $query->where('user_id', $currentUserID);
            //     });
            // },
            'reminder.recurring',
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
        });

        $selectedCurrentUserTasks = $selectedCurrentUserTasksBuilder->get();

        $selectedCurrentUserTasks = $selectedCurrentUserTasks->sortBy(function ($task) use ($currentUserID) {
            return $task->durations->where('user_id', $currentUserID)->first()->start ?? '23:59:59';
        });

        // rodrigo
        // dd($selectedCurrentUserTasks, $selectedDate, $weekDayOfSelectDate);

        $labelOverview = "";

        if ($selectedCurrentUserTasks->isEmpty()) {

            $labelOverview = $isToday ? "Nenhuma tarefa agendada para hoje, " . getFormatedDateBR($today) : " Nenhuma tarefa agendada para  " . getFormatedDateBR($selectedDate) . ",  $weekdayInPortuguese.";
        } else {
            $labelOverview = $isToday ? "Agenda de hoje,  " . getFormatedDateBR($today) : "Agenda de " . getFormatedDateBR($selectedDate) . ",  $weekdayInPortuguese.";
        }

        foreach ($selectedCurrentUserTasks as $task) {

            $taskID = $task->id;

            // $notificationTime = NotificationTime::whereHas('reminder', function ($query) use ($currentUserID, $taskID) {
            //     $query->where('user_id', $currentUserID)
            //         ->where('task_id', $taskID);
            // })->get();

            // dd($task->reminder->id);

            $creatorOrParticipant  = $task->created_by == $currentUserID ? 'creator' : 'participant';

            // dd($creatorOrParticipant);

            $notificationTime = null;

            if ($creatorOrParticipant == 'creator') {

                $notificationTime = NotificationTime::whereHas('reminder', function ($query) use ($currentUserID, $taskID) {

                    $query->whereHas('task', function ($query) use ($currentUserID, $taskID) {

                        $query->where('created_by', $currentUserID)->where('id', $taskID);
                    });
                })->first()->getAttributes();
            } else {
                $notificationTime = NotificationTime::whereHas('reminder', function ($query) use ($currentUserID, $taskID) {
                    $query->where('task_id', $taskID)
                        ->where('user_id', $currentUserID) // Verifica que o lembrete é para o participante
                        ->whereHas('task.participants', function ($query) use ($currentUserID) {
                            $query->where('user_id', $currentUserID)
                                ->where('status', 'accepted');
                        });
                })->get();
            }

            // else {

            //     $notificationTime = NotificationTime::whereHas('reminder', function ($query) use ($currentUserID, $taskID) {

            //         $query->whereHas('task', function ($query) use ($currentUserID, $taskID) {

            //             $query->where('task_id', $taskID)

            //                 ->whereHas('participants', function ($query) use ($currentUserID,  $taskID) {

            //                     $query->where(function ($query) use ($currentUserID,  $taskID) {
            //                         $query->where('user_id', $currentUserID)->where('task_id', $taskID);
            //                     })
            //                         ->where('status', 'accepted');
            //                 });
            //         });
            //     })->get();
            // }

            // dd($notificationTime);

            // dd($currentUserID, $taskID, auth()->user()->participatingTasks);

            // $filteredTasks = auth()->user()->participatingTasks->filter(function ($task) use ($taskID, $currentUserID) {
            //     return $task->id == $taskID && $task->participants->contains('id', $currentUserID);
            // });

            // dd($filteredTasks->first()->reminder->notificationTimes()->get);

            // dd(auth()->user()->participatingTasks->where('task_id', $taskID)->where('user_id', $currentUserID));
            // dd($notificationTime->first()->reminder->task->participants()->where('id', $currentUserID));

            // $participantNotificationTime = NotificationTime::whereHas('reminder', function ($query) use ($currentUserID, $taskID) {

            //     $query->whereHas('task', function ($query) use ($currentUserID, $taskID) {

            //         $query->where('id', $taskID)->whereHas('participants', function ($query) use ($currentUserID,  $taskID) {

            //             $query->where('user_id', $currentUserID)->where('task_id', $taskID)->where('status', 'accepted');
            //         });
            //     });
            // })->first()->getAttributes();

            // dd($participantNotificationTime);

            if (!is_null($notificationTime)) {

                $isNotificationTimeMissing = empty($notificationTime['specific_notification_time']) &&
                    $notificationTime['half_an_hour_before'] === "false" &&
                    $notificationTime['one_hour_before'] === "false" &&
                    $notificationTime['two_hours_before'] === "false" &&
                    $notificationTime['one_day_earlier'] === "false";
                // dd($isNotificationTimeMissing);

                $task->isNotificationTimeMissing = $isNotificationTimeMissing;
            }


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

        // foreach ($selectedCurrentUserTasks as $task) {
        //     $taskID = $task->id;

        //     $notificationTime = NotificationTime::whereHas('reminder', function ($query) use ($currentUserID, $taskID) {
        //         $query->where('user_id', $currentUserID)->where('task_id', $taskID);
        //     })->get();

        //     dd($notificationTime);

        //     $notificationTimes =  $task->reminder->notificationTimes->getAttributes();

        //     $isNotificationTimeMissing = empty($notificationTimes['specific_notification_time']) &&
        //         $notificationTimes['half_an_hour_before'] === "false" &&
        //         $notificationTimes['one_hour_before'] === "false" &&
        //         $notificationTimes['two_hours_before'] === "false" &&
        //         $notificationTimes['one_day_earlier'] === "false";

        //     $task->isNotificationTimeMissing = $isNotificationTimeMissing;

        //     if ($task->participants->isEmpty()) {

        //         $task->emailsParticipants = "Nenhum participante";
        //     } else {
        //         // Concatena os e-mails dos participantes
        //         $task->emailsParticipants = $task->participants->pluck('email')->implode(', ');
        //     }

        //     $task->start = substr($task->durations[0]->start, 0, 5);
        //     $task->end =  substr($task->durations[0]->end, 0, 5);

        //     $task->recurringMessage = getRecurringMessage($task->reminder->recurring);
        // }

        // dd($selectedCurrentUserTasks);

        return view('home', compact('isThereAnyReminder', 'selectedCurrentUserTasks', 'currentUserReminders', 'labelOverview'));
    }
}
