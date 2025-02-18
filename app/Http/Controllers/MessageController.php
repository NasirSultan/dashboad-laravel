<?php
namespace App\Http\Controllers;

use App\Models\ScheduledMessage;
use Carbon\Carbon;
use Illuminate\Http\Request;

class MessageController extends Controller
{
    public function scheduleMessage(Request $request)
    {
        // Validate the input
        $request->validate([
            'message' => 'required|string',
            'send_at' => 'required|date|after:now', // Make sure the send time is in the future
        ]);

        // Get the message and send_at from the request
        $message = $request->input('message');
        $sendAt = Carbon::parse($request->input('send_at'));

        // Create the scheduled message
        $scheduledMessage = ScheduledMessage::create([
            'message' => $message,
            'send_at' => $sendAt,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Your request was successful!',
            'documentation' => [
                'url' => 'https://laravel.com/docs',
                'description' => 'Laravel has wonderful documentation covering every aspect of the framework. Whether you are a newcomer or have prior experience with Laravel, we recommend reading our documentation from beginning to end.'
            ],
            'laracasts' => [
                'url' => 'https://laracasts.com',
                'description' => 'Laracasts offers thousands of video tutorials on Laravel, PHP, and JavaScript development. Check them out, see for yourself, and massively level up your development skills in the process.'
            ]
        ], 200);
        
    }

    public function getScheduledMessages(Request $request)
    {
        // Get the current time
        $currentTime = now();

        // Fetch messages that are scheduled to be sent in the past
        $messages = ScheduledMessage::where('send_at', '<=', $currentTime)
            ->get();

        return response()->json([
            'status' => 'success',
            'messages' => $messages,
        ]);
    }
}


