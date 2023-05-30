<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $name
 * @property mixed $phoneNumber
 * @property mixed $position
 * @property mixed $seatsNumber
 * @property mixed $id
 */
class DailyReservationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(["id" => "mixed", "name" => "mixed", "phoneNumber" => "mixed", "position" => "\Illuminate\Http\Resources\MissingValue|mixed", "seatsNumber" => "mixed"])]
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "phoneNumber" => $this->phoneNumber,
            "seatsNumber" => $this->seatsNumber,
            "position" => new TransferPositionResource($this->whenLoaded('position'))

        ];
    }
}
