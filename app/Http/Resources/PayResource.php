<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property mixed $amount
 * @property mixed $date
 */
class PayResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(["amount" => "mixed", "date" => "mixed"])]
    public function toArray(Request $request): array
    {
        return [
            "amount" => $this->amount,
            "date" => $this->date
        ];
    }
}
