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
 * @property mixed $image
 * @method subscription()
 * @method line()
 * @method position()
 * @method university()
 * @method location()
 * @method payments()
 * @method programs()
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
            "image" => $this->image,
            "expiredSubscriptionDate" => $this->expiredSubscriptionDate,
            'subscription' => new SubscriptionResource($this->whenLoaded('subscription')),
            'line' => new TransportationLineResource($this->whenLoaded('line')),
            'position' => new TransferPositionResource($this->whenLoaded('position')),
            'university' => new UniversityResource($this->whenLoaded('university')),
            'location' => new LocationResource($this->whenLoaded('location')),
            "payments" => PayResource::collection($this->whenLoaded('pays')),
            "programs" => ProgramResource::collection($this->whenLoaded('programs')),
        ];
    }
}
