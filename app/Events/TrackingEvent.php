<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TrackingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public float $lng;
    public float $lat;

    public int $trip_id;
    /**
     * Create a new event instance.
     */
    public function __construct($lng , $lat, $trip_id)
    {
        $this->lng = $lng;
        $this->lat = $lat;
        $this->trip_id = $trip_id;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('tracking.'.$this->trip_id),
        ];
    }
    public function broadcastWith(): array
    {
        return[
          'lng'=> $this->lng,
          'lat'=> $this->lat
        ];
    }
    public function broadcastAs(): string
    {
        return 'client-tracking';
    }
}
