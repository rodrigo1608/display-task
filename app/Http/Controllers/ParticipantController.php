<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;
use App\Models\User;

class ParticipantController extends Controller
{
    public function add(Request $request, $taskID)
    {

        // dd($request->all());

        foreach ($request->all() as $attribute => $value) {

            if (str_starts_with($attribute, 'participant')) {

                Participant::create([

                    'user_id' => User::where('email', $value)->first()->id,
                    'task_id' => $taskID,

                ]);
            }
        }

        // Participant::create([

        //     'user_id' => User::where('email', $value)->first()->id,
        //     'task_id' => $task->id;

        // ]);

        function get_participant_message($count)
        {
            $participantWord = $count > 1 ? 'participantes' : 'participante';
            return "O(s) $participantWord foram adicionados com sucesso.";
        }

        return redirect()->route('task.show', ['task' => $taskID])->with('success', "Participante(s) adicionado(s)");
    }

    public function destroy(Request $request)
    {

        $participant = Participant::where('task_id', $request->task_id)
            ->where('user_id', $request->user_id)
            ->first();

        $participant->delete();

        $creator = $participant->task->creator->name . " " . $participant->task->creator->lastname;
        $taskTitle = $participant->task->title;

        return redirect()->route('display.displayDay')->with('success', "Você recusou o convite de  $creator  para participar da tarerfa $taskTitle.");
    }
}
