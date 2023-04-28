<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $name
 * @property mixed $id
 */
class AreaResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(["id" => "mixed", "name" => "mixed"])]
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
        ];
    }
}
