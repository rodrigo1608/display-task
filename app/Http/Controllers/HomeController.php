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

        $daysOfWeek = getDaysOfWeek();

        $dayOfWeek = getDayOfWeek(getCarbonNow());

        $nextTasks = [];

        forEach( $daysOfWeek as $weekDayInEnglish => $weekdayInPortuguese){

            $taskBuilder =  Task::with([

                'participants',
                'reminder',
                'reminder.recurring',
                'durations'

            ])
            ->where('concluded','false')
            ->where(function($query){

                $query
                ->where('created_by',auth()->id())
                ->orWhereHas('participants',function($query){

                    $query
                    ->where('user_id',auth()->id())
                    ->where('status','accepted');

                });

            })
            ->whereHas('reminder',function($query) use ($weekDayInEnglish){
                $query->whereHas('recurring',function( $query) use ($weekDayInEnglish){

                    $query
                    ->where($weekDayInEnglish ,'true')
                    ->orWhere('specific_date_weekday',$weekDayInEnglish);

                });

            })
            ->where(function($query){

                $query->whereHas('durations',function($query){

                    $query->where('status','<>','finished');

                })
            ->whereHas('reminder',function($query){

                    $query->whereHas('recurring',function($query){

                        $query->whereNotNull('specific_date');
                    });

                });
            });

        $nextTasks[$weekdayInPortuguese] = sortByStart($taskBuilder);
    }

        // dd($nextTasks);

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

        $orderedReminders = sortStartingFromToday($userRemindersByWeekDay, 'pt-br');

        $selectedUserTask  = null;

        if ($request->has('filter')) {

            $selectedUserTasksBuilder = getFilteredTasks($request);

            $selectedUserTasks = sortByStart($selectedUserTasksBuilder);

            if ($selectedUserTasks->isEmpty()) {

                if ($request->input('filter') === 'participating') {

                    $labelOverview = "Você não está participando de nenhuma tarefa";
                } elseif ($request->input('filter') === 'created') {

                    $labelOverview = "Atualmente, você não tem nenhuma tarefa criada";
                } else {
                    $labelOverview = "Você ainda não concluiu nenhuma tarefa";
                }
            } else {

                if ($request->input('filter') === 'participating') {

                    $labelOverview = "Tarefas nas quais você está participando:";
                } elseif ($request->input('filter') === 'created') {

                    $labelOverview = "Tarefas criadas por você:";
                } else {

                    $labelOverview = "Tarefas concluídas:";
                }
                foreach ($selectedUserTasks as $task) {

                    $taskID = $task->id;

                    $creatorOrParticipant  = $task->created_by == $currentUserID ? 'creator' : 'participant';

                    $notificationTime = null;

                    $notificationTime = $task->reminder->notificationTimes()->where('user_id', $currentUserID)->first()?->getAttributes();

                    if ($task->participants->isEmpty()) {

                        $task->emailsParticipants = "Nenhum participante";
                    } else {

                        $task->emailsParticipants = $task->participants->pluck('email')->implode(', ');
                    }

                    $duration = $task->durations()->where('user_id', $currentUserID)->where('task_id', $task->id)->first();

                    if ($duration) {

                        $task->start = substr($duration->start, 0, 5);
                        $task->end =  substr($duration->end, 0, 5);
                        $task->status = $duration->status;
                    }

                    $task->recurringMessage = getRecurringMessage($task->reminder->recurring);

                    $start = isset($task->start) ? getCarbonTime($task->start) : null;
                    $end = isset($task->end) ? getCarbonTime($task->end) : null;

                    $recurring = $task->reminder->recurring;
                }
            }

        } else {

            $userTasks = getTasksByWeekday();

            $filteredUserTasks = array_filter($userTasks, function ($day) {
                return !$day->isEmpty();
            });

            $selectedUserTasks = sortStartingFromToday($filteredUserTasks, 'pt-br');

            $labelOverview = empty($filteredUserTasks)
                ? "Nenhuma tarefa agendada" : "";
        }

        return view('home', compact('isThereAnyReminder', 'selectedUserTasks', 'orderedReminders', 'labelOverview'));
    }
}
