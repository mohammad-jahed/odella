<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $day
 * @property mixed $position
 * @property mixed $start
 * @property mixed $end
 */
class ProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(["day" => "\App\Http\Resources\DayResource", "transfer_position" => "\App\Http\Resources\TransferPositionResource", "start" => "mixed", "end" => "mixed"])]
    public function toArray(Request $request): array
    {
        return [
            "day" => $this->whenLoaded("day", new DayResource($this->day)),
            "transfer_position" => $this->whenLoaded("position", new TransferPositionResource($this->position)),
            "start" => $this->start,
            "end" => $this->end
        ];
    }
}
