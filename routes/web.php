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


// Route::get('/', function () {
//     return view('teste');
// });

Route::get('/', function () {
    return redirect()->route('display.day');
});

Auth::routes(['verify' => true]);

Route::get('home', [HomeController::class, 'index'])->name('home');

Route::resource('reminder', ReminderController::class);

Route::resource('task', TaskController::class);
Route::put('{task}/accept-pending-task', [TaskController::class, 'acceptPendingTask'])->name('acceptPendingTask');
Route::post('{id}/mark-as-concluded', [TaskController::class, 'markAsConcluded'])->name('markAsConcluded');

Route::prefix('user')
    ->name('user.')
    ->controller(UserController::class)
    ->group(function () {
        Route::get('{id}', 'edit')->name('edit');
        Route::put('{id}', 'update')->name('update');
    });

Route::prefix('participant')
    ->name('participant.')
    ->controller(ParticipantController::class)
    ->group(function () {
        Route::post('{taskID}', 'add')->name('add');
        Route::delete('/',  'destroy')->name('destroy');
    });

Route::post('feedback', [FeedbackController::class, 'store'])->name('feedback.store');

Route::get('search_tasks', [FilterController::class, 'searchByTitle'])->name('search_tasks.searchByTitle');

Route::prefix('display')
    ->name('display.')
    ->controller(DisplayController::class)
    ->group(function () {

        Route::get('day', 'displayDay')->name('day');
        Route::get('week', 'displayWeek')->name('week');
        Route::get('month', 'displayMonth')->name('month');
        Route::get('panel', 'displayPanel')->name('panel');
    });
