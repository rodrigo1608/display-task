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

    public function index()
    {

        $daysInMonth = Carbon::now()->daysInMonth;

        $dayOfWeek = strtolower(Carbon::now()->format('l'));

        $today = Carbon::today()->format('Y-m-d');
        // Rodrigo
        // dd($today);

        $isThereAnyUser = Auth::check();

        $userReminders = Reminder::whereNotNull('user_id')->where('user_id', auth()->id())->get();

        $isThereAnyReminder = $userReminders->isNotEmpty();

        $currentUser = Auth::user();

        $currentUserID = $currentUser->id;

        $currentUser->telephone = '(' . substr($currentUser->telephone, 0, 2) . ') ' . substr($currentUser->telephone, 2, 1) . ' ' . substr($currentUser->telephone, 3);

        $myTasksBuilder = Task::whereHas('participants', function ($query) use ($currentUserID) {

            $query->where('user_id', $currentUserID)
                ->where('status', 'accepted');
        })->orWhere('created_by', $currentUserID);

        $myTasksToday = $myTasksBuilder->whereHas('reminder', function ($query) use ($today, $dayOfWeek) {

            $query->whereHas('recurring', function ($recurringQuery) use ($today, $dayOfWeek) {

                $recurringQuery->where('specific_date', $today)
                    ->orWhere($dayOfWeek, true);
            });
        })->get();

        $myTasks =  $myTasksBuilder->get();

        foreach ($myTasks as $task) {

            // rodrigo
            // dd($task);

            $duration = Duration::where('task_id', $task->id)->where('user_id', $currentUserID)->first();

            $durationExist = $duration !== null;

            // rodrigo
            // dd($duration->start);

            $task['start'] = $durationExist ? Carbon::parse($duration->start)->format('H:i') : null;

            $task['end'] = $durationExist ? Carbon::parse($duration->end)->format('H:i') : null;

            $currentTime = Carbon::now();

            // $timeDifference = $currentTime->diffForHumans($duration->start, [
            //     'parts' => 2,
            //     'join' => true,
            //     'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
            // ]);
            // $task['time_difference'] = $timeDifference;
        }

        foreach ($myTasksToday as $task) {

            // Rodrigo
            // dd($task->reminder->recurring);

            $duration = Duration::where('task_id', $task->id)->where('user_id', $currentUserID)->first();

            $durationExist = $duration !== null;

            // Rodrigo
            // dd($duration);

            if ($durationExist) {

                // rodrigo
                // dd(Carbon::parse($duration->start));

                $startTime = Carbon::parse($duration->start);

                $task['start'] =  $startTime;

                $endTime = Carbon::parse($duration->end);

                $task['end'] = $endTime;

                $currentTime = Carbon::now();

                $timeDifferenceInMinutes = $startTime->diffInMinutes($currentTime, false); // Add false to keep the negative value if startTime is in the past

                $task['time_difference'] = $timeDifferenceInMinutes;

                // dd($task['time_difference']);
            }
        }

        // $startTime = Carbon::parse($task->start);

        // $currentTime = Carbon::now();

        // $timeDifference = $startTime->diffForHumans($currentTime, [
        //     'parts' => 2,
        //     'join' => true,
        //     'syntax' => \Carbon\CarbonInterface::DIFF_RELATIVE_TO_NOW,
        // ]);

        return view('home', compact('isThereAnyReminder', 'myTasks', 'myTasksToday', 'currentUser', 'userReminders'));
    }
}
