<?php

// app/Jobs/SendScheduledMessage.php

namespace App\Jobs;

use App\Models\ScheduledMessage;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

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
        // Here you can add code to send the message (e.g., via email, SMS, etc.)
        Log::info('Sending message: ' . $this->scheduledMessage->message);
    }
}
