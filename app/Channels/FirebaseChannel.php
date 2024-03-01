<?php

namespace App\Channels;

use Illuminate\Notifications\Notification;

class FirebaseChannel
{
    /**
     * Send the given notification.
     */
    public function send($notifiable, Notification $notification): void
    {
        $message = $notification->toFirebase($notifiable);
    }
}
