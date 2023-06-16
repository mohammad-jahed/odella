<?php

namespace App\Notifications\Employees;

use App\Enums\NotificationType;
use App\Models\User;
use App\Models\Notification as Notifications;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Facades\Larafirebase;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class PendingUserRegisterNotification extends Notification
{
    use Queueable;

    private User $user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(User $notifiable): array
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
            'title' => 'New Student!',
            'type' => NotificationType::Registration,
            'body' => $this->user->firstName . ' ' . $this->user->lastName . ' Want to Register',
        ]);
        return Larafirebase::withTitle($notification->title)
            ->withBody($notification->body)
            ->withSound('default')
            ->withPriority('high')
            ->withAdditionalData([
                'color' => '#rrggbb',
                'badge' => 0,
            ])
            //->withAdditionalData($notification->type)
            ->sendNotification($notifiable->fcm_token);
//        return (new FirebaseMessage)
//            ->withTitle($notification->title)
//            ->withBody($notification->body)
//            ->withAdditionalData($notification->type)
//            ->withPriority('normal')->asNotification($notifiable->fcm_token);
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(User $notifiable): array
    {
        return [
            //
        ];
    }
}
