<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $id
 * @property mixed $firstName
 * @property mixed $lastName
 * @property mixed $number
 * @property mixed $buses
 */
class DriverResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(["id" => "mixed", "firstName" => "mixed", "lastName" => "mixed", "number" => "mixed", "buses" => "\Illuminate\Http\Resources\MissingValue|mixed"])]
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "firstName" => $this->firstName,
            "lastName" => $this->lastName,
            "number" => $this->number,
            "buses" => $this->whenLoaded("buses", BusResource::collection($this->buses))
        ];
    }
}
