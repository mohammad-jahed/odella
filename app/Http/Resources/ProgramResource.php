<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $day
 * @property mixed $position
 * @property mixed $start
 * @property mixed $end
 * @property mixed $confirmAttendance1
 * @property mixed $confirmAttendance2
 */
class ProgramResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            "day" => $this->whenLoaded("day", new DayResource($this->day)),
            "transfer_position" => $this->whenLoaded("position", new TransferPositionResource($this->position)),
            "start" => $this->start,
            "end" => $this->end,
            'confirmAttendance1' => $this->confirmAttendance1,
            'confirmAttendance2' => $this->confirmAttendance2
        ];
    }
}
