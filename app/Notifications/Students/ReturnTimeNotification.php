<?php

namespace App\Notifications\Students;

use App\Enums\NotificationType;
use App\Models\Notification as Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class ReturnTimeNotification extends Notification
{
    use Queueable;

    private string $remainTime;

    /**
     * Create a new notification instance.
     */
    public function __construct($remainTime)
    {
        $this->remainTime = $remainTime;
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
    public function toMail(object $notifiable)
    {
        /**
         * @var Notifications $notification ;
         */
        $notification = Notifications::query()->create([
            'user_id' => $notifiable->id,
            'title' => 'Get Ready!',
            'type' => NotificationType::ReturnTime,
            'body' => 'Your Bus Will Leave in ' . $this->remainTime . 'Minute',
        ]);

        return (new FirebaseMessage)
            ->withTitle($notification->title)
            ->withBody($notification->body)
            ->withAdditionalData(
                [
                    'type' => $notification->type
                ]
            )
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
