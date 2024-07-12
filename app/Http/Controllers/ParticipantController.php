<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\Task;

class ParticipantController extends Controller
{

    public function destroy(Request $request)
    {

        $participant = Participant::where('task_id', $request->task_id)
            ->where('user_id', $request->user_id)
            ->first();

        $participant->delete();

        $creator = $participant->task->creator->name . " " . $participant->task->creator->lastname;
        $taskTitle = $participant->task->title;

        return redirect()->route('home')->with('success', "Você recusou o convite de  $creator  para participar da tarerfa $taskTitle.");
    }
}
