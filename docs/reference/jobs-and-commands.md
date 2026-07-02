# Jobs and Commands

## Scheduled commands

Из [`routes/console.php`](../../routes/console.php):

| Command | Schedule | Purpose |
|---------|----------|---------|
| `app:cleanup-user-notifications` | daily | TTL cleanup in-app notifications |
| `views:flush` | every minute, `withoutOverlapping` | Redis views buffer → `questions.views_count` |
| `rating:process-pending` | every 5 minutes, `withoutOverlapping` | Dispatch rating jobs for flagged questions |

Cron на хосте: `* * * * * php artisan schedule:run` (см. [guides/cron-and-schedule.md](../../guides/cron-and-schedule.md)).

## Artisan commands

| Signature | Class | Purpose |
|-----------|-------|---------|
| `views:flush` | `FlushQuestionViewsBufferCommand` | Manual flush question views |
| `rating:process-pending` | `ProcessPendingRatingRecalculationsCommand` | Safety-net rating recalc |
| `app:cleanup-user-notifications` | `CleanupUserNotifications` | Notification cleanup |
| `app:init-roles` | `InitRoles` | Seed roles/permissions from config |
| `app:init-admin-user` | `InitAdminUser` | Create admin user |
| `app:init` | `Init` | Project init wrapper |
| `settings:import` | `ImportSettings` | Sync settings from config |
| `notifications:import` | `ImportNotifications` | Import notification types |

## Queued jobs

| Job | Queue | Unique / notes |
|-----|-------|----------------|
| `RecalculateQuestionRatingJob` | `Queue::Rating` (`rating`) | `ShouldBeUnique` per questionId |
| `FlushQuestionViewsBufferJob` | default | Exists; schedule uses command → service directly |
| `SendNotificationBatchJob` | `Queue::Notifications` | Batch send |
| `DispatchMassAdminNotificationJob` | `Queue::Notifications` | Mass admin notification |

Horizon supervisors: `rating-supervisor`, `notification-supervisor`, `suggestions-supervisor` — см. [guides/queues-and-horizon.md](../../guides/queues-and-horizon.md).

## Related

- [domains/question/rating.md](../../domains/question/rating.md)
- [domains/question/views.md](../../domains/question/views.md)
- [domains/notification/README.md](../../domains/notification/README.md)
