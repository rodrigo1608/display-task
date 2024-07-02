<?php

namespace App\Http\Controllers;

use App\Mail\TaskInvitationMail;
use App\Helpers\QueryHelpers;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\SetPendingTaskDuration;
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
use Carbon\Carbon;

use Illuminate\Http\Request;

use Mail;

class TaskController extends Controller
{

    public function __construct()
    {
        $this->middleware(['auth', 'verified']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

        $alertOptions = [

            'half_an_hour_before' => 'Meia hora antes',

            'one_hour_before' => 'Uma hora antes',

            'two_hours_before' => 'Duas horas antes',

            'one_day_earlier' => 'Um dia antes'

        ];

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

        // dd($recurrencePatterns['specific_date']);

        $isSpecificDayPattern  = isset($recurrencePatterns['specific_date']);

        // dd($isSpecificDayPattern);

        if ($isSpecificDayPattern) {

            $conflict = getConflictingTask($request->all(), 'specific_date',   $recurrencePatterns['specific_date']);

            if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                return $conflict;
            }
        }

        foreach (array_keys($recurrencePatterns) as $pattern) {

            $conflict = getConflictingTask($request,  $pattern);

            if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                return $conflict;
            }
        }

        // getConflictingTask($request, 'specific_date');
        // $hasMondayRecurrenceInRequest:

        // $hasTuesdayRecurrenceInRequest:

        // $hasWednesdayRecurrenceInRequest:

        // $hasThursdayRecurrenceInRequest:



        // $hasFridayRecurrenceInRequest:



        // $hasSaturdayRecurrenceInRequest:



        // $specific_date)
        //     ->where('created_by', $currentUserID)->orWhereHas('paticipating', function ($query) use ($currentUserID) {
        //         $query->where('user_id', $currentUserID);
        //     })
        //     ->exists();

        //Rodrigo
        // dd($request->specific_date);

        // $currentUserRecurrences = Recurring::where('available', 'true')
        //     ->whereHas('reminder', function ($reminderQuery) use ($currentUserID) {
        //         $reminderQuery->whereHas('task', function ($taskQuery) use ($currentUserID) {
        //             $taskQuery->where('created_by', $currentUserID)->orWhereHas('participants', function ($participantQuery) use ($currentUserID) {
        //                 $participantQuery->where('status', 'accepted')->where('user_id', $currentUserID);
        //             });
        //         });
        //     })->get();

        // $currentUserRecurrences = Recurring::where('available', 'true')
        //     ->where(function ($query) use ($currentUserID, $request) {
        //         $query->where('specific_date', $request->specific_date)
        //             ->orWhere(function ($query) use ($request) {
        //                 $query->where('monday', $request->monday)
        //                     ->orWhere('tuesday', $request->tuesday)
        //                     ->orWhere('wednesday', $request->wednesday)
        //                     ->orWhere('thursday', $request->thursday)
        //                     ->orWhere('friday', $request->friday)
        //                     ->orWhere('saturday', $request->saturday);
        //             });
        //     })
        //     ->whereHas('reminder.task', function ($taskQuery) use ($currentUserID) {
        //         $taskQuery->where('created_by', $currentUserID)
        //             ->orWhereHas('participants', function ($participantQuery) use ($currentUserID) {
        //                 $participantQuery->where('status', 'accepted')->where('user_id', $currentUserID);
        //             });
        //     })
        //     ->get();

        // Rodrigo
        // dd($currentUserRecurrences);

        // $taskDuration = ["start" => $request->start, 'end' => $request->end];



        //Rodrigo
        // dd($taskDuration);

        // $idTaskRecurrenceASpecificDate = isset($request->specific_date);

        // $weekDayOfCurrentTask = "";

        // if ($idTaskRecurrenceASpecificDate) {
        //     $weekDayOfCurrentTask = getWeekDayName($request->specific_date);
        // }

        // Verificar se há alguma duração que conflita dentro desta recorrência
        // foreach ($currentUserRecurrences as $userRecurrence) {

        // dd($userRecurrence->specific_date);

        // dd($taskRecurrenceIsASpecificDate);

        // if ($taskRecurrenceIsASpecificDate) {

        //     $date = $userRecurrence->specific_date;

        //     $carbonDate = Carbon::parse($date);

        //     $dayName = $carbonDate->englishDayOfWeek;

        //     dd($dayName);
        // }

        // $userTaskDurations = $userRecurrence->reminder->task->durations();

        // $conflictingDuration = $userTaskDurations->where(function ($userTasksDurationQuery) use ($request) {
        //     $userTasksDurationQuery->where('start', '>=', $request->start)
        //         ->where('start', '<', $request->end)
        //         ->orWhere(function ($startOverlapQuery) use ($request) {
        //             $startOverlapQuery->where('end', '>', $request->start)
        //                 ->where('end', '<=', $request->end);
        //         })
        //         ->orWhere(function ($intervalOverlapQuery) use ($request) {
        //             $intervalOverlapQuery->where('start', '<=', $request->start)
        //                 ->where('end', '>=', $request->end);
        //         });
        // })->exists();

        // foreach ($currentUserRecurrences as $userRecurrence) {

        //     $userTaskDurations = $userRecurrence->reminder->task->durations();

        //     // rodrigo
        //     // dd($userRecurrence);

        //     $conflictingDuration = $userTaskDurations->where(function ($userTasksDurationQuery) use ($taskDuration) {

        //         $userTasksDurationQuery->where('start', '>=', $taskDuration['start'])
        //             ->where('start', '<', $taskDuration['end'])
        //             ->orWhere(function ($startOverlapQuery) use ($taskDuration) {

        //                 $startOverlapQuery->where('end', '>', $taskDuration['start'])
        //                     ->where('end', '<=', $taskDuration['end']);
        //             })
        //             ->orWhere(function ($intervalOverlapQuery) use ($taskDuration) {
        //                 $intervalOverlapQuery->where('start', '<=', $taskDuration['start'])
        //                     ->where('end', '>=', $taskDuration['end']);
        //             });
        //     })->exists();

        // if ($conflictingDuration) {

        //     $conflictingTask = $userRecurrence->reminder->task;

        //     $conflictingTaskToArray =  $conflictingTask->toArray();

        //     $conflictingTaskToArray['owner'] = $conflictingTask->creator->name . ' ' . $conflictingTask->creator->lastname;

        //     $conflictingTaskToArray['owner_telehpone'] =  getFormatedTelephone($conflictingTask->creator);

        //     $conflictingTaskToArray['owner_email'] =  $conflictingTask->creator->email;

        //     $conflictingDuration =  $conflictingTask->durations->first();

        //     $conflictingTaskToArray['start'] = date('H:i', strtotime($conflictingDuration->start));

        //     $conflictingTaskToArray['end'] =  date('H:i', strtotime($conflictingDuration->end));

        //     $recurring = $conflictingTask->reminder->recurring;

        //rodrigo
        // dd($recurring);

        // $conflictingTaskToArray['recurringMessage'] = getRecurringMessage($recurring);

        // Rodrigo
        // dd($conflictingTaskToArray);

        // session()->flash('conflictingTask',  $conflictingTaskToArray);
        // dd(session()->get('conflictingTask'));

        //         return redirect()->back()->withErrors([
        //             'conflictingDuration' =>
        //             $userRecurrence->reminder->task->title,
        //         ])->withInput();
        //     }
        // }

        $task = Task::create([

            'title' => $request->title,

            'local' => $request->local ?? null,

            'created_by' => $currentUserID,
        ]);

        $reminder = Reminder::create([

            'title' =>  $task->title,

            'notification_message' => $request->description,

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

        $specificNotificationTime = $request->time ? date('H:i', strtotime($request->time)) : null;

        $notificationData = [

            'specific_notification_time' => $specificNotificationTime,

            'half_an_hour_before' => $request->half_an_hour_before ?? 'false',

            'one_hour_before' => $request->one_hour_before ?? 'false',

            'two_hours_before' => $request->two_hours_before ?? 'false',

            'one_day_earlier' => $request->one_day_earlier ?? 'false',

            'reminder_id' => $task->reminder->id

        ];

        $recurringData = [

            'specific_date' => $request->specific_date ?? null,

            'specific_date_weekday' => getWeekDayName($request->specific_date) ?? null,

            'sunday' => $request->sunday ?? 'false',

            'monday' => $request->monday ?? 'false',

            'tuesday' => $request->tuesday ?? 'false',

            'wednesday' => $request->wednesday ??  'false',

            'thursday' => $request->thursday ?? 'false',

            'friday' => $request->friday ?? 'false',

            'saturday' => $request->saturday ?? 'false',

            'reminder_id' => $reminder->id,

        ];

        $recurring = Recurring::create($recurringData);

        NotificationTime::create($notificationData);

        foreach ($request->all() as $key => $value) {

            if (str_starts_with($key, 'participant')) {

                Participant::create([

                    'user_id' => User::where('email', $value)->first()->id,
                    'task_id' => $task->id

                ]);
            }
        }

        // $startTime = $request->start ? date('Y-m-d H:i:s', strtotime($request->start)) : null;
        // $endTime = $request->end ? date('Y-m-d H:i:s', strtotime($request->end)) : null;

        Duration::create([

            'start' => $request->start,
            'end' => $request->end,
            'task_id' => $task->id,
            'user_id' => auth()->id(),

        ]);

        $participantsEmail = getParticipantsEmail($request);

        //Rodrigo
        // dd($participantsEmail);

        $hasAnyParticipant = !empty($participantEmails);

        //Rodrigo
        // dd($hasAnyParticipant);

        if ($hasAnyParticipant) {

            //Rodrigo
            // dd($hasAnyParticipant);

            $creator = User::where('id', $task->created_by)->first();

            $creatorName = "$creator->name $creator->lastname";

            Mail::to($participantsEmail)->send(new TaskInvitationMail($task, $creatorName));
        }

        return redirect()->route('task.show', ['task' => $task->id])->with('success', 'Tarefa criada com sucesso!');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {

        $task = Task::find($id);

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

        //rodrigo
        // $startTime = Carbon::parse($duration->start_time)->format('H:i');

        $recurring = $task->reminder->recurring;

        //rodrigo
        // dd($recurring);

        $task['recurringMessage'] = getRecurringMessage($recurring);

        // dd($task->getAttributes());

        return $view === 'pending'
            ?  view('tasks/showPending', compact('task'))
            :  view('tasks/show', compact('task'));
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
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {

        $startTime = $request->start_time ? date('Y-m-d H:i:s', strtotime($request->start_time)) : null;
        $endTime = $request->end_time ? date('Y-m-d H:i:s', strtotime($request->end_time)) : null;

        $duration = Duration::create([

            'start_time' => $startTime,
            'end_time' => $endTime,
            'task_id' => $id,
            'user_id' => $request->user_id
        ]);

        $participant = Participant::where('user_id', $request->user_id)->where('task_id', $id)->first();
        $participant->status = 'accepted';

        $participant->save();

        return redirect('home');
    }

    public function acceptPendingTask(SetPendingTaskDuration $request, string $id)
    {
        //Rodrigo
        // dd($request->all());

        $currentUserID = auth()->id();

        $currentTask = Task::find($id);

        $currentTaskRecurring = $currentTask->reminder->recurring->getAttributes();

        // dd($currentTaskRecurring);

        $recurrencePatterns = getRecurrencePatterns($currentTaskRecurring);

        // dd($recurrencePatterns);

        $isSpecificDayPattern  = isset($recurrencePatterns['specific_date']);

        if ($isSpecificDayPattern) {

            $conflict = getConflictingTask($request->all(), 'specific_date',   $recurrencePatterns['specific_date'], $id);

            if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                return $conflict;
            }
        }

        foreach (array_keys($recurrencePatterns) as $pattern) {

            $conflict = getConflictingTask($request,  $pattern, $id);

            if ($conflict instanceof \Illuminate\Http\RedirectResponse) {
                return $conflict;
            }
        }

        //Rodrigo
        // dd($currentTaskRecurring);

        // $currentUserRecurrences = Recurring::where('available', 'true')
        //     ->whereHas('reminder', function ($reminderQuery) use ($currentUserID) {
        //         $reminderQuery->whereHas('task', function ($taskQuery) use ($currentUserID) {
        //             $taskQuery->where('created_by', $currentUserID)->orWhereHas('participants', function ($participantQuery) use ($currentUserID) {
        //                 $participantQuery->where('status', 'accepted')->where('user_id', $currentUserID);
        //             });
        //         });
        //     })->get();

        //Rodrigo
        // dd($currentUserRecurrences);

        // $currentTaskDuration = ["start" => $request->start, 'end' => $request->end]; // Aqui você precisa definir as durações da tarefa atual

        // foreach ($currentUserRecurrences as $userRecurrence) {
        //     // Verificar se há alguma duração que conflita dentro desta recorrência

        //     $conflictingDuration = $userRecurrence->reminder->task->durations()->where(function ($queryUserTasksDurationFirst) use ($currentTaskDuration) {

        //         $queryUserTasksDurationFirst->where('start', '>=', $currentTaskDuration['start'])
        //             ->where('start', '<', $currentTaskDuration['end'])
        //             ->orWhere(function ($queryUserTasksDurationSecond) use ($currentTaskDuration) {

        //                 $queryUserTasksDurationSecond->where('end', '>', $currentTaskDuration['start'])
        //                     ->where('end', '<=', $currentTaskDuration['end']);
        //             })
        //             ->orWhere(function ($queryUserTasksDurationThird) use ($currentTaskDuration) {
        //                 $queryUserTasksDurationThird->where('start', '<=', $currentTaskDuration['start'])
        //                     ->where('end', '>=', $currentTaskDuration['end']);
        //             });
        //     })->exists();

        //     if ($conflictingDuration) {
        //         return redirect()->back()->withErrors([
        //             'conflictingDuration' =>
        //             $userRecurrence->reminder->task->title,

        //         ])->withInput();
        //     }
        // }

        $startTime = $request->start ? date('H:i', strtotime($request->start)) : null;
        $endTime = $request->end ? date('H:i', strtotime($request->end)) : null;

        $myTasks = Task::whereHas('participants', function ($query) use ($currentUserID) {

            $query->where('user_id', $currentUserID)->where('status', 'accepted');
        })->orWhere('created_by', $currentUserID)->with(['durations' => function ($query) use ($currentUserID) {
            $query->where('user_id', $currentUserID);
        }])->get();

        $currentUserDurations = Duration::whereHas('task', function ($query) use ($currentUserID) {
            $query->where('user_id', $currentUserID);
        })->get();

        //Rodrigo
        // dd($currentUserDurations);

        // Verificar conflitos de horário

        $currentUserTaskDuration = [];

        // dd($myTasks);

        // foreach ($myTasks as $task) {

        //     dd($task->reminder->recurring);

        //     foreach ($task->durations as $duration) {

        //         $overlappingTimeCheck = ($startTime >= $duration->start && $startTime < $duration->end) ||
        //             ($endTime > $duration->start && $endTime <= $duration->end) ||
        //             ($startTime <= $duration->start && $endTime >= $duration->end);

        //         if ($duration->user_id === $currentUserID) {
        //             if ($overlappingTimeCheck) {
        //                 // Conflito encontrado
        //                 return redirect('home')->withErrors(['conflict' => 'Você já possui um compromisso nesse horário.']);
        //             }
        //         };
        //     }
        // }

        $duration = Duration::create([

            'start' => $startTime,
            'end' => $endTime,
            'task_id' => $id,
            'user_id' => $currentUserID
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
