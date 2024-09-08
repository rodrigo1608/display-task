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

                $pendingTasks = Task::whereHas('participants', function ($query) use ($userID) {
                    $query->where('concluded', 'false')
                        ->where('user_id', $userID)
                        ->where('status', 'pending');
                })->get();
            } else {

                $userID = null;
                $pendingTasks = collect();
            }

            $view->with('user', $user);

            $view->with('pendingTasks',   $pendingTasks);
        });
    }
}
