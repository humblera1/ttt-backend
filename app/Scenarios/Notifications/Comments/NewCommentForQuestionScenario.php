<?php

namespace App\Scenarios\Notifications\Comments;

use App\DTOs\v1\Notification\Mappers\Contexts\Comments\CommentNotificationPayloadContext;
use App\DTOs\v1\Notification\Scenarios\Contexts\ScenarioContext;
use App\DTOs\v1\Notification\Scenarios\Instructions\Instruction;
use App\DTOs\v1\Notification\Scenarios\Instructions\InstructionToOne;
use App\DTOs\v1\Notification\TemplatedNotificationDTO;
use App\Enums\Notification\NotificationCategory;
use App\Enums\Notification\NotificationType;
use App\Mappers\Notification\Comments\NewCommentNotificationMapper;
use App\Scenarios\Notifications\NotificationScenario;

final class NewCommentForQuestionScenario extends NotificationScenario
{
    protected NotificationCategory $category = NotificationCategory::ContentInteraction;

    protected NotificationType $type = NotificationType::NewComment;

    public function build(ScenarioContext $context): ?Instruction
    {
        $comment = $context->comment;

        $comment->loadMissing([
            'question.user',
            'user',
        ]);

        $question = $comment->question;
        $responded = $comment->user;

        $recipient = $question->user;

        if (!$recipient) {
            return null;
        }

        // user comments on his own question
        if ($recipient->id === $responded->id) {
            return null;
        }

        /** @var NewCommentNotificationMapper $mapper */
        $mapper = $this->getMapper();

        $payloadContext = new CommentNotificationPayloadContext(
            reply: $comment,
            question: $question,
            respondent: $responded,
        );

        $data = $mapper->map($payloadContext);

        return new InstructionToOne(
            recipient: $recipient,
            notification: new TemplatedNotificationDTO(
                categoryKey: $this->category->value,
                typeKey: $this->type->value,
                data: $data,
            ),
        );
    }
}
