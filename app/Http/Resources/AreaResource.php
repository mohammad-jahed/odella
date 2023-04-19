<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;
use JetBrains\PhpStorm\Pure;

/**
 * @property mixed $name
 */
class AreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(["name" => "mixed", "city" => "\App\Http\Resources\CityResource"])] #[Pure]
    public function toArray(Request $request): array
    {
        return [
            "name" => $this->name,
        ];
    }
}
