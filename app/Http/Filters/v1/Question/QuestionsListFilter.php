<?php

namespace App\Http\Filters\v1\Question;

use App\DTOs\v1\Question\QuestionsFilterDTO;
use App\Models\Question;
use Illuminate\Contracts\Database\Eloquent\Builder;

class QuestionsListFilter
{
    protected array $sortMap = [
        'rating'         => ['rating', 'asc'],
        '-rating'        => ['rating', 'desc'],
        'published_at'   => ['published_at', 'asc'],
        '-published_at'  => ['published_at', 'desc'],
        'title'          => ['title', 'asc'],
        '-title'         => ['title', 'desc'],
    ];

    public function __construct
    (
        protected QuestionsFilterDTO $dto,
    ) {}

    public function apply(): Builder
    {
        $dto = $this->dto;
        [$field, $direction] = $this->mapSort($dto->sort);

        $query = Question::query()
            ->with(['tags', 'grades', 'companies'])
            ->orderBy($field, $direction);

        if (isset($dto->title)) {
            $query->where('title', 'like', "%{$dto->title}%");
        }

        if (isset($dto->tagIds)) {
            $query->whereHas(
                'tags',
                fn (Builder $query): Builder => $query->whereIn('tags.id', $dto->tagIds)
            );
        }

        if (isset($dto->gradeIds)) {
            $query->whereHas(
                'grades',
                fn (Builder $query): Builder => $query->whereIn('grades.id', $dto->gradeIds)
            );
        }

        // todo: ограничение на фильтрацию по компаниям по разрешению
        if (isset($dto->companyIds)) {
            $query->whereHas(
                'companies',
                fn (Builder $query): Builder => $query->whereIn('companies.id', $dto->companyIds)
            );
        }

        return $query;
    }

    protected function mapSort(string $sort): array
    {
        return $this->sortMap[$sort];
    }
}
