<?php

namespace App\Notifications\Students;

use App\Models\Notification as Notifications;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class ExpiredSubscriptionNotification extends Notification
{
    use Queueable;

    private int $date;

    /**
     * Create a new notification instance.
     */
    public function __construct($date)
    {
        $this->date = $date;
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
            'title' => 'Your Subscription About to Expired!',
            'body' => 'Your Subscription Will Expired in' . $this->date . ' Days',
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
