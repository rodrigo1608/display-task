<?php

use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\FilterController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\DisplayController;
use App\Http\Controllers\ReminderController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\UserController;

use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return redirect()->route('display.displayDay');
});

Auth::routes(['verify' => true]);

Route::get('home', [HomeController::class, 'index'])->name('home');

Route::resource('reminder', ReminderController::class);

Route::resource('task', TaskController::class);
Route::put('task/{task}/accept-pending-task', [TaskController::class, 'acceptPendingTask'])->name('task.acceptPendingTask');
Route::post('tasks/{id}/markAsConcluded', [TaskController::class, 'markAsConcluded'])->name('tasks.markAsConcluded');

Route::get('user/{id}', [UserController::class, 'edit'])->name('user.edit');
Route::put('user/{id}', [UserController::class, 'update'])->name('user.update');

Route::post('participant/{taskID}', [ParticipantController::class, 'add'])->name('participant.add');
Route::delete('participant', [ParticipantController::class, 'destroy'])->name('participant.destroy');

Route::post('feedback', [FeedbackController::class, 'store'])->name('feedback.store');

Route::get('search_tasks', [FilterController::class, 'searchByTitle'])->name('search_tasks.searchByTitle');

Route::get('display/day', [DisplayController::class, 'displayDay'])->name('display.displayDay');
Route::get('display/week', [DisplayController::class, 'displayWeek'])->name('display.displayWeek');
