<?php

// app/Jobs/SendScheduledMessage.php

namespace App\Jobs;

use App\Models\ScheduledMessage;
use App\Models\User; // Assuming you have a User model
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendScheduledMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $scheduledMessage;

    public function __construct(ScheduledMessage $scheduledMessage)
    {
        $this->scheduledMessage = $scheduledMessage;
    }

    public function handle()
    {
        $users = User::all(); // Get all users
        foreach ($users as $user) {
            // Logic to send the message to the user
            // For example, sending an email
            \Mail::to($user->email)->send(new \App\Mail\ScheduledMessageMail($this->scheduledMessage));
        }

        // Mark the message as sent
        $this->scheduledMessage->update(['sent' => true]);
    }
}

