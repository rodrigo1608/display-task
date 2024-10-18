<?php

namespace App\Jobs;

use App\Models\NotificationTime;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

use Illuminate\Support\Facades\Log;

class NotifyAtCustomTime implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('');
        Log::info('Job NotifyAtCustomTime: INÍCIO');

        $notificationTimes = NotificationTime::with('reminder')->whereHas('reminder', function ($query) {

            $query->where('available', 'true');
        })->get();

        if (!$notificationTimes->isEmpty()) {

            foreach ($notificationTimes as $notificationTime) {

                $reminder = $notificationTime->reminder;

                $isTask = is_null($reminder->user_id);

                $task = $isTask ? $notificationTime->reminder->task : null;

                $reminder = $notificationTime->reminder;

                $isUnavaliableReminder = !$isTask ? $reminder->available === 'false' : null;

                $isConcludedTask  = $isTask ? $task->concluded === 'true' : null;

                $isInvalidStatusNotificaiton  = $isUnavaliableReminder ||  $isConcludedTask;

                if ($isInvalidStatusNotificaiton) {

                    Log::info('Job NotifyAtCustomTime:');
                    Log::info('Job NotifyAtCustomTime: Não foram encontradas notificações pendentes');
                } else {
                    $recurring = $notificationTime->reminder->recurring;

                    $hasSpecificDate = !is_null($recurring->specific_date);

                    $task = $notificationTime->reminder->task()->where('concluded', 'false')->first();

                    $userToNotify = $notificationTime->user;

                    $notificationData = [

                        'has_specific_date' => $hasSpecificDate,

                        'notification_time' => $notificationTime,

                        'recurring' => $recurring,

                        'task' => $task,

                        'user_to_notify' => $userToNotify,

                    ];

                    if ($hasSpecificDate) {

                        $specificDate = getCarbonDate($recurring->specific_date);

                        Log::info('Job NotifyAtCustomTime:');
                        Log::info('Job NotifyAtCustomTime: ' . getRecurringLog($notificationTime) . ' - Recurring ID: ' . $recurring->id);

                        $hasCustomTime = !is_null($notificationTime->custom_time);

                        if ($hasCustomTime) {

                            $customTime = getCarbonTime($notificationTime->custom_time);

                            $isToday = checkIsToday($specificDate);

                            if (!$isToday) {

                                $notificationData['scheduled_date'] = $specificDate;

                                logNotificationNotScheduledForToday($notificationData);
                            } else {

                                $notificationPattern = 'custom_time';

                                $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

                                $isNotificationTime = logNotificationTime($notificationTimeData, $isToday);

                                if ($isNotificationTime) {

                                    notify($notificationTime, $customTime);
                                }
                            }
                        } else {

                            $selectedTimes = getSelectedPredefinedAlerts($notificationTime);

                            foreach ($selectedTimes as $time) {

                                if ($time === 'one_day_earlier') {

                                    $isTodayADayBefore = checkIsDayBefore($specificDate);

                                    if (!$isTodayADayBefore) {

                                        $notificationData['scheduled_date'] = $specificDate->copy()->subDay();

                                        logNotificationNotScheduledForToday($notificationData);
                                    } else {

                                        $notificationPattern = 'one_day_earlier';

                                        $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

                                        $isNotificationTime = logNotificationTime($notificationTimeData, $isTodayADayBefore);

                                        if ($isNotificationTime) {

                                            $start = getStartDuration($task, $userToNotify);
                                            $alertTime = $start->copy()->subDay();

                                            notify($notificationTime, $alertTime);
                                        }
                                    }
                                } else {

                                    $isToday = checkIsToday($specificDate);

                                    if (!$isToday) {

                                        $notificationData['scheduled_date'] = $specificDate;
                                        logNotificationNotScheduledForToday($notificationData);
                                    } else {

                                        $notificationPattern = $time;

                                        $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

                                        $isNotificationTime = logNotificationTime($notificationTimeData, $isToday);

                                        if ($isNotificationTime) {

                                            $userToNotify = $notificationTime->user;

                                            $start = getStartDuration($task, $userToNotify);

                                            $alertTime = getDefaultTimeAlert($notificationPattern, $start);

                                            notify($notificationTime, $alertTime);
                                        }
                                    }
                                }
                            }
                        }
                    } else {

                        $isTask = !is_null($task);

                        Log::info('Job NotifyAtCustomTime:');
                        Log::info('Job NotifyAtCustomTime: ' . getNotificationContextSnippet($isTask) . ' para se repetir semanalmente em dias selecionados - Recurring ID: ' . $recurring->id);

                        $recurringDays = getRepeatingDays($recurring);

                        $today = getToday();
                        $todayWeekday = getDayOfWeek($today);
                        $oneDayAfterWeekday = getDayOfWeek($today->copy()->addDay());

                        // dd($oneDayAfterWeekday);
                        // dd($todayWeekday);

                        $hasCustomTime = !is_null($notificationTime->custom_time);

                        if ($hasCustomTime) {

                            $customTime = getCarbonTime($notificationTime->custom_time);

                            $hasNoTodayWeekday = !in_array($todayWeekday, $recurringDays);

                            if ($hasNoTodayWeekday) {

                                $scheduledDate = [];

                                foreach ($recurringDays  as $day) {

                                    $scheduledDate[] = getDaysOfWeek()[$day];
                                }

                                $notificationData['scheduled_date'] = $scheduledDate;

                                logNotificationNotScheduledForToday($notificationData);
                            } else {

                                $notificationPattern = 'custom_time';

                                $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

                                $isNotificationTime = logNotificationTime($notificationTimeData, $todayWeekday);

                                if ($isNotificationTime) {

                                    notify($notificationTime, $customTime);
                                }
                            }
                        } else {

                            $selectedTimes = getSelectedNotificationTimes($notificationTime);

                            foreach ($selectedTimes as $time) {

                                if ($time === 'one_day_earlier') {

                                    $isOneDayAfterWeekday = in_array($oneDayAfterWeekday, $recurringDays);

                                    if (!$isOneDayAfterWeekday) {

                                        // $scheduledDate = getDaysOfWeek()[strtolower(Carbon::parse($oneDayAfterWeekday)->subDay()->format('l'))];

                                        $oneDayAfterWeekdays = [];

                                        foreach ($recurringDays  as $day) {

                                            $oneDayAfterWeekdays[] = getDaysOfWeek()[strtolower(getCarbonDate($day)->subDay()->format('l'))];
                                        }

                                        $notificationData['scheduled_date'] = $oneDayAfterWeekdays;

                                        logNotificationNotScheduledForToday($notificationData);
                                    } else {

                                        $notificationPattern = 'one_day_earlier';

                                        $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

                                        $isNotificationTime = logNotificationTime($notificationTimeData, $isOneDayAfterWeekday);

                                        if ($isNotificationTime) {

                                            $start = getStartDuration($task, $userToNotify);

                                            $alertTime = $start->copy()->subDay();

                                            notify($notificationTime, $alertTime);
                                        }
                                    }
                                } else {

                                    $todayWeekday = in_array($todayWeekday, $recurringDays);

                                    if (!$todayWeekday) {

                                        $alertDays = [];

                                        foreach ($recurringDays  as $day) {
                                            $alertDays[] = getDaysOfWeek()[$day];
                                        }


                                        $notificationData['scheduled_date'] = $alertDays;

                                        logNotificationNotScheduledForToday($notificationData);
                                    } else {

                                        $notificationPattern = $time;

                                        $notificationTimeData = getNotificationTimeData($notificationTime, $notificationPattern);

                                        $isNotificationTime = logNotificationTime($notificationTimeData, $todayWeekday);

                                        if ($isNotificationTime) {

                                            $userToNotify = $notificationTime->user;

                                            $start = getStartDuration($task, $userToNotify);

                                            $alertTime = getDefaultTimeAlert($notificationPattern, $start);

                                            notify($notificationTime, $alertTime);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            Log::info('Job NotifyAtCustomTime:');
            Log::info('Job NotifyAtCustomTime: Não foram encontradas notificações pendentes');
        }

        Log::info('Job NotifyAtCustomTime:');
        Log::info('Job NotifyAtCustomTime: FIM');
    }
}
