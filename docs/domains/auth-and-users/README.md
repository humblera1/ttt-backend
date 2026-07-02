# Auth and Users

Регистрация, аутентификация, профиль и управление пользователями в API и Filament.

## API Auth

Prefix: `api/v1/auth/`. Controller: `AuthController`.

| Method | Route | Middleware | Назначение |
|--------|-------|------------|------------|
| POST | `/login` | — | Login, token |
| POST | `/me` | — | Current user (token validation) |
| POST | `/register` | `auth:sanctum` | Register (invite flow) |
| POST | `/change-password` | `auth:sanctum` | Change password |
| POST | `/forgot-password` | `auth:sanctum` | Forgot |
| POST | `/reset-password` | `auth:sanctum` | Reset |

Route names: `api.v1.auth.*`.

Service: `app/Services/api/v1/AuthService.php`.

Tests: `tests/Feature/v1/Auth/`.

## Sanctum

Bearer token для API. Protected routes используют `middleware('auth:sanctum')`.

## Roles and permissions

Инициализация:

```bash
docker compose exec app-dev php artisan app:init-roles
docker compose exec app-dev php artisan app:init-admin-user
```

Конфиг ролей: [`config/roles.php`](../../config/roles.php). Формат permission: `{action}-{entity}`.

Роли: `admin`, `moderator`, `premium-user`, `user`, `guest` (kebab-case в БД).

## User management

- `UserBanService` — ban/unban (`ban-user`, `unban-user`)
- `UserDeleteService`, `BulkDeleteService`
- Filament `UserResource` (группа **Users**)
- Scope `NotBannedScope` — скрывает banned в Filament selects

## Model

`app/Models/User.php` — Spatie `HasRoles`, accessor `full_name`, soft delete support.

## Related

- [explanation/architecture/authorization.md](../../explanation/architecture/authorization.md)
- [reference/permissions.md](../../reference/permissions.md)

## Planning

- Базовый домен; не привязан к phase-vii…ix.
