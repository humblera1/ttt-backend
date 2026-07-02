# Notification

In-app уведомления пользователей: категория → тип → экземпляр `UserNotification`.

## Модели

| Model | Роль |
|-------|------|
| `NotificationCategory` | Группа типов |
| `NotificationType` | Шаблон (key, category) |
| `UserNotification` | Запись пользователя (read/unread, soft delete) |

## API

Middleware: `auth:sanctum`.

| Method | Route | Назначение |
|--------|-------|------------|
| GET | `api/v1/notifications/list` | Список с pagination |
| PATCH | `api/v1/notifications/{notification}/read` | Mark as read |
| PATCH | `api/v1/notifications/read-all` | Mark all read |

Permissions: `view-own-notification`, `mark-as-read-notification`, …

## Отправка

- `NotificationSendingService` — создание записей
- `SendNotificationBatchJob`, `DispatchMassAdminNotificationJob` — очередь `Queue::Notifications`
- Mass send из Filament (`UserNotificationResource`)

## Comment notifications

`CommentNotificationSubscriber` слушает `CommentCreated` — уведомления участникам треда.

## Cleanup

Command `app:cleanup-user-notifications` — daily schedule.

Settings (Section `Notifications`):

- `read_ttl_days`, `unread_ttl_days`, `max_per_user`, `default_per_page`

Service: `UserNotificationCleanupService`.

## Code map

| Layer | Path |
|-------|------|
| API Controller | `app/Http/Controllers/v1/NotificationController.php` |
| Services | `app/Services/api/v1/Notification/` |
| Filament | `NotificationCategoryResource`, `NotificationTypeResource`, `UserNotificationResource` |
| Import | `app/Console/Commands/Notifications/ImportNotifications.php` |

## Related

- [settings/README.md](../settings/README.md)
- [reference/jobs-and-commands.md](../../reference/jobs-and-commands.md)
- [guides/cron-and-schedule.md](../../guides/cron-and-schedule.md)

## Planning

- Домен реализован до phase-vii; интеграция с comments — отдельные listeners.
