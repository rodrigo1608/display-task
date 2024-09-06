<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\NotificationTime;
use App\Models\Reminder;
use App\Models\Recurring;
use App\Http\Requests\StoreReminderRequest;

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

        // $tasks = Task::all();

        return view('reminders/create');
    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(StoreReminderRequest $request)
    {
        $reminder = Reminder::create([

            'title' => $request->title,

            'notification_message' => $request->notification_message,

            'user_id' => auth()->id(),

        ]);

        $recurrencePatterns = getRecurrencePatterns($request->all());

        $isSpecificDayPattern  = isset($recurrencePatterns['specific_date']);

        $recurringData = getRecurringData($request,  $reminder);
        //rodrigo
        // dd($request->all(), $isSpecificDayPattern);

        Recurring::create($recurringData);

        $customTime = $request->time ? date('H:i:s', strtotime($request->time)) : null;

        NotificationTime::create([

            'custom_time' => $customTime,
            'reminder_id' => $reminder->id,
            'user_id' => auth()->id(),
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
