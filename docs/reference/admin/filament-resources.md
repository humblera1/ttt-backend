# Filament Resources

Админ-панель: `/admin`. Filament 3, ресурсы в `app/Filament/Resources/`.

## Navigation groups

| Group | Resources |
|-------|-----------|
| **Main** | `QuestionResource`, `CommentResource`, `CompanyResource`, `PositionResource`, `TagResource`, `GradeResource` |
| **Moderation** | `QuestionCompanySuggestionResource`, `QuestionPositionSuggestionResource` |
| **Notifications** | `NotificationCategoryResource`, `NotificationTypeResource`, `UserNotificationResource` |
| **Users** | `UserResource` |

## Resource → Model

| Resource | Model |
|----------|-------|
| `QuestionResource` | `Question` |
| `CommentResource` | `Comment` |
| `CompanyResource` | `Company` |
| `PositionResource` | `Position` |
| `TagResource` | `Tag` |
| `GradeResource` | `Grade` |
| `QuestionCompanySuggestionResource` | `QuestionCompanySuggestion` |
| `QuestionPositionSuggestionResource` | `QuestionPositionSuggestion` |
| `NotificationCategoryResource` | `NotificationCategory` |
| `NotificationTypeResource` | `NotificationType` |
| `UserNotificationResource` | `UserNotification` |
| `UserResource` | `User` |

## QuestionResource relation managers

| Manager | Связь |
|---------|-------|
| `CommentsRelationManager` | `comments` |
| `StatisticsRelationManager` | `statistics` |
| `CompanySuggestionRelationManager` | company suggestions |
| `PositionSuggestionRelationManager` | position suggestions |

## Shared widgets

- `app/Filament/Resources/Widgets/Status/StatusBadge.php`
- `StatusWithReviewBadge.php`
- `Widgets/Notification/KeyBadge.php`

## Permissions

Доступ к ресурсам через Spatie permissions (`view-any-comment`, `edit-any-question`, …). См. [permissions.md](../permissions.md).

## Comment UI (phase VII)

| Step | Документ |
|------|----------|
| List + filters | [domains/comment/moderation-and-soft-delete.md](../../domains/comment/moderation-and-soft-delete.md) |
| Edit + delete reason | там же |
| Relation manager on Question | [domains/comment/README.md](../../domains/comment/README.md) |

## Related

- [`.cursor/rules/filament.mdc`](../../.cursor/rules/filament.mdc)
- [planning/phase-vii.md](../../planning/phase-vii.md)
