<?php

namespace App\Notifications\Students;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class StopRegistrationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
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


    public function toFirebase(User $notifiable)
    {
        return (new FirebaseMessage)
            ->withTitle('Subscription Expired.')
            ->withBody('Your Subscription Expired Please Visit The Company To Renew It')
            ->withPriority('high')->asNotification($notifiable->fcm_token);
    }
}
