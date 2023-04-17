<?php

namespace App\Notifications\Students;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class PositionTimeNotification extends Notification
{
    use Queueable;


    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['firebase'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toFirebase(object $notifiable)
    {
        return (new FirebaseMessage)
            ->withTitle('Get Ready!')
            ->withBody('Your Bus Will Be At the Position In 5 Minute')
            ->withPriority('high')->asNotification($notifiable->fcm_token);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
