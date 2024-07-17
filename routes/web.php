<?php

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('home');
});

Auth::routes(['verify' => true]);

Route::get('/home/{selectedDate?}', [HomeController::class, 'index'])->name('home');

Route::resource('reminder', ReminderController::class);

Route::resource('task', TaskController::class);
Route::put('task/{task}/accept-pending-task', [TaskController::class, 'acceptPendingTask'])->name('task.acceptPendingTask');

Route::get('user/{id}', [UserController::class, 'edit'])->name('user.edit');
Route::put('user/{id}', [UserController::class, 'update'])->name('user.update');

Route::delete('participant', [ParticipantController::class, 'destroy'])->name('participant.destroy');

Route::post('feedback', [FeedbackController::class, 'store'])->name('feedback.store');

Route::get('filteredTasks', [FilterController::class, 'filterByTitle'])->name('filteredTasks.filterByTitle');
