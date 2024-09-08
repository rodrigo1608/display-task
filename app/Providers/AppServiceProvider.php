<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Task;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer('layouts/app', function ($view) {

            $user = Auth::user();

            if ($user) {

                $userID = $user->id;

                $pendingTasks = Task::with('durations')->whereHas('participants', function ($query) use ($userID) {
                    $query->where('concluded', 'false')
                        ->where('user_id', $userID)
                        ->where('status', 'pending');
                })->get();

                $now = getCarbonNow();

                foreach ($pendingTasks as $task) {

                    $duration = $task->durations()
                        ->where('task_id', $task->id)
                        ->where('user_id', $task->created_by)
                        ->first();

                    $start =  getCarbonTime($duration->start);

                    if (filled($task->specific_date)) {

                        switch ($task->duration->status) {
                            case 'starting':
                                if ($now->diffInMinutes($start) < 30) {
                                    $task->specificDateNotificationAlert = 'A tarefa irá começar em breve';
                                }
                                break;
                            case 'in_progress':
                                $task->specificDateNotificationAlert = 'A tarefa já está em andamento';
                                break;
                            case 'finished':
                                $task->specificDateNotificationAlert = 'A tarefa está expirada';
                                break;
                        }
                    } else {

                        switch ($task->duration->status) {

                            case 'starting':
                                if ($now->diffInMinutes($start) < 30) {
                                    $task->notificationAlert = 'A tarefa irá começar em breve, caso o alerta supere 30 min, você só será alertado na proxima recorrência da tarefa';
                                }
                                break;

                            case 'in_progress':
                                $task->specificDateNotificationAlert = 'A tarefa já está em andamento, você só será alertado na proxima recorrência da tarefa';
                                break;
                            case 'finished':
                                $task->specificDateNotificationAlert = 'A tarefa está expirada,  você só será alertado na proxima recorrência da tarefa';
                                break;
                        }
                    }
                }
            } else {

                $userID = null;
                $pendingTasks = collect();
            }

            $view->with('user', $user);

            $view->with('pendingTasks',   $pendingTasks);
        });
    }
}
