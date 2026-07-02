# Авторизация

## API: Sanctum

Защищённые маршруты используют middleware `auth:sanctum`. Клиент передаёт Bearer token (personal access token или session — по конфигурации Sanctum).

Публичные маршруты (например `GET /api/v1/questions/list`, `POST /api/v1/questions/{question}/view`) доступны без токена, но `authorize()` в Form Request может требовать аутентификации для части операций.

## Spatie Permission

Permissions генерируются командой `app:init-roles` из [`config/roles.php`](../../config/roles.php):

```
{action}-{entity}
```

Примеры: `view-any-question`, `propose-question`, `vote-question`, `view-any-comment`.

`RoleService::generatePermission()` склеивает kebab-case action и entity.

## Роли

| Роль | Конфиг-ключ | Назначение |
|------|-------------|------------|
| Admin | `Role::Admin` | Полный доступ в Filament и расширенные API abilities |
| Moderator | `Role::Moderator` | Модерация контента, suggestions, комментарии |
| Premium User | `Role::PremiumUser` | Premium-вопросы, фильтр по company |
| User | `Role::User` | Базовый зарегистрированный пользователь |
| Guest | `Role::Guest` | Минимальный набор для неавторизованных сценариев |

Матрица permissions по ролям — в [reference/permissions.md](../../reference/permissions.md).

## Policies

Модели помечены `#[UsePolicy(...)]` (например `QuestionPolicy`, `CommentPolicy`). Form Request вызывает:

```php
$user->can('viewAny', Question::class);
$user->can('vote', $question);
$user->can('propose', Question::class);
```

Route model binding передаёт сущность в policy для instance-level checks.

## Filament

Доступ к ресурсам и действиям — через те же Spatie permissions (`view-any-comment`, `edit-any-question`, …). Filament не использует отдельную систему прав.

## Ban

`UserBanService` блокирует пользователей с ability `ban-user`. Забаненные пользователи исключаются scope'ами (например `NotBannedScope` на User в Filament).

## Связанные разделы

- [reference/permissions.md](../../reference/permissions.md)
- [domains/auth-and-users/README.md](../../domains/auth-and-users/README.md)
