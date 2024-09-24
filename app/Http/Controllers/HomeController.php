<?php

namespace App\Http\Controllers;


use App\Models\Task;

use Illuminate\Http\Request;

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

    public function index(Request $request)
    {
        // //-----------------------------------------------------------------------------------------------Teste

        //------------------------------------------------------------------------------------------termina o teste


        // if (!empty($request->all())) {
        //     dd($request->all());
        // }

        $now = getCarbonNow()->format('H:i');

        $today = getToday();

        $selectedDate = $request->input('specific_date') ?? $today;

        $isToday = checkIsToday($selectedDate);

        $weekdayOfSelectDate = getDayOfWeek(getCarbonDate($selectedDate));

        $weekdayInPortuguese = getDayOfWeek(getCarbonDate($selectedDate), 'pt-br');

        $currentUserID = auth()->id();

        $daysOfWeek = getDaysOfWeek();

        $userRemindersByWeekDay = getRemindersByWeekday($daysOfWeek);

        $isThereAnyReminder = !empty($userRemindersByWeekDay);

        $orderedReminders = getWeekDaysStartingFromToday($userRemindersByWeekDay);

        $selectedUserTask  = null;


        if ($request->has('select_filter')) {

            $selectedUserTasks = getFilteredBySelectTasks($request);
        } else {
            $selectedUserTasksBuilder = getSelectedUserTasksBuilder($selectedDate);

            $selectedUserTasks = sortByStart($selectedUserTasksBuilder);
        }

        $labelOverview = "";

        if ($selectedUserTasks != null && $selectedUserTasks->isEmpty()) {

            if ($request->has('select_filter')) {

                if ($request->input('select_filter') === 'participating') {

                    $labelOverview = "Você não está participando de nenhuma tarefa";
                } elseif ($request->input('select_filter') === 'created_by_me') {

                    $labelOverview = "Atualmente, você não tem nenhuma tarefa criada";
                } else {
                    $labelOverview = "Você ainda não concluiu nenhuma tarefa";
                }
            } else {
                $labelOverview = $isToday ? "Nenhuma tarefa agendada para hoje, " . getFormatedDateBR($today) : " Nenhuma tarefa agendada para  " . getFormatedDateBR($selectedDate) . ",  $weekdayInPortuguese.";
            }
        } elseif ($selectedUserTasks != null) {

            if ($request->has('select_filter')) {

                if ($request->input('select_filter') === 'participating') {

                    $labelOverview = "Tarefas nas quais você está participando:";
                } elseif ($request->input('select_filter') === 'created_by_me') {

                    $labelOverview = "Tarefas criadas por você:";
                } else {
                    $labelOverview = "Tarefas concluídas:";
                }
            } else {
                $labelOverview = $isToday ? "Agenda de hoje, " . getFormatedDateBR($today) : "Agenda de " . getFormatedDateBR($selectedDate) . ",  $weekdayInPortuguese.";
            }
        }
        if ($selectedUserTasks != null) {

            foreach ($selectedUserTasks as $task) {

                $taskID = $task->id;

                $creatorOrParticipant  = $task->created_by == $currentUserID ? 'creator' : 'participant';

                $notificationTime = null;

                $notificationTime = $task->reminder->notificationTimes()->where('user_id', $currentUserID)->first()?->getAttributes();

                // if ($creatorOrParticipant == 'creator') {

                //     $notificationTime = NotificationTime::whereHas('reminder', function ($query) use ($currentUserID, $taskID) {

                //         $query->whereHas('task', function ($query) use ($currentUserID, $taskID) {

                //             $query->where('created_by', $currentUserID)->where('id', $taskID);
                //         });
                //     })->first()->getAttributes();
                // } else {
                //     $notificationTime = NotificationTime::where('user_id', $currentUserID)->whereHas('reminder', function ($query) use ($taskID) {

                //         $query->whereHas('task', function ($query) use ($taskID) {

                //             $query->where('task_id', $taskID);
                //         });
                //     })->first()->getAttributes();
                // }

                if ($task->participants->isEmpty()) {

                    $task->emailsParticipants = "Nenhum participante";
                } else {

                    $task->emailsParticipants = $task->participants->pluck('email')->implode(', ');
                }

                $duration = $task->durations()->where('user_id', $currentUserID)->where('task_id', $task->id)->first();

                $task->start = substr($duration->start, 0, 5);
                $task->end =  substr($duration->end, 0, 5);

                $task->recurringMessage = getRecurringMessage($task->reminder->recurring);

                $start = isset($task->start) ? getCarbonTime($task->start) : null;
                $end = isset($task->end) ? getCarbonTime($task->end) : null;

                $recurring = $task->reminder->recurring;

                $task->status = $duration->status;
            }
        }


        return view('home', compact('isThereAnyReminder', 'selectedUserTasks', 'orderedReminders', 'labelOverview'));
    }
}
