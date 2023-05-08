<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $user
 * @property mixed $trip
 * @property mixed $review
 */
class EvaluationResource extends JsonResource
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
            'user' => $this->whenLoaded('user', new UserResource($this->user)),
            'trip' => $this->whenLoaded('trip', new TripResource($this->trip)),
            'review' => $this->review
        ];
    }
}
