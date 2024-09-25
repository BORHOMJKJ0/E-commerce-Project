<?php

namespace App\Notifications;

use Ichtrojan\Otp\Otp;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ResetPasswordVerificationNotifiaction extends Notification
{
    use Queueable;

    public $message;

    public $fromEmail;

    public $otp;

    public $mailer;

    public $subject;

    public function __construct()
    {
        $this->message = 'Use the below code for resetting your password';
        $this->subject = 'Password Resetting';
        $this->fromEmail = env('MAIL_FROM_ADDRESS');
        $this->mailer = env('MAIL_MAILER');
        $this->otp = new Otp;
    }

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $otp = $this->otp->generate($notifiable->email, 'numeric', 6, 60);

        return (new MailMessage)
            ->mailer($this->mailer)
            ->subject($this->subject)
            ->greeting('Hello '.$notifiable->name)
            ->line($this->message)
            ->line('code : '.$otp->token);

    }

    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
