<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Http\Requests\SetPendingTask;
use App\Jobs\SendInvitationEmail;
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

    public function create(Request $request)
    {
        $userID = $request->query('user');

        $alertOptions = getAlertOptions();

        $participants = User::where('id', '!=', $userID)->get();

        return view('tasks/create', compact('alertOptions', 'participants', 'userID'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTaskRequest $request)
    {

        $currentUserID = $request->userID;

        $recurrencePatterns = getRecurrencePatterns($request->all());

        $isSpecificDayPattern  = isset($recurrencePatterns['specific_date']);

        $dayOfWeekToday = strtolower(getCarbonNow()->format('l'));

        $hasRecurrenceToday = array_key_exists($dayOfWeekToday, $recurrencePatterns);

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

            'visibility' => $request->visibility,
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

        $recurringData = getRecurringData($request, $reminder);

        Recurring::create($recurringData);

        foreach ($request->all() as $attribute => $value) {

            if (str_starts_with($attribute, 'participant')) {

                Participant::create([

                    'user_id' => User::where('email', $value)->first()->id,
                    'task_id' => $task->id

                ]);
            }
        }

        $duration = Duration::create([

            'start' => $request->start,
            'end' => $request->end,
            'task_id' => $task->id,
            'user_id' => $task->created_by,

        ]);

        if ($hasRecurrenceToday) {

            $duration->status = match (true) {

                getCarbonTime($duration->start)->isFuture() => 'starting',

                getCarbonTime($duration->start)->isPast() && getCarbonTime($duration->end)->isFuture() => 'in_progress',

                getCarbonTime($duration->end)->isPast() => 'finished',

                default => $duration->status,
            };
        } else {

            $duration->status = 'starting';
        }

        $duration->save();

        $participantsEmails = getParticipantsEmail($request);

        $hasAnyParticipant = !empty($participantsEmails);

        if ($hasAnyParticipant) {

            $creator = User::where('id', $task->created_by)->first();

            $creatorName = "$creator->name $creator->lastname";

            SendInvitationEmail::dispatch($participantsEmails, $task, $creatorName);
        }

        // $isForMe =  $task->created_by->id === auth()->id();

        return redirect()->route('task.show', ['task' => $task->id])->with('success', 'Tarefa criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(int $id)
    {

        $task = Task::findOrFail($id);

        $createdBy = User::findOrFail($task->created_by);

        $createdByID =  $createdBy->id;

        $taskID = $task->id;

        $possibleParticipants = User::whereDoesntHave('participatingTasks', function ($query) use ($taskID) {
            $query->where('task_id', $taskID);
        })->where('id', '!=', $createdByID)->get();

        if (isset($task)) {

            $view = request()->query('view', 'default');

            $task->creator_name = $createdBy->name . ' ' . $createdBy->lastname;

            $task->is_creator = $createdBy->id === auth()->id();

            $task->is_participant = $task->participants()->where(function ($query) {

                $query->where('user_id', auth()->id())->where('status', 'accepted');
            })->exists();

            $task->creator_email = $createdBy->email;

            $task->description = $task->feedbacks->first()->feedback;

            $task->attachments = $task->feedbacks->first()->attachments->all();

            $duration = getDuration($task);

            $task->start = date('H:i', strtotime($duration->start));

            $task->end = date('H:i', strtotime($duration->end));

            $recurring = $task->reminder->recurring;

            $task->recurringMessage = getRecurringMessage($recurring);

            $alertOptions = getAlertOptions();

            $hasSpecificDate = filled($task->reminder->recurring->specific_date);

            $expiredTask = $duration->status === 'finished';

            $task->shoudDisplayButton = !($hasSpecificDate && $expiredTask);

            $task->isConcluded = $task->concluded === 'true';

            $task->emailsParticipants = $task->participants->isEmpty()
                ? "Nenhum participante"
                : $task->participants->pluck('email')->implode(', ');

            $today = getToday();

            $task->status = $duration->status;

            $task->notificationAlert = getAlertAboutNotificationTime($task);

            // dd($task->notificationAlert);

            $task->shouldHiddenTimeAlertsOptions = in_array($task->notificationAlert, getSpecificDayAlerts(), true);

            $task->shouldDisplayRecurringTimeAlert = in_array($task->notificationAlert, getRecurringAlerts(), true);

            return $view === 'pending'
                ?  view('tasks/showPending', compact('task', 'alertOptions'))
                :  view('tasks/show', compact('possibleParticipants', 'task'));
        } else {

            return redirect('display.day');
        }
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {
        $userID  = auth()->id();

        $task = Task::findOrFail($id);

        $alertOptions = getAlertOptions();

        $participants = User::where('id', '!=', auth()->id())->get();

        return view('tasks/edit', compact('alertOptions', 'participants', 'task'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTaskRequest $request, int $id)
    {
        $task =  Task::with(

            'reminder',

            'reminder.recurring',

            'durations',

            'feedbacks',

            'participants'

        )->findOrFail($id);

        $recurrencePatterns = getRecurrencePatterns($request->all());
        $isSpecificDayPattern  = isset($recurrencePatterns['specific_date']);

        if ($isSpecificDayPattern) {

            $conflict = getConflictingTask($request->all(), 'specific_date', $task->id);

            if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                return $conflict;
            }
        } else {

            foreach (array_keys($recurrencePatterns) as $pattern) {

                $conflict = getConflictingTask($request,  $pattern, $task->id);

                if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                    return $conflict;
                }
            }
        }

        $recurring = $task->reminder->recurring;

        $duration = getDuration($task);

        $notificationTime  = $task->reminder->notificationTimes()->where('user_id', auth()->id())->first();

        $taskAttributes  = $request->only('title', 'local', 'concluded');

        $recurringAttributes = getRecurringData($request, $task->reminder);

        $durationAttributes = $request->only('start', 'end');

        $notificationTimeAttributes = [

            'custom_time' => $request->time,

            'half_an_hour_before' => $request->half_an_hour_before ?? 'false',

            'one_hour_before' => $request->one_hour_before ?? 'false',

            'two_hours_before' => $request->two_hours_before ?? 'false',

            'one_day_earlier' => $request->one_day_earlier ?? 'false',

        ];

        $task->feedbacks()->first()->update(['feedback' => $request->description]);

        $task->update($taskAttributes);

        $recurring->update($recurringAttributes);

        $duration->update($durationAttributes);

        $duration->status = match (true) {

            getCarbonTime($duration->start)->isFuture() => 'starting',

            getCarbonTime($duration->start)->isPast() && getCarbonTime($duration->end)->isFuture() => 'in_progress',

            getCarbonTime($duration->end)->isPast() => 'finished',

            default => $duration->status,
        };

        $duration->save();

        $notificationTime->update($notificationTimeAttributes);

        return redirect()->route('home')->with('success', 'Tarefa atualizada com sucesso!');
    }

    public function acceptPendingTask(SetPendingTask $request, int $id)
    {

        $currentUserID = auth()->id();

        $currentTask = Task::findOrFail($id);

        $currentTaskRecurring = $currentTask->reminder->recurring->getAttributes();

        $recurrencePatterns = getRecurrencePatterns($currentTaskRecurring);

        $isSpecificDayPattern = isset($recurrencePatterns['specific_date']);

        if ($isSpecificDayPattern) {

            $inputData = $request->all() + $recurrencePatterns;

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

        $duration = getDuration($currentTask);

        $duration = Duration::create([

            'start' => $startTime,
            'end' => $endTime,
            'task_id' => $id,
            'user_id' => $currentUserID,
            'status' => $duration->status

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

        return redirect()->route('home')->with('success', 'Tarefa aceita com sucesso!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function markAsConcluded(int $id)
    {
        $task =  Task::findOrFail($id);

        $task->participants()->wherePivot('status', 'pending')->detach();

        $task->update(['concluded' => true]);

        return  redirect()->route('home')->with('success', 'Tarefa concluída!');
    }
}
