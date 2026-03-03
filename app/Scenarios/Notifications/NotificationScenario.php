<?php

namespace App\Scenarios\Notifications;

use App\DTOs\v1\Notification\Scenarios\Contexts\ScenarioContext;
use App\DTOs\v1\Notification\Scenarios\Instructions\Instruction;
use App\Enums\Notification\NotificationCategory;
use App\Enums\Notification\NotificationType;
use App\Factories\v1\Notifications\NotificationPayloadMapperFactory;
use App\Mappers\Notification\NotificationPayloadMapper;

abstract class NotificationScenario
{
    abstract protected NotificationCategory $category {
        get;
    }

    abstract protected NotificationType $type {
        get;
    }

    public function __construct(
        protected NotificationPayloadMapperFactory $mapperFactory,
    ) {}

    /**
     * Builds a single notification instruction for the given context
     * or returns null if the scenario is not applicable.
     */
    abstract public function build(ScenarioContext $context): ?Instruction;

    /**
     * Gets specific mappers class by category and type of scenario.
     */
    protected function getMapper(): NotificationPayloadMapper
    {
        return $this->mapperFactory->forCategoryAndType(
            $this->category,
            $this->type,
        );
    }
}
