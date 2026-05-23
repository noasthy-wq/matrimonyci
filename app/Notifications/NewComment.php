<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewComment extends Notification implements ShouldQueue
{
    use Queueable;

    public $comment;
    public $author;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($comment, $author)
    {
        $this->comment = $comment;
        $this->author = $author;
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
            ->subject('Nouveau commentaire sur votre profil')
            ->line($this->author . ' a laissé un commentaire sur votre profil:')
            ->line('"' . substr($this->comment, 0, 100) . '..."')
            ->action('Voir le commentaire', url('/'));
    }
}
