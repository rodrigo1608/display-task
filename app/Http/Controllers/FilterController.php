<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Duration;
use App\Models\Feedback;
use App\Models\NotificationTime;
use App\Models\Task;
use App\Models\User;
use App\Models\Participant;
use App\Models\Recurring;
use App\Models\Reminder;
use Carbon\Carbon;

use Illuminate\Http\Request;

class FilterController extends Controller
{




    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    public function searchByTitle(Request $request)
    {
        $currentUserID = auth()->id();

        $tasksFilteredByTitle = Task::where('concluded', 'false')->where('created_by', $currentUserID)->orWhereHas('participants', function ($query) use ($currentUserID) {
            $query->where('user_id', $currentUserID)->where('status', 'accepted');
        })->where('title', 'LIKE', "%$request->title_filter%")->get();;







        return view('tasks/filtered', compact('tasksFilteredByTitle'));
    }


    public function getTasksCreatedByMe(Request $request)
    {
        $userID = auth()->id();


        $tasksFilteredByTitle = Task::where('concluded', 'false')->where('title', 'LIKE', "%$request->title_filter%")->get();
    }
}
