<?php

namespace App\Notifications\Supervisor;

use App\Models\User;
use App\Models\Notification as Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class SupervisorDailyReservationNotification extends Notification
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

    /**
     * Get the mail representation of the notification.
     */
    public function toFirebase(User $notifiable)
    {
        /**
         * @var Notifications $notification ;
         */
        $notification = Notifications::query()->create([
            'user_id' => $notifiable->id,
            'title' => 'New Daily Reservation!',
            'body' => 'You Have New Pending Daily Reservation',
        ]);

        return (new FirebaseMessage)
            ->withTitle($notification->title)
            ->withBody($notification->body)
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
