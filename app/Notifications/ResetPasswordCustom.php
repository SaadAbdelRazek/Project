<?php

namespace App\Notifications;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordCustom extends Notification
{
    public $url;

    public function __construct($url)
    {
        $this->url = $url;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Reset Your Password')
            ->line('We received a request to reset your password.')
            ->action('Reset Password', $this->url)
            ->line('If you did not request a password reset, no further action is required.');
    }
}
