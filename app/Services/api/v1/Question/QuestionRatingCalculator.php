<?php

namespace App\Services\api\v1\Question;

use App\Models\Question;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Computes question rating from denormalized aggregate columns only.
 */
readonly class QuestionRatingCalculator
{
    /**
     * @return int Rounded rating persisted as integer (may be negative).
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function calculate(Question $question): int
    {
        $votesWeight = setting('rating_weights.votes', 1.0);
        $metWeight = setting('rating_weights.met', 3.0);
        $viewsWeight = setting('rating_weights.views', 0.3);
        $commentsWeight = setting('rating_weights.comments', 0.7);

        $votesContribution = $question->likes_count * $votesWeight;
        $metContribution = $question->met_in_real_interview_count * $metWeight;
        $viewsContribution = log($question->views_count + 1) * $viewsWeight;
        $commentsContribution = log($question->comments_count + 1) * $commentsWeight;

        $score = $votesContribution
            + $metContribution
            + $viewsContribution
            + $commentsContribution;

        return (int) round($score);
    }
}
