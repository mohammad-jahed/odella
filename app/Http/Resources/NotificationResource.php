<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @property string $title
 * @property integer $type
 * @property string $body
 * @property integer $is_read
 * @property mixed $id
 */
class NotificationResource extends JsonResource
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
            'title' => $this->title,
            'type' => $this->type,
            'body' => $this->body,
            'is_read' => $this->is_read
        ];
    }
}
