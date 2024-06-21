<?php

namespace App\Http\Controllers;

use App\Mail\TaskInvitationMail;
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

        $task = Task::create([

            'title' => $request->title,

            'local' => $request->local ?? null,

            'created_by' => auth()->id(),
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

            'sunday' => $request->sunday ?? 'false',

            'monday' => $request->monday ?? 'false',

            'tuesday' => $request->tuesday ?? 'false',

            'wednesday' => $request->wednesday ??  'false',

            'thursday' => $request->thursday ?? 'false',

            'friday' => $request->friday ?? 'false',

            'saturday' => $request->saturday ?? 'false',

            'reminder_id' => $reminder->id,

        ];

        Recurring::create($recurringData);

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

        $participantEmails = $task->participants()->pluck('users.email');

        //Rodrigo
        // dd($participantEmails);

        $hasAnyParticipant = !empty($participantEmails);

        //Rodrigo
        // dd($hasAnyParticipant);

        if ($hasAnyParticipant) {

            //Rodrigo
            // dd($hasAnyParticipant);


            $creator = User::where('id', $task->created_by)->first();

            $creatorName = "$creator->name $creator->lastname";

            Mail::to($participantEmails)->send(new TaskInvitationMail($task, $creatorName));
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

        $createdBy->telephone = '(' . substr($createdBy->telephone, 0, 2) . ') ' . substr($createdBy->telephone, 2, 1) . ' ' . substr($createdBy->telephone, 3);

        $description = $task->feedbacks->first()->feedback;

        $attachments = $task->feedbacks->first()->attachments->all();

        $duration = $task->durations->first();

        $startTime = $duration->start ? date('H:i', strtotime($duration->start)) : null;

        $endTime = $duration->end ? date('H:i', strtotime($duration->end)) : null;

        //rodrigo
        // $startTime = Carbon::parse($duration->start_time)->format('H:i');

        $recurring = $task->reminder->recurring;

        $recurringMessage = '';

        if (is_null($recurring->specific_date)) {

            $daysOfWeek = [

                'sunday' => 'domingo',

                'monday' => 'segunda',

                'tuesday' => 'terça',

                'wednesday' => 'quarta',

                'thursday' => 'quinta',

                'friday' => 'sexta',

                'saturday' => 'sábado',

            ];

            $repeatingDays = [];

            foreach ($daysOfWeek as $key => $day) {

                if ($recurring->$key === 'true') {

                    $repeatingDays[] = $day;
                }
            }

            $numberOfDays = count($repeatingDays);

            if ($numberOfDays == 7) {

                $recurringMessage = 'Irá se repetir todos os dias.';
            } else {

                $lastDay = array_pop($repeatingDays);

                $recurringMessage = 'Irá se repetir a cada ' . implode(', ', $repeatingDays);

                if ($numberOfDays > 1) {

                    $recurringMessage .= ' e ' . $lastDay . '.';
                } else {

                    $recurringMessage .= '.';
                }
            }
        } else {
            $formatedDate = '<strong>' . Carbon::parse($recurring->specific_date)->format('d/m/Y') . '</strong>';
            $recurringMessage = "Ocorrerá exclusivamente no dia: $formatedDate ";
        }
        return $view === 'pending'
            ?  view('tasks/showPending', compact('attachments', 'createdBy', 'description', 'endTime', 'recurringMessage', 'startTime', 'task'))
            :  view('tasks/show', compact('attachments', 'createdBy', 'description', 'endTime', 'recurringMessage', 'startTime', 'task'));
    }

    public function showPending(string $id)
    {
        $task = Task::find($id);

        return view('tasks/showPending', compact('task'));
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

        $currentTaskRecurring = $currentTask->reminder->recurring;

        //Rodrigo
        // dd($currentTaskRecurring);

        $currentUserRecurrences = Recurring::where('available', 'true')
            ->whereHas('reminder', function ($reminderQuery) use ($currentUserID) {
                $reminderQuery->whereHas('task', function ($taskQuery) use ($currentUserID) {
                    $taskQuery->where('created_by', $currentUserID)->orWhereHas('participants', function ($participantQuery) use ($currentUserID) {
                        $participantQuery->where('status', 'accepted')->where('user_id', $currentUserID);
                    });
                });
            })
            ->get();

        //Rodrigo
        // dd($currentUserRecurrences);

        $currentTaskDuration = ["start" => $request->start, 'end' => $request->end]; // Aqui você precisa definir as durações da tarefa atual

        foreach ($currentUserRecurrences as $userRecurrence) {
            // Verificar se há alguma duração que conflita dentro desta recorrência

            $conflictingDuration = $userRecurrence->reminder->task->durations()->where(function ($queryUserTasksDurationFirst) use ($currentTaskDuration) {

                $queryUserTasksDurationFirst->where('start', '>=', $currentTaskDuration['start'])
                    ->where('start', '<', $currentTaskDuration['end'])
                    ->orWhere(function ($queryUserTasksDurationSecond) use ($currentTaskDuration) {

                        $queryUserTasksDurationSecond->where('end', '>', $currentTaskDuration['start'])
                            ->where('end', '<=', $currentTaskDuration['end']);
                    })
                    ->orWhere(function ($queryUserTasksDurationThird) use ($currentTaskDuration) {
                        $queryUserTasksDurationThird->where('start', '<=', $currentTaskDuration['start'])
                            ->where('end', '>=', $currentTaskDuration['end']);
                    });
            })->exists();

            if ($conflictingDuration) {
                // Retorne um erro de validação ou faça algo para lidar com a sobreposição
                // Por exemplo:
                throw new \Exception('As durações propostas se sobrepõem com uma tarefa existente.');
            }
        }

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
