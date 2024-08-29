<?php

namespace App\Http\Controllers;

use App\Helpers\QueryHelpers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\SetPendingTask;
use App\Jobs\SendInvitationEmail;
// use App\Models\NotificationTime;
use App\Models\Attachment;
use App\Models\Duration;
use App\Models\Feedback;
use App\Models\NotificationTime;
use App\Models\Task;
use App\Models\User;
use App\Models\Participant;
use App\Models\Recurring;
use App\Models\Reminder;
use Illuminate\Http\Request;

class TaskController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index() {}

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $alertOptions = getAlertOptions();

        $participants = User::where('id', '!=', auth()->id())->get();

        return view('tasks/create', compact('alertOptions', 'participants'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {
        // rodrigo
        // dd($request->all());
        $currentUserID = auth()->id();
        $recurrencePatterns = getRecurrencePatterns($request->all());

        //rodrigo
        // dd($recurrencePatterns);
        $isSpecificDayPattern  = isset($recurrencePatterns['specific_date']);

        //rodrigo
        // dd($isSpecificDayPattern);

        if ($isSpecificDayPattern) {

            $conflict = getConflictingTask($request->all(), 'specific_date');

            if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                return $conflict;
            }
        } else {

            foreach (array_keys($recurrencePatterns) as $pattern) {

                $conflict = getConflictingTask($request,  $pattern);

                if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                    return $conflict;
                }
            }
        }

        $task = Task::create([

            'title' => $request->title,

            'local' => $request->local ?? null,

            'created_by' => $currentUserID,
        ]);

        $reminder = Reminder::create([

            'task_id' => $task->id
        ]);

        $feedback = Feedback::create([

            'feedback' => $request->description,

            'user_id' => auth()->id(),

            'task_id' => $task->id,
        ]);

        $attachments = $request->task_attachments;

        if (isset($attachments)) {

            foreach ($attachments  as  $attachment) {

                $attachmentName = $attachment . '.' . $attachment->getClientOriginalExtension();

                $path = $attachment->storeAs('task-attachments', $attachmentName);

                Attachment::create([

                    'path' => $path,

                    'feedback_id' => $feedback->id,

                ]);
            }
        }

        $customTime = $request->time ? date('H:i', strtotime($request->time)) : null;

        NotificationTime::create([

            'custom_time' => $customTime,

            'half_an_hour_before' => $request->half_an_hour_before ?? 'false',

            'one_hour_before' => $request->one_hour_before ?? 'false',

            'two_hours_before' => $request->two_hours_before ?? 'false',

            'one_day_earlier' => $request->one_day_earlier ?? 'false',

            'reminder_id' => $task->reminder->id,

            'user_id' => auth()->id(),

        ]);

        $recurringData = getRecurringData($request, $isSpecificDayPattern, $reminder);

        Recurring::create($recurringData);

        foreach ($request->all() as $attribute => $value) {

            if (str_starts_with($attribute, 'participant')) {

                Participant::create([

                    'user_id' => User::where('email', $value)->first()->id,
                    'task_id' => $task->id

                ]);
            }
        }

        Duration::create([

            'start' => $request->start,
            'end' => $request->end,
            'task_id' => $task->id,
            'user_id' => auth()->id(),

        ]);

        $participantsEmails = getParticipantsEmail($request);
        //Rodrigo
        // dd($participantsEmail);

        $hasAnyParticipant = !empty($participantsEmails);
        //rodrigo
        // dd($hasAnyParticipant);

        if ($hasAnyParticipant) {
            //Rodrigo
            // dd($hasAnyParticipant);

            $creator = User::where('id', $task->created_by)->first();

            $creatorName = "$creator->name $creator->lastname";

            SendInvitationEmail::dispatch($participantsEmails, $task, $creatorName);
        }

        $feedbacks = Feedback::all();

        return redirect()->route('task.show', compact('task'))->with('success', 'Tarefa criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $task = Task::find($id);

        $taskID = $task->id;

        $possibleParticipants = User::whereDoesntHave('participatingTasks', function ($query) use ($taskID) {
            $query->where('task_id', $taskID);
        })->where('id', '!=', auth()->id())->get();

        // dd($possibleParticipants);

        if (isset($task)) {

            $view = request()->query('view', 'default');

            $createdBy =  $task->creator ?? null;

            $task['creator'] = $createdBy->name . ' ' . $createdBy->lastname;

            $task['creator_telephone'] = getFormatedTelephone($createdBy);

            $task['creator_email'] = $createdBy->email;

            $task['description'] = $task->feedbacks->first()->feedback;

            $task['attachments'] = $task->feedbacks->first()->attachments->all();

            $duration = $task->durations->first();

            $task['start'] = $duration->start ? date('H:i', strtotime($duration->start)) : null;

            $task['end'] = $duration->end ? date('H:i', strtotime($duration->end)) : null;

            $recurring = $task->reminder->recurring;

            $task['recurringMessage'] = getRecurringMessage($recurring);

            $alertOptions = getAlertOptions();

            if ($task->participants->isEmpty()) {

                $task->emailsParticipants = "Nenhum participante";
            } else {

                $task->emailsParticipants = $task->participants->pluck('email')->implode(', ');
            }
            if ($task->participants->isEmpty()) {

                $task->emailsParticipants = "Nenhum participante";
            } else {

                $task->emailsParticipants = $task->participants->pluck('email')->implode(', ');
            }

            $today = getToday();

            if (isset($recurring->specific_date)) {

                $specificDate = getCarbonDate($recurring->specific_date);

                $isPast = $specificDate->isBefore($today);

                $isTodaySpecificDate = checkIsToday($specificDate);

                if ($isPast) {

                    $task['tatus'] = 'finished';
                } elseif ($isTodaySpecificDate) {

                    $task['status'] = getTaskStatus($duration);
                } else {

                    $task['status'] = 'starting';
                }
            } else {

                $todayWeekday = getDayOfWeek($today);

                $repeatingDays = getRepeatingDays($recurring);

                foreach ($repeatingDays as $day) {

                    if ($day ===  $todayWeekday) {

                        $task['status'] = getTaskStatus($duration);
                    } else {
                        $task['status'] = 'finished';
                    }
                }
            }

            return $view === 'pending'
                ?  view('tasks/showPending', compact('task', 'alertOptions'))
                :  view('tasks/show', compact('possibleParticipants', 'task'));
        } else {

            return redirect('home');
        }
    }

    // public function showPending(string $id)
    // {
    //     $task = Task::find($id);

    //     return view('tasks/showPending', compact('task'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $user  = auth()->id();

        $task = Task::find($id);

        $alertOptions = getAlertOptions();

        $participants = User::where('id', '!=', auth()->id())->get();

        return view('tasks/edit', compact('alertOptions', 'participants', 'task'));

        // $task->created_by === $user ?

        // if()

    }

    /**
     * Update the specified resource in storage.
     */
    public function update(StoreTaskRequest $request, int $id)
    {


        return redirect('home');
    }

    public function acceptPendingTask(SetPendingTask $request, string $id)
    {
        //rodrigo
        // dd($request->all());

        $currentUserID = auth()->id();

        $currentTask = Task::find($id);

        $currentTaskRecurring = $currentTask->reminder->recurring->getAttributes();
        //rodrigo
        // dd($currentTaskRecurring);

        $recurrencePatterns = getRecurrencePatterns($currentTaskRecurring);
        //rodrigo
        // dd($recurrencePatterns);

        $isSpecificDayPattern = isset($recurrencePatterns['specific_date']);
        //rodrigo
        // dd($isSpecificDayPattern);

        if ($isSpecificDayPattern) {

            $inputData = $request->all() + $recurrencePatterns;
            //rodrigo
            // dd($inputData);

            $conflict = getConflictingTask($inputData, 'specific_date', $id);

            if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                return $conflict;
            }
        } else {

            foreach (array_keys($recurrencePatterns) as $pattern) {

                $conflict = getConflictingTask($request,  $pattern, $id);

                if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                    return $conflict;
                }
            }
        }

        $startTime = $request->start ? date('H:i', strtotime($request->start)) : null;
        $endTime = $request->end ? date('H:i', strtotime($request->end)) : null;

        Duration::create([

            'start' => $startTime,
            'end' => $endTime,
            'task_id' => $id,
            'user_id' => $currentUserID

        ]);

        $customTime = $request->time ? date('H:i', strtotime($request->time)) : null;

        NotificationTime::create([

            'custom_time' => $customTime,

            'half_an_hour_before' => $request->half_an_hour_before ?? 'false',

            'one_hour_before' => $request->one_hour_before ?? 'false',

            'two_hours_before' => $request->two_hours_before ?? 'false',

            'one_day_earlier' => $request->one_day_earlier ?? 'false',

            'reminder_id' => $currentTask->reminder->id,

            'user_id' => auth()->id(),

        ]);

        $participant = Participant::where('user_id', $currentUserID)->where('task_id', $id)->first();
        $participant->status = 'accepted';

        $participant->save();

        return redirect('home');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
