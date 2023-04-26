<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $driver
 * @property mixed $bus
 */
class BusDriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['driver' => "\Illuminate\Http\Resources\MissingValue|mixed", 'bus' => "\Illuminate\Http\Resources\MissingValue|mixed"])]
    public function toArray(Request $request): array
    {
        return [
            'driver' => $this->whenLoaded('driver', new DriverResource($this->driver)),
            'bus' => $this->whenLoaded('bus', new BusResource($this->bus)),
        ];
    }
}
