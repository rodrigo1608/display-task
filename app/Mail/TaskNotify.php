<?php

namespace App\Mail;

use App\Models\Task;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class TaskNotify extends Mailable
{
    use Queueable, SerializesModels;

    public $taskData;
    public $url;

    /**
     * Create a new message instance.
     */
    public function __construct($taskData)
    {
        $this->taskData = $taskData;
        $this->url = route('task.show', ['task' => $taskData['id']]);
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            // subject: $this->taskData['message'],
            subject: "Notificação da tarefa: ". $this->taskData['title'],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'mail.task-notify',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
