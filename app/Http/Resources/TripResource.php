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
            'supervisor' => $this->whenLoaded('supervisor', new UserResource($this->supervisor()->first())),
            'busDriver' => $this->whenLoaded('busDriver', new BusDriverResource($this->busDriver()->first())),
            'time' => $this->whenLoaded('time', $this->time()->first()),
            'availableSeats' => $this->availableSeats,
            'lines' => $this->whenLoaded('lines', TransportationLineResource::collection($this->lines()->get())),
            'transferPositions' => $this->whenLoaded('transferPositions', TransferPositionResource::collection($this->transferPositions()->get())),
            'users' => $this->whenLoaded('users', UserResource::collection($this->users()->get()))
        ];
    }
}
