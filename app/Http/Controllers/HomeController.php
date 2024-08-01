<?php

namespace App\Http\Controllers;

use App\Mail\ReminderNotify;
use App\Mail\TaskNotify;
use App\Models\NotificationTime;
use App\Models\Reminder;
use App\Models\Task;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

// use function PHPUnit\Framework\isEmpty;

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

        $now = getCarbonNow()->format('H:i:s');

        //Teste
        $notificationTimes =  getNotificationtimes('specific_notification_time');
        // dd($notificationTimes);

        foreach ($notificationTimes as $notificationTime) {

            //Se houver um user_id setado na instancia de um Reminder, é lembrete
            //Se houver um task_id setado na instancia de um Reminder, é uma tarefa
            $isTask = is_null($notificationTime->reminder->user_id);

            $hasSpecificDate = !is_null($notificationTime->reminder->recurring);
            $userToNotify =  $notificationTime->user;

            if ($hasSpecificDate) {

                $specificDate = getCarbonDate($notificationTime->reminder->recurring->specific_date);
                $specificTime = getCarbonTime($notificationTime->specific_notification_time);

                $isNotificationTime = $specificDate->isToday() && ($now !== $specificTime);

                if ($isNotificationTime) {

                    $emailAddress = $userToNotify->email;
                    // dump($emailAddressToNotify);

                    if ($isTask) {

                        $task = $notificationTime->reminder->task;

                        $start = getCarbonTime($task->durations()
                            ->where('user_id', $userToNotify->id)
                            ->where('task_id', $task->id)->first()
                            ->start);

                        $notificationMessage = getTaskNotificationMessage($task->title, $specificTime, $start);

                        $taskData = $task->getAttributes();

                        $taskData['start'] = $start;
                        $taskData['message'] = getTaskNotificationMessage($task->title, $specificTime, $start);

                        Mail::to($emailAddress)->send(new TaskNotify($taskData));
                    } else {

                        Mail::to($emailAddress)->send(new ReminderNotify());
                    }
                }
            }

            $repeatingDays = getRepeatingDays($notificationTime->reminder->recurring);

            foreach ($repeatingDays as $day) {
            }

            $userToBeNotify = $notificationTime->user;

            $usersEmailsToNotificationsTimes = array();

            // $startTaskReference = $notificationTime->reminder
            // ->task->durations
            // ->where('user_id', $userToBeNotify->id)
            // ->first()
            // ->start;














            //             $startReference;
            // $notificationTime->reminder->task
            // $specificNotificationTimePattern = $notificationPattern === 'specific_notification_time' &&

            // if ($notificationPattern === 'specific_notification_time') {

            //     $usersEmailsToNotificationsTimes;

            //     dd($notificationTime->specific_notification_time);
            // }

            // $startReference;
            //         }




            // dd($userToBeNotify->reminder->task->get());

            // $startReference = $notificationTime->reminder
            //     ->task->durations
            //     ->where('user_id', $userToBeNotify->id)
            //     ->first()
            //     ->start;

            // $carbonStartReference = Carbon::createFromFormat('H:i:s', $startReference);

            // $notificationTime = $carbonStartReference->subMinutes(30)->format('H:i:s');

            // // dump($notificationTime);
        }

        // $tasks = Task::all();

        // foreach ($tasks as $task) {

        //     $notificationTimes = $task->reminder->notificationTimes;

        //     foreach ($notificationTimes as  $notificationTime) {

        //         foreach ($notificationTime->getAttributes() as $timeSlot => $timeSlotValue) {

        //             if ($timeSlotValue === "true") {

        //                 // dd($timeSlot);

        //                 $halfAnHourBefore = "";
        //             }
        //         }

        //         if (!is_null($notificationTime->specific_notification_time)) {
        //         }
        //     }
        // }

        // $notificationTimes =
        //termina o teste

        // Task::whereHas('durations', function ($query) use ($now) {
        //     $query->where('end', '<', $now);
        // })->update(['status' => 'finished']);

        $today = getToday()->format('Y-m-d');

        $selectedDate = $request->input('specific_date') ?? $today;
        //Rodrigo
        // dd($selectedDate);

        $isToday = $selectedDate == $today;

        //Rodrigo
        // if ($selectedDate != $today) {
        //     dd($isToday);
        // }

        $weekDayOfSelectDate = getWeekDayName($selectedDate);

        $weekdayInPortuguese = getWeekdayInPortuguese($weekDayOfSelectDate);

        //Rodrigo
        // dd($weekdayInPortuguese);

        $currentUserID = Auth::id();

        $currentUserReminders = Reminder::whereNotNull('user_id')->where('user_id', $currentUserID)->get();

        $isThereAnyReminder = $currentUserReminders->isNotEmpty();

        $selectedCurrentUserTasksBuilder = Task::with([

            'participants',
            'reminder',
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
                $notificationTime = NotificationTime::where('user_id', $currentUserID)->whereHas('reminder', function ($query) use ($taskID) {

                    $query->whereHas('task', function ($query) use ($taskID) {

                        $query->where('task_id', $taskID);
                    });
                })->first()->getAttributes();
            }

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
