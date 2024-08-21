<?php

namespace App\Http\Controllers;

use App\Models\NotificationTime;
use App\Models\Reminder;
use App\Models\Task;


use Illuminate\Support\Facades\Log;

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
        $now = getCarbonNow()->format('H:i');

        //-----------------------------------------------------------------------------------------------Teste

        // $notificationTimes = NotificationTime::all();

        // if (!$notificationTimes->isEmpty()) {

        //     foreach ($notificationTimes as $notificationTime) {

        //         $recurring = $notificationTime->reminder->recurring;

        //         $hasSpecificDate = !is_null($recurring->specific_date);

        //         $task = $notificationTime->reminder->task;

        //         $userToNotify = $notificationTime->user;

        //         $notificationData = [

        //             'has_specific_date' => $hasSpecificDate,

        //             'notification_time' => $notificationTime,

        //             'recurring' => $recurring,

        //             'task' => $task,

        //             'user_to_notify' => $userToNotify,

        //         ];

        //         if ($hasSpecificDate) {

        //             $specificDate = getCarbonDate($recurring->specific_date);

        //             Log::info('');
        //             Log::info('Job NotifyAtCustomTime: ' . getRecurringLog($notificationTime) . ' - Recurring ID: ' . $recurring->id);

        //             $hasCustomTime = !is_null($notificationTime->custom_time);

        //             if ($hasCustomTime) {

        //                 $customTime = getCarbonTime($notificationTime->custom_time);

        //                 $isToday = checkIsToday($specificDate);

        //                 if (!$isToday) {

        //                     $notificationData['scheduled_date'] = $specificDate;

        //                     logNotificationNotScheduledForToday($notificationData);
        //                 } else {

        //                     $notificationPattern = 'custom_time';

        //                     $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //                     $isNotificationTime = logNotificationTime($notificationTimeData, $isToday);

        //                     if ($isNotificationTime) {

        //                         notify($notificationTime, $customTime);
        //                     }
        //                 }
        //             } else {

        //                 $selectedTimes = getSelectedNotificationTimes($notificationTime);

        //                 foreach ($selectedTimes as $time) {

        //                     if ($time === 'one_day_earlier') {


        //                         $isTodayADayBefore =  checkIsDayBefore($specificDate);

        //                         if (!$isTodayADayBefore) {

        //                             $notificationData['scheduled_date'] = $specificDate->copy()->subDay();

        //                             logNotificationNotScheduledForToday($notificationData);
        //                         } else {

        //                             $notificationPattern = 'one_day_earlier';

        //                             $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //                             $isNotificationTime = logNotificationTime($notificationTimeData, $isTodayADayBefore);

        //                             if ($isNotificationTime) {

        //                                 $start = getStartDuration($task, $userToNotify);
        //                                 $alertTime = $start->copy()->subDay();

        //                                 notify($notificationTime, $alertTime);
        //                             }
        //                         }
        //                     } else {

        //                         $isToday = checkIsToday($specificDate);

        //                         if (!$isToday) {

        //                             $notificationData['scheduled_date'] = $specificDate;
        //                             logNotificationNotScheduledForToday($notificationData);
        //                         } else {

        //                             $notificationPattern = $time;

        //                             $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //                             $isNotificationTime = logNotificationTime($notificationTimeData, $isToday);

        //                             if ($isNotificationTime) {

        //                                 $userToNotify = $notificationTime->user;

        //                                 $start = getStartDuration($task, $userToNotify);

        //                                 $alertTime = getDefaultTimeAlert($notificationPattern, $start);

        //                                 notify($notificationTime, $alertTime);
        //                             }
        //                         }
        //                     }
        //                 }
        //             }
        //         } else {

        //             $isTask = !is_null($task);
        //             Log::info('');
        //             Log::info('Job NotifyAtCustomTime: ' . getNotificationContextSnippet($isTask) . ' para se repetir semanalmente em dias selecionados - Recurring ID: ' . $recurring->id);

        //             $recurringDays = getRepeatingDays($recurring);


        //             $today = getToday();
        //             $todayWeekday = getDayOfWeek($today);
        //             $oneDayAfterWeekday = getDayOfWeek($today->copy()->addDay());

        //             // dd($oneDayAfterWeekday);
        //             // dd($todayWeekday);

        //             $hasCustomTime = !is_null($notificationTime->custom_time);

        //             if ($hasCustomTime) {

        //                 $customTime = getCarbonTime($notificationTime->custom_time);

        //                 $hasNoTodayWeekday = !in_array($todayWeekday, $recurringDays);

        //                 if ($hasNoTodayWeekday) {

        //                     $scheduledDate = [];

        //                     foreach ($recurringDays  as $day) {

        //                         $scheduledDate[] = getDaysOfWeek()[$day];
        //                     }

        //                     $notificationData['scheduled_date'] = $scheduledDate;

        //                     logNotificationNotScheduledForToday($notificationData);
        //                 } else {

        //                     $notificationPattern = 'custom_time';

        //                     $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //                     $isNotificationTime = logNotificationTime($notificationTimeData, $todayWeekday);

        //                     if ($isNotificationTime) {

        //                         notify($notificationTime, $customTime);
        //                     }
        //                 }
        //             } else {

        //                 $selectedTimes = getSelectedNotificationTimes($notificationTime);

        //                 foreach ($selectedTimes as $time) {

        //                     if ($time === 'one_day_earlier') {

        //                         $isOneDayAfterWeekday = in_array($oneDayAfterWeekday, $recurringDays);

        //                         if (!$isOneDayAfterWeekday) {

        //                             // $scheduledDate = getDaysOfWeek()[strtolower(Carbon::parse($oneDayAfterWeekday)->subDay()->format('l'))];

        //                             $oneDayAfterWeekdays = [];

        //                             foreach ($recurringDays  as $day) {

        //                                 $oneDayAfterWeekdays[] = getDaysOfWeek()[strtolower(getCarbonDate($day)->subDay()->format('l'))];
        //                             }
        //                             $notificationData['scheduled_date'] = $oneDayAfterWeekdays;

        //                             logNotificationNotScheduledForToday($notificationData);
        //                         } else {

        //                             $notificationPattern = 'one_day_earlier';

        //                             $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //                             $isNotificationTime = logNotificationTime($notificationTimeData, $isOneDayAfterWeekday);

        //                             if ($isNotificationTime) {

        //                                 $start = getStartDuration($task, $userToNotify);

        //                                 $alertTime = $start->copy()->subDay();

        //                                 notify($notificationTime, $alertTime);
        //                             }
        //                         }
        //                     } else {

        //                         $todayWeekday = in_array($todayWeekday, $recurringDays);

        //                         if (!$todayWeekday) {

        //                             $alertDays = [];

        //                             foreach ($recurringDays  as $day) {
        //                                 $alertDays[] = getDaysOfWeek()[$day];
        //                             }


        //                             $notificationData['scheduled_date'] = $alertDays;

        //                             logNotificationNotScheduledForToday($notificationData);
        //                         } else {

        //                             $notificationPattern = $time;

        //                             $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //                             $isNotificationTime = logNotificationTime($notificationTimeData, $todayWeekday);

        //                             if ($isNotificationTime) {

        //                                 $userToNotify = $notificationTime->user;

        //                                 $start = getStartDuration($task, $userToNotify);

        //                                 $alertTime = getDefaultTimeAlert($notificationPattern, $start);

        //                                 notify($notificationTime, $alertTime);
        //                             }
        //                         }
        //                     }
        //                 }
        //             }



































        //             // $oneDayBeforeWeekday = getDayOfWeek($today->copy()->subDay());

        //             // $recurringNotificationToday =  $notificationTime->reminder->recurring->where($todayWeekday, 'true')->exists();

        //             // $oneDayBeforeRecurringNotification =  $notificationTime->with('reminder.recurring')

        //             //     ->where('one_day_earlier', 'true')

        //             //     ->whereHas('reminder', function ($query) use ($oneDayAfterWeekday) {

        //             //         $query->whereHas('recurring', function ($query) use ($oneDayAfterWeekday) {

        //             //             $query->where($oneDayAfterWeekday, 'true');
        //             //         });
        //             //     })->first();

        //             // dd($oneDayBeforeRecurringNotification);

        //             // if ($oneDayBeforeRecurringNotification->isNotEmpty()) {

        //             //     $notificationPattern = $time;

        //             //     $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //             //     $isNotificationTime = logNotificationTime($notificationTimeData, $isToday);

        //             //     if ($isNotificationTime) {

        //             //         $userToNotify = $notificationTime->user;

        //             //         $start = getStartDuration($task, $userToNotify);

        //             //         $alertTime = getDefaultTimeAlert($notificationPattern, $start);

        //             //         notify($notificationTime, $alertTime);
        //             //     }
        //             // }

        //             // if ($hasOneDayBeforeRecurringNotification) {

        //             //     logNotificationNotScheduledForToday($notificationData);
        //             // } else {

        //             //     $isTask = !is_null($notificationTime->reminder->task);

        //             //     Log::info('');
        //             //     Log::info('Job NotifyAtCustomTime: ' . getNotificationContextSnippet($isTask) . ' para se repetir semanalmente em dia(s) selecionado(s) - Recurring ID: ' . $recurring->id);
        //             // }

        //             // foreach ($recurringDays as $day) {

        //             //     $isToday =  checkIsToday($day);

        //             //     if (!$isToday) {

        //             //         $notificationData['day'] = $day;

        //             //         logNotificationNotScheduledForToday($notificationData);
        //             //     } else {
        //             //         $isTask = !is_null($notificationTime->reminder->task);
        //             //         Log::info('');
        //             //         Log::info('Job NotifyAtCustomTime: ' . getNotificationContextSnippet($isTask) . ' para se repetir semanalmente em dias selecionados - Recurring ID: ' . $recurring->id);

        //             //         $hasCustomTime = !is_null($notificationTime->custom_time);


        //             //         $customTime =  $hasCustomTime
        //             //             ? getCarbonTime($notificationTime->custom_time)
        //             //             : null;

        //             //         if ($hasCustomTime) {

        //             //             $isToday = checkIsToday($day);

        //             //             if (!$isToday) {

        //             //                 logNotificationNotScheduledForToday($notificationData);
        //             //             } else {

        //             //                 $notificationPattern = 'custom_time';

        //             //                 $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //             //                 $isNotificationTime = logNotificationTime($notificationTimeData, $isToday);

        //             //                 if ($isNotificationTime) {

        //             //                     notify($notificationTime, $customTime);
        //             //                 }
        //             //             }
        //             //         }


        //             //         // else
        //             //         // {

        //             //         //     $selectedTimes = getSelectedNotificationTimes($notificationTime);

        //             //         //     foreach ($selectedTimes as $time) {

        //             //         //         if ($time === 'one_day_earlier') {

        //             //         //             $oneDayBefore = $specificDate->copy()->subDay();

        //             //         //             $notificationData['specific_date'] = $oneDayBefore;

        //             //         //             $isToday =  checkIsToday($oneDayBefore);

        //             //         //             $notificationPattern = 'one_day_earlier';

        //             //         //             if (!$isToday) {

        //             //         //                 logNotificationNotScheduledForToday($notificationData);
        //             //         //             } else {

        //             //         //                 $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //             //         //                 $isNotificationTime = logNotificationTime($notificationTimeData, $isToday);

        //             //         //                 if ($isNotificationTime) {

        //             //         //                     notify($notificationData, $customTime);
        //             //         //                 }
        //             //         //             }
        //             //         //         } else {

        //             //         //             $isToday =  checkIsToday($specificDate);

        //             //         //             if (!$isToday) {

        //             //         //                 logNotificationNotScheduledForToday($notificationData);
        //             //         //             } else {

        //             //         //                 $notificationPattern = $time;

        //             //         //                 $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

        //             //         //                 $isNotificationTime = logNotificationTime($notificationTimeData, $isToday);

        //             //         //                 if ($isNotificationTime) {

        //             //         //                     $userToNotifyID = $notificationTime->user->id;

        //             //         //                     $start = getStartDuration($task, $userToNotifyID);

        //             //         //                     $timeAlert = getDefaultTimeAlert($notificationPattern, $start);

        //             //         //                     notify($notificationData, $customTime);
        //             //         //                 }
        //             //         //             }
        //             //         //         }
        //             //         //     }
        //             //         // }
        //             //     }
        //             // }



















        //         }
        //     }
        // }

        //------------------------------------------------------------------------------------------termina o teste

        // Task::whereHas('durations', function ($query) use ($now) {
        //     $query->where('end', '<', $now);
        // })->update(['status' => 'finished']);

        $today = getToday();

        $selectedDate = $request->input('specific_date') ?? $today->format('Y-m-d');
        //Rodrigo
        // dd($selectedDate);

        $isToday = $selectedDate == $today;

        //Rodrigo
        // if ($selectedDate != $today) {
        //     dd($isToday);
        // }
        $weekDayOfSelectDate = getDayOfWeek(getCarbonDate($selectedDate));

        $weekdayInPortuguese = getDayOfWeek(getCarbonDate($selectedDate), 'pt-br');

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

                $isNotificationTimeMissing = empty($notificationTime['custom_time']) &&
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
