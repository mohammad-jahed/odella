<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $supervisor
 * @property mixed $busDriver
 * @property mixed $lines
 * @property mixed $time
 * @property mixed $availableSeats
 * @property mixed $id
 * @property mixed $transferPositions
 * @property mixed $users
 */
class TripResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'supervisor' => $this->whenLoaded('supervisor', new UserResource($this->supervisor)),
            'busDriver' => $this->whenLoaded('busDriver', new BusDriverResource($this->busDriver)),
            'time' => $this->whenLoaded('time', $this->time),
            'availableSeats' => $this->availableSeats,
            'lines' => $this->whenLoaded('lines', TransportationLineResource::collection($this->lines)),
            'transferPositions' => $this->whenLoaded('transferPositions', TransferPositionResource::collection($this->transferPositions)),
            'users' => $this->whenLoaded('users', UserResource::collection($this->users))
        ];
    }
}
