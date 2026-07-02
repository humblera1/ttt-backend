# Comment — модерация и soft delete

## Purpose

Комментарии не удаляются физически из API пользователем — используется `SoftDeletes`. Модератор может удалять с указанием причины через Filament (`CommentDeleteWithReasonDTO`).

## Статус в UI

«Активен» / «Удалён» определяется **только** по `deleted_at`, без поля `is_deleted`.

Filament `CommentResource`:

- `getEloquentQuery()` — `withTrashed()`
- Badge по `deleted_at`
- Фильтр: `TrashedFilter` или ternary по `deleted_at`

## ReasonForDeletion

Enum `App\Enums\Comment\ReasonForDeletion` — код причины при модераторском удалении. User self-delete использует `UserRemoved`.

## Restore

API: `POST api.v1.comments.restore` на soft-deleted comment (route binding `restorable_comment`).

Событие `CommentRestored` → increment `comments_count`, rating recalc.

## Permissions

| Ability | Назначение |
|---------|------------|
| `view-deleted-comment` | Видеть удалённые в API/Filament |
| `restore-own-comment` / `restore-any-comment` | Восстановление |
| `delete-any-comment` | Модераторское удаление |
| `force-delete-any-comment` | Filament force delete |

## Filament (phase VII)

| Шаг | Содержание |
|-----|------------|
| 01 | ListComments: колонки, фильтры, eager load |
| 02 | EditComment: форма, причина удаления |
| 03 | CommentsRelationManager на QuestionResource |

## Code map

| Component | Path |
|-----------|------|
| Delete with reason | `CommentService::deleteWithReason()` |
| DTO | `app/DTOs/v1/Comment/CommentDeleteWithReasonDTO.php` |
| Filament resource | `app/Filament/Resources/CommentResource.php` |
| Relation manager | `app/Filament/Resources/QuestionResource/RelationManagers/CommentsRelationManager.php` |

## Related

- [README.md](README.md)
- [reference/admin/filament-resources.md](../../reference/admin/filament-resources.md)

## Planning

- [phase-vii.md](../../planning/phase-vii.md)
