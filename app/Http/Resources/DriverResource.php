<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $id
 * @property mixed $firstname
 * @property mixed $lastname
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
    #[ArrayShape(["id" => "mixed", "firstname" => "mixed", "lastname" => "mixed", "number" => "mixed", "buses" => "\Illuminate\Http\Resources\MissingValue|mixed"])]
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "firstname" => $this->firstname,
            "lastname" => $this->lastname,
            "number" => $this->number,
            "buses" => $this->whenLoaded("buses", BusResource::collection($this->buses))
        ];
    }
}
