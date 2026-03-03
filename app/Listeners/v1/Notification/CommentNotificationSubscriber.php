<?php

namespace App\Listeners\v1\Notification;

use App\DTOs\v1\Notification\Scenarios\Contexts\Comments\CommentNotificationScenarioContext;
use App\Enums\Queue\Queue;
use App\Events\v1\Comment\CommentCreated;
use App\Interfaces\v1\Scenarios\ScenarioInterface;
use App\Scenarios\Notifications\NotificationScenario;
use App\Scenarios\Notifications\Comments\{CommentReplyScenario, NewCommentForQuestionScenario};
use App\Services\api\v1\Notification\NotificationSendingService;
use Illuminate\Contracts\Queue\ShouldQueue;

class CommentNotificationSubscriber implements ScenarioInterface, ShouldQueue
{
    /**
     * The name of the queue the job should be sent to.
     */
    public string $queue = Queue::Notifications->value;

    public function __construct(
        private readonly NotificationSendingService $service,
    ) {}

    public function getScenarios(): array
    {
        return [
            CommentReplyScenario::class,
            NewCommentForQuestionScenario::class,
        ];
    }

    public function handleCommentCreated(CommentCreated $event): void
    {
        $scenarios = $this->getScenarios();

        $context = new CommentNotificationScenarioContext(
            comment: $event->comment,
        );

        foreach ($scenarios as $scenario) {
            $instance = app($scenario);

            /** @var NotificationScenario $instance */
            $instruction = $instance->build($context);

            if ($instruction) {
                $this->service->sendTemplatedTo($instruction->recipient, $instruction->notification);
            }
        }
    }

    public function subscribe($events): void
    {
        $events->listen(
            CommentCreated::class,
            [self::class, 'handleCommentCreated'],
        );
    }
}
