<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use JetBrains\PhpStorm\ArrayShape;

/**
 * @property integer $daysNumber
 * @property integer $price
 * @property string $name
 */
class SubscriptionResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    #[ArrayShape(['daysNumber' => "int", 'price' => "int", 'name' => "string"])]
    public function toArray(Request $request): array
    {
        return [
            'daysNumber' => $this->daysNumber,
            'price' => $this->price,
            'name' => $this->name
        ];
    }
}
