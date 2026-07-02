# Очереди и Horizon

## Queue enum

`App\Enums\Queue\Queue`:

| Case | Value | Purpose |
|------|-------|---------|
| `Suggestions` | `suggestion` | Question-company/position suggestion processing |
| `Notifications` | `notification` | Async notification delivery |
| `Rating` | `rating` | `RecalculateQuestionRatingJob` |

## Horizon

- Config: [`config/horizon.php`](../../config/horizon.php)
- Path: `/horizon` (env `HORIZON_PATH`)
- Dev container: `horizon-dev` runs `php artisan horizon`

### Supervisors (defaults)

| Supervisor | Queue | maxProcesses (local) |
|------------|-------|----------------------|
| `suggestions-supervisor` | `suggestion` | 1 |
| `notification-supervisor` | `notification` | 1 |
| `rating-supervisor` | `rating` | 1 |

Connection: `redis`. Sequential processing on suggestion queue by design (single worker).

## Jobs by queue

| Job | Queue |
|-----|-------|
| `RecalculateQuestionRatingJob` | `rating` |
| `SendNotificationBatchJob` | `notification` |
| `DispatchMassAdminNotificationJob` | `notification` |
| `UpdateQuestionCompanySuggestionListener` | `suggestion` (queued listener) |
| `UpdateQuestionPositionSuggestionListener` | `suggestion` (queued listener) |

`FlushQuestionViewsBufferJob` exists but schedule uses command → service synchronously in `views:flush`.

## Tests

PHPUnit: `QUEUE_CONNECTION=sync` — jobs run inline unless `Bus::fake()` / `Queue::fake()`.

Rating tests often use:

```php
Bus::fake();
// or
Artisan::call('rating:process-pending');
```

## Related

- [reference/jobs-and-commands.md](../reference/jobs-and-commands.md)
- [domains/question/rating.md](../domains/question/rating.md)
- [cron-and-schedule.md](cron-and-schedule.md)
- [local-development.md](local-development.md)
