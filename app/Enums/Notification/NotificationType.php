<?php

namespace App\Enums\Notification;

enum NotificationType: string
{
    // system
    case SystemAnnouncement = 'system_announcement';
    case AdminMessage = 'admin_message';

    // subscription
    case SubscriptionActivated = 'subscription_activated';
    case SubscriptionExpiring = 'subscription_expiring';
    case SubscriptionExpired = 'subscription_expired';
    case PaymentSuccessful = 'payment_successful';
    case PaymentFailed = 'payment_failed';
    case PaymentRefunded = 'payment_refunded';

    // proposal
    case ProposalReceived = 'proposal_received';
    case ProposalUnderReview = 'proposal_under_review';
    case ProposalAccepted = 'proposal_accepted';
    case ProposalRejected = 'proposal_rejected';

    // content_interaction
    case QuestionLiked = 'question_liked';
    case QuestionDisliked = 'question_disliked';
    case NewComment = 'new_comment';
    case CommentReply = 'comment_reply';
    case QuestionFavorited = 'question_favorited';
    case NewPremiumQuestion = 'new_premium_question';

    // moderation
    case CommentRemoved = 'comment_removed';
    case UserBanned = 'user_banned';

    // account
    case EmailChanged = 'email_changed';
    case PasswordChanged = 'password_changed';
    case NewLogin = 'new_login';

    // custom
    case CustomMessage = 'custom_message';

    public static function label(self|string $value): string
    {
        $value = $value instanceof self ? $value : self::from($value);

        return match ($value) {
            // system
            self::SystemAnnouncement => 'Системное объявление',
            self::AdminMessage => 'Сообщение от администратора',

            // subscription
            self::SubscriptionActivated => 'Подписка активирована',
            self::SubscriptionExpiring => 'Подписка скоро истечет',
            self::SubscriptionExpired => 'Подписка истекла',
            self::PaymentSuccessful => 'Платеж успешен',
            self::PaymentFailed => 'Платеж не прошел',
            self::PaymentRefunded => 'Платеж возвращен',

            // proposal
            self::ProposalReceived => 'Предложение получено',
            self::ProposalUnderReview => 'Предложение на модерации',
            self::ProposalAccepted => 'Предложение принято',
            self::ProposalRejected => 'Предложение отклонено',

            // content_interaction
            self::QuestionLiked => 'Ваш вопрос получил лайк',
            self::QuestionDisliked => 'Ваш вопрос получил дизлайк',
            self::NewComment => 'Новый комментарий к вашему вопросу',
            self::CommentReply => 'Ответ на ваш комментарий',
            self::QuestionFavorited => 'Ваш вопрос добавлен в избранное',
            self::NewPremiumQuestion => 'Новый премиальный вопрос',

            // moderation
            self::CommentRemoved => 'Ваш комментарий удален модерацией',
            self::UserBanned => 'Аккаунт заблокирован',

            // account
            self::EmailChanged => 'Email аккаунта изменен',
            self::PasswordChanged => 'Пароль изменен',
            self::NewLogin => 'Новый вход в аккаунт',

            // custom
            self::CustomMessage => 'Кастомное сообщение',
        };
    }
}
