<?php

namespace App\Services\api\v1\Question;

use App\Exceptions\v1\RepositoryException;
use App\Repositories\v1\QuestionRepository;

/**
 * Persists recalculated question rating from denormalized aggregates.
 */
class QuestionRatingService
{
    public function __construct(
        private readonly QuestionRepository $repository,
        private readonly QuestionRatingCalculator $calculator,
    ) {}

    /**
     * Recalculates and persists rating for the given question.
     *
     * @throws RepositoryException
     */
    public function recalculate(int $questionId): void
    {
        $question = $this->repository->findByIdOrFail($questionId);
        $rating = $this->calculator->calculate($question);

        $this->repository->persistRating($questionId, $rating);
    }
}
