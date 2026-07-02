---
name: read-database
description: >-
  Reads MySQL schema and data via Laravel db:* commands inside the app-dev Docker
  container. Use when inspecting database state, tables, columns, or live data; or when
  direct DB access fails because mysql-dev is not reachable from the host.
---

# Read Database

MySQL runs in the `mysql-dev` container. From the host, `DB_HOST=mysql-dev` in `.env` does not resolve — run Artisan **inside `app-dev`**.

## Workflow

From the project root:

```bash
docker compose exec app-dev php artisan db:show
docker compose exec app-dev php artisan db:table questions
```

Or open a shell in the container:

```bash
docker compose exec app-dev bash
php artisan db:show
php artisan db:table questions
```

## Available commands

```bash
php artisan db:show      # database overview (tables, size, connection)
php artisan db:table     # table structure, indexes, sample rows
php artisan db:monitor   # connection count over time
php artisan db:seed      # seed records — only when user explicitly asks
php artisan db:wipe      # drop all tables — only when user explicitly asks
```

For schema inspection, prefer `db:show` and `db:table`. Do not run `db:seed` or `db:wipe` unless the user explicitly requests it.

## Examples

```bash
docker compose exec app-dev php artisan db:show
docker compose exec app-dev php artisan db:table questions
docker compose exec app-dev php artisan db:table statistics
docker compose exec app-dev php artisan db:monitor
```

## When containers are not running

Start the stack, then retry:

```bash
docker compose up -d mysql-dev app-dev
```

If containers cannot be started, read structure from `database/migrations/` and `app/Models/` — live data will be unavailable.
