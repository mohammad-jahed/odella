<?php

namespace App\Notifications\Employees;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Kutia\Larafirebase\Messages\FirebaseMessage;

class PendingUserRegisterNotification extends Notification
{
    use Queueable;

    private User $user;
    private User $employees;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $user, User $employees)
    {
        $this->user = $user;
        $this->employees = $employees;
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
            ->withTitle('New Student!')
            ->withBody($this->user->firstName . ' ' . $this->user->lastName . ' Want to Register')
            ->withPriority('normal')->asNotification($this->employees->fcm_token);
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
