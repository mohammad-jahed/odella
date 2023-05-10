<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $trip
 * @property mixed $user
 * @property mixed $image
 * @property mixed $description
 * @method user()
 */
class Lost_FoundResource extends JsonResource
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
            'description' => $this->description,
            'image' => $this->image,
            'trip' => $this->whenLoaded('trip'),
            'user' => $this->whenLoaded('user', new UserResource($this->user()->first())),
        ];
    }
}
