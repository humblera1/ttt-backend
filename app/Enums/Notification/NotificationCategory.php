<?php

namespace App\Enums\Notification;

enum NotificationCategory: string
{
    case System = 'system';
    case Subscription = 'subscription';
    case Proposal = 'proposal';
    case ContentInteraction = 'content_interaction';
    case Moderation = 'moderation';
    case Account = 'account';
    case Custom = 'custom';

    public static function label(self|string $value): string
    {
        $value = $value instanceof self ? $value : self::from($value);

        return match ($value) {
            self::System => 'Системные',
            self::Subscription => 'Подписка',
            self::Proposal => 'Предложения вопросов',
            self::ContentInteraction => 'Взаимодействие с контентом',
            self::Moderation => 'Модерация и политика',
            self::Account => 'Аккаунт и безопасность',
            self::Custom => 'Прочие / Кастомные',
        };
    }
}
