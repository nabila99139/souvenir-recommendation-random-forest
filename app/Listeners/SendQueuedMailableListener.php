<?php

namespace App\Listeners;

use Illuminate\Support\Facades\Log;
use Illuminate\Mail\Events\MessageSending;
use Illuminate\Mail\Events\MessageSent;
use Illuminate\Contracts\Queue\Job;

class SendQueuedMailableListener
{
    /**
     * Handle message sending event.
     */
    public function handleSending(MessageSending $event)
    {
        $message = $event->message;
        $to = $message->getTo();
        $subject = $message->getSubject();

        Log::info('Mail job processing started', [
            'to' => is_array($to) ? array_keys($to)[0] : $to,
            'subject' => $subject,
            'timestamp' => now()->toDateTimeString()
        ]);
    }

    /**
     * Handle message sent event.
     */
    public function handleSent(MessageSent $event)
    {
        $message = $event->message;
        $to = $message->getTo();
        $subject = $message->getSubject();

        Log::info('Mail job completed successfully', [
            'to' => is_array($to) ? array_keys($to)[0] : $to,
            'subject' => $subject,
            'timestamp' => now()->toDateTimeString()
        ]);
    }
}
