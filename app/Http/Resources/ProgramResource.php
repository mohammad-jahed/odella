<?php

namespace App\Http\Resources;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @method  Builder position()
 * @property mixed $start
 * @property mixed $end
 * @property mixed $confirmAttendance1
 * @property mixed $confirmAttendance2
 * @property mixed $id
 * @method Builder day()
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
            'id' => $this->id,
            "day" => new DayResource($this->whenLoaded('day')),
            "transfer_position" => new TransferPositionResource($this->whenLoaded('position')),
            "start" => $this->start,
            "end" => $this->end,
            'confirmAttendance1' => $this->confirmAttendance1,
            'confirmAttendance2' => $this->confirmAttendance2
        ];
    }
}
