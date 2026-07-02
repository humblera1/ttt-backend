<?php

namespace App\Listeners\v1\Question;

use App\Events\v1\Comment\CommentCreated;
use App\Events\v1\Comment\CommentDeleted;
use App\Events\v1\Comment\CommentRestored;
use App\Traits\Question\MarksQuestionForRatingRecalculation;

readonly class RequestQuestionRatingRecalculationOnCommentListener
{
    use MarksQuestionForRatingRecalculation;

    public function handleCommentCreated(CommentCreated $event): void
    {
        $this->markQuestionForRatingRecalculation($event->comment->question_id);
    }

    public function handleCommentDeleted(CommentDeleted $event): void
    {
        $this->markQuestionForRatingRecalculation($event->comment->question_id);
    }

    public function handleCommentRestored(CommentRestored $event): void
    {
        $this->markQuestionForRatingRecalculation($event->comment->question_id);
    }

    public function subscribe($events): void
    {
        $events->listen(
            CommentCreated::class,
            [self::class, 'handleCommentCreated'],
        );

        $events->listen(
            CommentDeleted::class,
            [self::class, 'handleCommentDeleted'],
        );

        $events->listen(
            CommentRestored::class,
            [self::class, 'handleCommentRestored'],
        );
    }
}
