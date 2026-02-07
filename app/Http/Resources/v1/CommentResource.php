<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
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
            'question_id' => $this->question_id,
            'parent_id' => $this->parent_id,
            'body' => $this->body,
            'likes_count' => $this->likes_count,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'deleted_reason_code' => $this->deleted_reason_code,
            'deleted_reason_comment' => $this->deleted_reason_comment,
            'user' => new UserResource($this->whenLoaded('user')),
        ];
    }
}
