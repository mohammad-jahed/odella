<?php

namespace App\Notifications\Students;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class ReturnTimeNotification extends Notification
{
    use Queueable;

    private User $user;
    private   String $remainTime;
    /**
     * Create a new notification instance.
     */
    public function __construct(User $user , $remainTime)
    {
        $this->user = $user;
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
        return (new FirebaseMessage)
                    ->withTitle('Get Ready!')
                    ->withBody('Your Bus Will Leave in '.$this->remainTime.'Minute')
                    ->withPriority('high')->asNotification($this->user->fcm_token);
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
