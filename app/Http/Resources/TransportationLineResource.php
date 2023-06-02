<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $name
 * @property mixed $from
 * @property mixed $to
 * @property mixed $id
 */
class TransportationLineResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "from" => $this->whenNotNull($this->from),
            "to" => $this->whenNotNull($this->to),
        ];
    }
}
