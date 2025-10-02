<?php

namespace App\Services\api\v1;

use App\DTOs\v1\Question\QuestionsFilterDTO;
use App\Http\Filters\v1\Question\QuestionsListFilter;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class QuestionService
{
    public function getQuestionsList(QuestionsFilterDTO $dto): LengthAwarePaginator
    {
        return new QuestionsListFilter($dto)->apply()->paginate(setting('question.per_page', 15));
    }
}
