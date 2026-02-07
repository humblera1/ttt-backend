<?php

namespace App\Listeners\v1\Question;

use App\Events\v1\Comment\CommentCreated;
use App\Events\v1\Comment\CommentDeleted;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpdateQuestionCommentsCountSubscriber implements ShouldQueue
{
    public function handleCommentCreated($event): void
    {
        // increment comments_count
    }

    public function handleCommentDeleted($event): void
    {
        // decrement comments_count
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
    }
}
