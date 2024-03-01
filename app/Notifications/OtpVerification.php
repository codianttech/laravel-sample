<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

/**
 * OtpVerification
 */
class OtpVerification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $user;

    /**
     * Create a new notification instance.
     *
     * @param User $user [User model reference]
     *
     * @return void
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param mixed $notifiable 
     *
     * @return array
     */
    public function via($notifiable)
    {
        $channels = [];
        $sendVia = config('constants.send_otp_by');
        if (in_array($sendVia, ['sms', 'both'])) {
            $channels[] = 'sms';
        }
        if (in_array($sendVia, ['mail', 'both'])) {
            $channels[] = 'mail';
        }

        return $channels;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param mixed $notifiable 
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $fromEmail = config('mail.from.address');
        $fromName = getAppName();

        return (new MailMessage())
            ->from($fromEmail, $fromName)
            ->line('Use this code ' . $this->user->otp . ' to verify your account.')
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @param mixed $notifiable 
     *
     * @return array
     */
    public function toArray($notifiable)
    {
        return [

        ];
    }
}
