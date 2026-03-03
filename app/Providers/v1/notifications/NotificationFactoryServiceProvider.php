<?php

namespace App\Providers\v1\notifications;

use App\Enums\Notification\NotificationCategory;
use App\Enums\Notification\NotificationType;
use App\Factories\v1\Notifications\NotificationPayloadMapperFactory;
use Illuminate\Support\ServiceProvider;
use RuntimeException;

class NotificationFactoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(NotificationPayloadMapperFactory::class, function () {
            $config = config('notification.mappers', []);

            $mappersByCategoryAndType = [];

            foreach ($config as $category => $types) {
                $this->checkCategoryExists($category);

                foreach ($types as $type => $mapperClass) {
                    $this->checkTypeExists($type);

                    $mappersByCategoryAndType[$category][$type] = app($mapperClass);
                }
            }

            return new NotificationPayloadMapperFactory($mappersByCategoryAndType);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }

    protected function checkCategoryExists(string $category): void
    {
        if (!NotificationCategory::tryFrom($category)) {
            throw new RuntimeException(
                "Unknown notification category [$category] defined in notification.mappers config."
            );
        }
    }

    protected function checkTypeExists(string $type): void
    {
        if (!NotificationType::tryFrom($type)) {
            throw new RuntimeException(
                "Unknown notification type [$type] defined in notification.mappers config."
            );
        }
    }
}
