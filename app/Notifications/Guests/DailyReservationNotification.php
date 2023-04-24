<?php

namespace App\Notifications\Guests;

use App\Models\DailyReservation;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class DailyReservationNotification extends Notification
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    private bool $status;

    public function __construct($status)
    {
        //
        $this->status = $status;
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
    public function toFirebase(DailyReservation $notifiable)
    {
        if ($this->status) {
            return (new FirebaseMessage)
                ->withTitle('Daily Reservation.')
                ->withBody('Your reservation has been confirmed')
                ->withPriority('high')->asNotification($notifiable->fcm_token);
        } else {
            return (new FirebaseMessage)
                ->withTitle('Daily Reservation.')
                ->withBody("Sorry, There are no enough seats")
                ->withPriority('high')->asNotification($notifiable->fcm_token);
        }

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
