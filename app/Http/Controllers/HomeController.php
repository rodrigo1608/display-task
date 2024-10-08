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

        if ($request->has('filter')) {

            $selectedUserTasksBuilder = getFilteredTasks($request);

            $selectedUserTasks = sortByStart($selectedUserTasksBuilder);
        } else {
            $selectedUserTasks = getTasksByWeekday();
        }
        $labelOverview = "";


        // if(($selectedUserTasks != null && is_array($selectedUserTasks)){

        // }elseif($selectedUserTasks != null && $selectedUserTasks->isEmpty()){

        // }



        //     if ($selectedUserTasks != null && $selectedUserTasks->isEmpty()) {

        //         if ($request->has('filter')) {

        //             if ($request->input('filter') === 'participating') {

        //                 $labelOverview = "Você não está participando de nenhuma tarefa";
        //             } elseif ($request->input('filter') === 'created_by_me') {

        //                 $labelOverview = "Atualmente, você não tem nenhuma tarefa criada";
        //             } else {
        //                 $labelOverview = "Você ainda não concluiu nenhuma tarefa";
        //             }
        //         } else {
        //             $labelOverview = $isToday
        //                 ? "Nenhuma tarefa agendada para hoje, <span class='fs-3 poppins'> " . getFormatedDateBR($today) . "</span>  , " .  $weekdayInPortuguese

        //                 : "Nenhuma tarefa agendada para <span class='fs-3 poppins'> " . getFormatedDateBR($selectedDate) . "</span>, " .  $weekdayInPortuguese;
        //         }
        //     } elseif ($selectedUserTasks != null) {

        //         if ($request->has('select_filter')) {

        //             if ($request->input('select_filter') === 'participating') {

        //                 $labelOverview = "Tarefas nas quais você está participando:";
        //             } elseif ($request->input('select_filter') === 'created_by_me') {

        //                 $labelOverview = "Tarefas criadas por você:";
        //             } else {

        //                 $labelOverview = "Tarefas concluídas:";
        //             }
        //         } else {

        //             $labelOverview = "Agenda de <span class='fs-2 poppins'>" . getFormatedDateBR($selectedDate) . "</span>,  $weekdayInPortuguese.";
        //         }
        //     }
        //     if ($selectedUserTasks != null) {

        //         foreach ($selectedUserTasks as $task) {

        //             $taskID = $task->id;

        //             $creatorOrParticipant  = $task->created_by == $currentUserID ? 'creator' : 'participant';

        //             $notificationTime = null;

        //             $notificationTime = $task->reminder->notificationTimes()->where('user_id', $currentUserID)->first()?->getAttributes();


        //             if ($task->participants->isEmpty()) {

        //                 $task->emailsParticipants = "Nenhum participante";
        //             } else {

        //                 $task->emailsParticipants = $task->participants->pluck('email')->implode(', ');
        //             }

        //             $duration = $task->durations()->where('user_id', $currentUserID)->where('task_id', $task->id)->first();

        //             if ($duration) {
        //                 $task->start = substr($duration->start, 0, 5);
        //                 $task->end =  substr($duration->end, 0, 5);
        //                 $task->status = $duration->status;
        //             }

        //             $task->recurringMessage = getRecurringMessage($task->reminder->recurring);

        //             $start = isset($task->start) ? getCarbonTime($task->start) : null;
        //             $end = isset($task->end) ? getCarbonTime($task->end) : null;

        //             $recurring = $task->reminder->recurring;
        //         }
        //     }

        return view('home', compact('isThereAnyReminder', 'selectedUserTasks', 'orderedReminders', 'labelOverview'));
    }
}
