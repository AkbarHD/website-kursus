<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
// use Illuminate\Notifications\Notification;
use illuminate\Auth\Notifications\VerifyEmail as VerifyEmailNotification; // ganti

class VerifyEmail extends VerifyEmailNotification // ganti
{
    use Queueable;


    public function __construct()
    {
        //
    }


    public function via($notifiable)
    {
        return ['mail'];
    }


    public function toMail($notifiable): MailMessage
    {
        $verificationUrl = $this->verificationUrl($notifiable);
        return (new MailMessage)
            ->subject('Verify Email Address Boyy')
            ->line('Verifikasi Email Anda yaaaa.')
            ->action('Notification Action', $verificationUrl)
            ->line('Thank you for using our application!');
    }

    protected function verificationUrl($notifiable)
    {
        return route('verification.verify', [
            'id' => $notifiable->getKey(),
            'hash' => sha1($notifiable->getEmailForVerification()),
        ]);
    }


    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         //
    //     ];
    // }
}
