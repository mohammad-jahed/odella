<?php

namespace App\Notifications\Students;

use App\Enums\NotificationType;
use App\Models\Notification as Notifications;
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
        /**
         * @var Notifications $notification ;
         */
        $notification = Notifications::query()->create([
            'user_id' => $notifiable->id,
            'title' => 'Get Ready!',
            'type' => NotificationType::PositionTime,
            'body' => 'Your Bus Will Be At the Position In 5 Minute',
        ]);

        return (new FirebaseMessage)
            ->withTitle($notification->title)
            ->withBody($notification->body)
            ->withAdditionalData($notification->type)
            ->withPriority('normal')->asNotification($notifiable->fcm_token);
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
