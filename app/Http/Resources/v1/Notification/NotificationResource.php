<?php

namespace App\Http\Resources\v1\Notification;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

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
            'id' => $this->id,
            'title' => $this->title,
            'body' => $this->body,
            'read_at' => $this->read_at,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            'type' => NotificationTypeResource::make(
                $this->whenLoaded('type'),
            ),

            'category' => NotificationCategoryResource::make(
                $this->whenLoaded('category'),
            ),
        ];
    }
}
