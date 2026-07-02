# Cron и Schedule

## Laravel Schedule

Определения в [`routes/console.php`](../../routes/console.php):

| Command | Interval | Options | Purpose |
|---------|----------|---------|---------|
| `app:cleanup-user-notifications` | daily | — | Удаление старых `user_notifications` по TTL settings |
| `views:flush` | every minute | `withoutOverlapping()` | Flush Redis view buffer → DB |
| `rating:process-pending` | every 5 minutes | `withoutOverlapping()` | Dispatch `RecalculateQuestionRatingJob` для flagged questions |

## Dev environment

Контейнер `scheduler-dev` в [`docker-compose.yaml`](../../docker-compose.yaml) выполняет:

```bash
while true; do php artisan schedule:run; sleep 60; done
```

Отдельный system cron на dev не обязателен при работающем `scheduler-dev`.

## Production

На сервере один cron entry:

```
* * * * * php /path/to/artisan schedule:run >> /dev/null 2>&1
```

См. также root [`README.md`](../../README.md) — политика cleanup notifications.

## Command details

| Command | Class |
|---------|-------|
| `app:cleanup-user-notifications` | `App\Console\Commands\Notifications\CleanupUserNotifications` |
| `views:flush` | `App\Console\Commands\FlushQuestionViewsBufferCommand` |
| `rating:process-pending` | `App\Console\Commands\ProcessPendingRatingRecalculationsCommand` |

Полный список: [reference/jobs-and-commands.md](../reference/jobs-and-commands.md).

## Related

- [domains/question/views.md](../domains/question/views.md)
- [domains/question/rating.md](../domains/question/rating.md)
- [domains/notification/README.md](../domains/notification/README.md)
- [queues-and-horizon.md](queues-and-horizon.md)
