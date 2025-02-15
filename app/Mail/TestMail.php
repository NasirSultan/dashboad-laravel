<?php
namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TestMail extends Mailable
{
    use Queueable, SerializesModels;

    public $subjectLine;
    public $messageBody;

    public function __construct($subjectLine, $messageBody)
    {
        $this->subjectLine = $subjectLine;
        $this->messageBody = $messageBody;
    }

    public function build()
    {
        return $this->subject($this->subjectLine)
                    ->text(function ($message) {
                        $message->setBody($this->messageBody, 'text/plain');
                    });
    }
}
