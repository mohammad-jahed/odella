<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $trip
 * @property mixed $user
 * @property mixed $description
 */
class ClaimResource extends JsonResource
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
            'trip' => $this->whenLoaded('trip', $this->trip),
            'user' => $this->whenLoaded('user', new UserResource($this->user)),
            'description' => $this->description
        ];
    }
}
