<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property mixed $id
 * @property mixed $firstName
 * @property mixed $lastName
 * @property mixed $subscription
 * @property mixed $email
 * @property mixed $line
 * @property mixed $position
 * @property mixed $university
 * @property mixed $expiredSubscriptionDate
 * @property mixed $phoneNumber
 * @property mixed $location
 * @property mixed $payments
 * @property mixed $programs
 */
class UserResource extends JsonResource
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
            'firstName' => $this->firstName,
            'lastName' => $this->lastName,
            'email' => $this->email,
            "phoneNumber" => $this->phoneNumber,
            "expiredSubscriptionDate" => $this->expiredSubscriptionDate,
            'subscription' => new SubscriptionResource($this->subscription),
            'line' => new TransportationLineResource($this->line),
            'position' => new TransferPositionResource($this->position),
            'university' => new UniversityResource($this->university),
            'location' => new LocationResource($this->location),
            "payments" => $this->whenLoaded("payments", PayResource::collection($this->payments)),
            "programs" => $this->whenLoaded("programs", ProgramResource::collection($this->programs)),
        ];
    }
}
