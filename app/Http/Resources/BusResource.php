<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $image
 * @property mixed $details
 * @property mixed $capacity
 * @property mixed $key
 * @property mixed $id
 */
class BusResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(["id" => "mixed", "key" => "mixed", "capacity" => "mixed", "details" => "mixed", "image" => "mixed"])]
    public function toArray(Request $request): array
    {
        return [
            "key" => $this->key,
            "capacity" => $this->capacity,
            "details" => $this->details,
            "image" => $this->image
        ];
    }
}
