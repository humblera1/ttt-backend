<?php

namespace App\Listeners\v1\Question;

use App\Events\v1\Comment\CommentCreated;
use App\Events\v1\Comment\CommentDeleted;
use App\Events\v1\Comment\CommentRestored;
use App\Services\api\v1\Question\QuestionAggregateService;

readonly class UpdateQuestionCommentsCountListener
{
    public function __construct(
        private QuestionAggregateService $service,
    ) {}

    public function handleCommentCreated(CommentCreated $event): void
    {
        $this->service->incrementCommentsCount($event->comment->question);
    }

    public function handleCommentDeleted(CommentDeleted $event): void
    {
        $this->service->decrementCommentsCount($event->comment->question);
    }

    public function handleCommentRestored(CommentRestored $event): void
    {
        $this->service->incrementCommentsCount($event->comment->question);
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
