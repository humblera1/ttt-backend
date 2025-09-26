<?php

namespace App\Http\Filters\v1\Question;

use App\DTOs\v1\Question\QuestionsFilterDTO;
use App\Models\Question;
use Illuminate\Contracts\Database\Eloquent\Builder;

class QuestionsListFilter
{
    public function __construct
    (
        protected QuestionsFilterDTO $dto,
    ) {}

    public function apply(): Builder
    {
        $dto = $this->dto;

        $query = Question::query()->with(['tags', 'grades', 'companies']);

        if (isset($dto->title)) {
            $query->where('title', 'like', "%{$dto->title}%");
        }

        if (isset($dto->tagIds)) {
            $query->whereHas(
                'tags',
                fn (Builder $query): Builder => $query->whereIn('tags.id', $dto->tagIds)
            );
        }

        return $query;
    }
}
