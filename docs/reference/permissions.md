# Permissions

Spatie Laravel Permission. Имена генерируются `RoleService::generatePermission(action, entity)` → `{action}-{entity}` (kebab-case).

Инициализация: `php artisan app:init-roles` из [`config/roles.php`](../../config/roles.php).

## Roles

| Config key | DB name | Назначение |
|------------|---------|------------|
| `Role::Admin` | `admin` | Full Filament + extended API |
| `Role::Moderator` | `moderator` | Content moderation |
| `Role::PremiumUser` | `premium-user` | Premium questions, company filter |
| `Role::User` | `user` | Registered user |
| `Role::Guest` | `guest` | Minimal public abilities |

## API-relevant permissions (Question)

| Permission | Typical use |
|------------|-------------|
| `view-any-question` | List questions |
| `view-premium-question` | See premium content |
| `view-own-question` | Own drafts |
| `propose-question` | POST propose |
| `vote-question` | PUT question vote |
| `send-feedback-question` | POST feedback |
| `create-comment` | POST comment |
| `view-any-comment` | GET comments list |
| `edit-own-comment` | PATCH own comment |
| `delete-own-comment` | DELETE own comment |
| `restore-own-comment` | POST restore |
| `vote-comment` | PUT comment vote |
| `view-deleted-comment` | See soft-deleted comments |

## API-relevant permissions (Taxonomy)

| Permission | Entity |
|------------|--------|
| `view-any-company` | Company list |
| `view-any-position` | Position list |
| `view-any-tag` | Tag list |
| `view-any-grade` | Grade list |
| `suggest-company` | Propose new company in statistic |
| `suggest-position` | Propose new position |
| `suggest-tag` | Propose new tag |

## API-relevant permissions (Auth / Notification)

| Permission | Use |
|------------|-----|
| `view-own-notification` | Notification list |
| `mark-as-read-notification` | Mark read |
| `edit-own-user` | Profile |
| `ban-user` | Admin ban |

## Policies

Instance checks через Eloquent policies:

```php
$user->can('vote', $question);
$user->can('propose', Question::class);
$user->can('update', $comment);
```

Policies: `app/Policies/`, `app/Policies/v1/`.

## Endpoint matrix (summary)

| Route name | Permission |
|------------|------------|
| `api.v1.questions.list` | `view-any-question` |
| `api.v1.questions.propose` | `propose-question` |
| `api.v1.questions.vote.update` | `vote-question` |
| `api.v1.questions.comments.store` | `create-comment` |
| `api.v1.comments.vote.update` | `vote-comment` |
| `api.v1.companies.list` | `view-any-company` |
| `api.v1.tags.list` | `view-any-tag` |
| `api.v1.notifications.list` | `view-own-notification` |

## Filament permissions

Admin role includes `view-any-*`, `edit-any-*`, `delete-any-*`, `change-status-*` для большинства entities. Moderator — subset без destructive actions на taxonomy.

Полный список — [`config/roles.php`](../../config/roles.php).

## Related

- [explanation/architecture/authorization.md](../../explanation/architecture/authorization.md)
- [domains/auth-and-users/README.md](../../domains/auth-and-users/README.md)
- [api/routes.md](../api/routes.md)
