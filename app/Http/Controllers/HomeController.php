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

    public function index($recurrencePattern = 'tuesday')
    {

        // $daysInMonth = Carbon::now()->daysInMonth;

        // $dayOfWeek = strtolower(Carbon::now()->format('l'));

        // $today = Carbon::today()->format('Y-m-d');
        // Rodrigo
        // dd($today);

        // $isThereAnyUser = Auth::check();

        $currentUserID = Auth::id();

        $currentUserReminders = Reminder::whereNotNull('user_id')->where('user_id', auth()->id())->get();

        $isThereAnyReminder = $currentUserReminders->isNotEmpty();

        // $currentUser = Auth::user();

        $currentUserTasksBuilder = Task::with('participants')->whereHas('participants', function ($query) use ($currentUserID) {
            $query->where('user_id', $currentUserID)
                ->where('status', 'accepted');
        })->orWhere('created_by', $currentUserID);

        $isASpecificRecurrence = $recurrencePattern === "spec";

        $currentUserPatternsTasks = null;

        if ($isASpecificRecurrence) {

            $currentUserPatternsTasks = $currentUserTasksBuilder->with('reminder', 'reminder.recurring')->whereHas('reminder', function ($currentUserTasksReminderQuery) {
                $currentUserTasksReminderQuery->whereHas('recurring', function ($currentUserTasksReminderRecurringQuery) {
                    $currentUserTasksReminderRecurringQuery->whereNotNull('specific_date');
                });
            })->get();
        } else {
            $currentUserPatternsTasks = getRecurringTasks($recurrencePattern, $currentUserTasksBuilder)->get();
        }

        // dd($currentUserPatternsTasks);

        // dd($currentUserSpecificDateTasks->all());

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

        return view('home', compact('isThereAnyReminder', 'myTasks', 'currentUser', 'currentUserReminders'));
    }
}
