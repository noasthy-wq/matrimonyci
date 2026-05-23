<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AccountBanned extends Notification implements ShouldQueue
{
    use Queueable;

    public $reason;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($reason = null)
    {
        $this->reason = $reason;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Votre compte a été suspendu')
            ->line('Votre compte MatrimonyCI a été suspendu pour violation de nos conditions d\'utilisation.')
            ->when($this->reason, function ($mail) {
                return $mail->line('Raison: ' . $this->reason);
            })
            ->line('Si vous pensez que c\'est une erreur, veuillez nous contacter.');
    }
}
