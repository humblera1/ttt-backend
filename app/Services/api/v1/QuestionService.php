<?php

namespace App\Services\api\v1;

use App\DTOs\v1\Question\QuestionsFilterDTO;
use App\Http\Filters\v1\Question\QuestionsListFilter;
use Illuminate\Database\Eloquent\Collection;

class QuestionService
{
    public function getQuestionsList(QuestionsFilterDTO $dto): Collection
    {
        return new QuestionsListFilter($dto)->apply()->get();
    }
}
