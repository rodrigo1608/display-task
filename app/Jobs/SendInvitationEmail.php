<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Mail\TaskInvitationMail;

use Mail;

class SendInvitationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */

    protected $participantsEmails;
    protected $task;
    protected $creatorName;

    public function __construct($participantsEmails, $task, $creatorName)
    {
        $this->participantsEmails = $participantsEmails;
        $this->task = $task;
        $this->creatorName = $creatorName;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Mail::to($this->participantsEmails)->send(new TaskInvitationMail($this->task, $this->creatorName));
    }
}
