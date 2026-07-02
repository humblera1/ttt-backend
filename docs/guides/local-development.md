# Локальная разработка

## Docker Compose

Стек описан в [`docker-compose.yaml`](../../docker-compose.yaml):

| Service | Назначение |
|---------|------------|
| `app-dev` | PHP-FPM приложение |
| `nginx-dev` | Web server (Traefik host `ttt-api.dev.localhost`) |
| `mysql-dev` | MySQL 9.3 |
| `redis-dev` | Redis 8.4 |
| `horizon-dev` | `php artisan horizon` |
| `scheduler-dev` | `schedule:run` каждые 60 секунд |

## Первый запуск

Из корня проекта:

```bash
docker compose exec app-dev sh -lc 'chown -R www-data:www-data storage bootstrap/cache && chmod -R ug+rwX storage bootstrap/cache'
docker compose exec app-dev php artisan key:generate --force
docker compose exec app-dev php artisan migrate
docker compose exec app-dev php artisan app:init-roles
docker compose exec app-dev php artisan app:init-admin-user
docker compose exec app-dev php artisan settings:import
```

## Artisan в контейнере

Все команды Laravel — через `app-dev`:

```bash
docker compose exec app-dev php artisan <command>
```

**Не запускайте** `php artisan` на хосте без зеркала стека: `DB_HOST=mysql-dev` не резолвится с хоста.

## Инспекция БД

MySQL доступен из `app-dev`:

```bash
docker compose exec app-dev php artisan db:show
docker compose exec app-dev php artisan db:table questions
```

Подробнее: [`.cursor/skills/read-database/SKILL.md`](../../.cursor/skills/read-database/SKILL.md).

## Filament admin

URL зависит от Traefik/nginx config (по умолчанию host из `APP_HOSTNAME`). Login — пользователь после `app:init-admin-user`.

## Related

- [testing.md](testing.md)
- [queues-and-horizon.md](queues-and-horizon.md)
- [docs/README.md](../README.md)
