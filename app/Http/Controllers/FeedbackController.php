<?php

namespace App\Http\Controllers;

use App\Models\Attachment;
use App\Models\Feedback;
use Illuminate\Http\Request;

class FeedbackController extends Controller
{
    public function create()
    {
        return view('feedbacks/create');
    }

    public function store(Request $request)
    {

        $feedback =  Feedback::create([

            'feedback' => $request->feedback,

            'user_id' => auth()->id(),

            'task_id' => $request->task_id

        ]);

        $attachments = $request->task_attachments;

        if (isset($attachments)) {

            foreach ($attachments as $attachment) {

                $attachmentURN = $attachment->store('task-attachments');

                Attachment::create([

                    'path' =>  $attachmentURN,

                    'feedback_id' => $feedback->id,

                ]);
            }
        }

        $task = $request->task_id;

        return redirect()->route('task.show', compact('task'))->with('success', 'Tarefa criada com sucesso!');
    }
}
