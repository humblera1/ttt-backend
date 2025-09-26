<?php

namespace App\Http\Resources\v1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class QuestionPreviewResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $canView = auth()->user()->can('view', $this->resource);

        return [
            'id' => $this->id,
            'is_premium' => $this->is_premium,
            'locked' => !$canView,
            $this->mergeWhen($canView, [
                'rating' => $this->rating,
                'title' => $this->title,
                'published_at' => $this->published_at,
                'views_count' => $this->views_count,
                'likes_count' => $this->likes_count,
                'grades' => GradeResource::collection($this->whenLoaded('grades')),
            ]),
        ];
    }
}
