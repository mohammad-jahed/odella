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
            'review' => $this->review,
            'trip' => new TripResource($this->whenLoaded('trip')),
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
