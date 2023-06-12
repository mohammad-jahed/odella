<?php

namespace App\Notifications\Guests;

use App\Enums\NotificationType;
use App\Models\DailyReservation;
use App\Models\Notification as Notifications;
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
    private string $time;

    public function __construct($status, $time = null)
    {
        $this->status = $status;
        $this->time = $time;
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
        //TODO
//        /**
//         * @var Notifications $notification ;
//         */
//        $body = [
//            'user_id' => $notifiable->id,
//            'title' => 'ODELLA Daily Reservation.',
//            'type' => NotificationType::GuestDailyReservation,
//        ];
//        if ($this->status) {
//            $body['body'] = `Your Reservation Has been Confirmed,The Bus Will Arrived To Your Position At:' . $this->time`;
//        } else {
//            $body['body'] = "Sorry Your Request Has been Rejected, There are No Enough Seats";
//        }
//        $notification = Notifications::query()->create($body);
//        return (new FirebaseMessage)
//            ->withTitle($notification->title)
//            ->withBody($notification->body)
//            ->withAdditionalData($notification->type)
//            ->withPriority('normal')->asNotification($notifiable->fcm_token);

        if ($this->status) {
            return (new FirebaseMessage)
                ->withTitle('ODELLA Daily Reservation.')
                ->withBody('Your Reservation Has been Confirmed,
                 The Bus Will Arrived To Your Position At:' . $this->time)
                ->withPriority('high')->asNotification($notifiable->fcm_token);
        } else {
            return (new FirebaseMessage)
                ->withTitle('ODELLA Daily Reservation.')
                ->withBody("Sorry Your Request Has been Rejected,
                 There are No Enough Seats")
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
