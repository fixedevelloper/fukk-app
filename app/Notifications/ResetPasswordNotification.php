<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends Notification
{
    public $token;
    public $email;

    public function __construct($token, $email)
    {
        $this->token = $token;
        $this->email = $email;
    }

    public function via($notifiable)
    {
        return ['mail'];
    }

    public function toMail($notifiable)
    {
        $url = config('app.frontend_url') . "/auth/reset-password?token={$this->token}&email=" . urlencode($this->email);

        logger($url);
        return (new MailMessage)
            ->subject('Réinitialisation de votre mot de passe')
            ->view('emails.reset-password', [
                'url' => $url,
                'user' => $notifiable,
            ]);
    }
}
