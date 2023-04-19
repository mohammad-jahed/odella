<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * @property mixed $city
 * @property mixed $area
 * @property mixed $street
 */
class LocationResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[Pure] #[ArrayShape(["city" => "\App\Http\Resources\CityResource", "area" => "\App\Http\Resources\AreaResource", "street" => "mixed"])]
    public function toArray(Request $request): array
    {
        return [
            "city" => new CityResource($this->city),
            "area" => new AreaResource($this->area),
            "street" => $this->street
        ];
    }
}
