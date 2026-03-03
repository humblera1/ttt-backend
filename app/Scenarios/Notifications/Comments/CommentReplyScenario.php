<?php

namespace App\Scenarios\Notifications\Comments;

use App\DTOs\v1\Notification\Mappers\Contexts\Comments\CommentReplyNotificationPayloadContext;
use App\DTOs\v1\Notification\Scenarios\Contexts\ScenarioContext;
use App\DTOs\v1\Notification\Scenarios\Instructions\Instruction;
use App\DTOs\v1\Notification\Scenarios\Instructions\InstructionToOne;
use App\DTOs\v1\Notification\TemplatedNotificationDTO;
use App\Enums\Notification\NotificationCategory;
use App\Enums\Notification\NotificationType;
use App\Mappers\Notification\Comments\CommentReplyNotificationMapper;
use App\Scenarios\Notifications\NotificationScenario;

final class CommentReplyScenario extends NotificationScenario
{
    protected NotificationCategory $category = NotificationCategory::ContentInteraction;

    protected NotificationType $type = NotificationType::CommentReply;

    /** @inheritdoc */
    public function build(ScenarioContext $context): ?Instruction
    {
        $comment = $context->comment;

        $comment->loadMissing([
            'parent.user',
            'question',
            'user',
        ]);

        $parent = $comment->parent;
        $question = $comment->question;
        $responded = $comment->user;

        if (!$parent) {
            return null;
        }

        $recipient = $parent->user;

        // user replies on his own comment
        if ($recipient->id === $responded->id) {
            return null;
        }

        /** @var CommentReplyNotificationMapper $mapper */
        $mapper = $this->getMapper();

        $payloadContext = new CommentReplyNotificationPayloadContext(
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
