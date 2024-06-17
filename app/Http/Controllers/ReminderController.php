<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NotificationTime;
use App\Models\Reminder;
use App\Models\Recurring;
use App\Http\Requests\StoreReminderRequest;
use App\Models\Task;

use Carbon\Carbon;

class ReminderController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // $reminders =  Reminder::all();

        // return view('reminders/index', compact('reminders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $tasks = Task::all();

        return view('reminders/create', compact('tasks'));
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreReminderRequest $request)
    {

        $reminder = Reminder::create([

            'title' => $request->title,

            'notification_message' => $request->description,

            'user_id' => auth()->id(),

            'task_id' => $request->task,

        ]);

        $recurrings = Recurring::create(
            [
                'specific_date' => $request->specific_date ?? null,

                'sunday' => $request->sunday ?? 'false',

                'monday' => $request->monday ?? 'false',

                'tuesday' => $request->tuesday ?? 'false',

                'wednesday' => $request->wednesday ??  'false',

                'thursday' => $request->thursday ?? 'false',

                'friday' => $request->friday ?? 'false',

                'saturday' => $request->saturday ?? 'false',

                'reminder_id' => $reminder->id,

            ]
        );


        $specificNotificationTime = $request->time ? date('Y-m-d H:i:s', strtotime($request->time)) : null;

        NotificationTime::create([

            'specific_notification_time' => $specificNotificationTime,

            'reminder_id' => $reminder->id
        ]);


        return redirect()->route('home');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Reminder $reminder)
    {
        $reminder->delete();

        return redirect()->route('reminder.index');
    }
}
