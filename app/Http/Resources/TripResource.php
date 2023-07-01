<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $availableSeats
 * @property mixed $id
 * @method Builder supervisor()
 * @method Builder busDriver()
 * @method Builder lines()
 * @method Builder transferPositions()
 * @method Builder users()
 * @method Builder time()
 * @method Builder tripUsers()
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
            'availableSeats' => $this->availableSeats,
            'time' => $this->whenLoaded('time'),
            'supervisor' => new UserResource($this->whenLoaded('supervisor')),
            'lines' => TransportationLineResource::collection($this->whenLoaded('lines')),
            'transferPositions' => TransferPositionResource::collection($this->whenLoaded('transferPositions')),
            'busDriver' => new BusDriverResource($this->whenLoaded('busDriver')),
            'tripUsers' => $this->tripUsers()->get(),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'evaluations' => EvaluationResource::collection($this->whenLoaded('evaluations'))
        ];
    }
}
