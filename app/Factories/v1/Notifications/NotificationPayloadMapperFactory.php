<?php

namespace App\Factories\v1\Notifications;

use App\Enums\Notification\NotificationCategory;
use App\Enums\Notification\NotificationType;
use App\Mappers\Notification\NotificationPayloadMapper;
use RuntimeException;

/**
 * @see NotificationFactoryServiceProvider
 */
readonly class NotificationPayloadMapperFactory
{
    /**
     * @param array<string, <string, NotificationPayloadMapper>> $mappersByCategoryAndType
     */
    public function __construct(
        private array $mappersByCategoryAndType,
    ) {}

    public function forCategoryAndType(NotificationCategory $category, NotificationType $type): NotificationPayloadMapper
    {
        $categoryKey = $category->value;
        $typeKey = $type->value;

        if (!isset($this->mappersByCategoryAndType[$categoryKey][$typeKey])) {
            throw new RuntimeException("No payload mapper defined for category [$categoryKey] & type [$typeKey]");
        }

        return $this->mappersByCategoryAndType[$categoryKey][$typeKey];
    }
}
