<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobSendSpecificTimeNotifications implements ShouldQueue
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
        $notificationTimes = getNotificationtimes('specific_notification_time');

        $notificationPattern = 'specific_notification_time';

        $usersEmailsToNotificationsTimes = array();

        foreach ($notificationTimes as $notificationTime) {

            $user = $notificationTime->user;

            $startReference = $notificationTime->reminder
                ->task->durations
                ->where('user_id', $user->id)
                ->first()
                ->start;

            $startReference;

            if ($notificationPattern === 'specific_notification_time') {
                $carbonStartReference = Carbon::createFromFormat('H:i:s', $startReference);

                //     $notificationTime = $carbonStartReference->subMinutes(30)->format('H:i:s');
                dd($notificationTime->specific_notification_time);
            }
        }
    }
}
