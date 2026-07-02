# API Reference

REST API версии **v1**.

## Base URL

```
/api/v1/
```

Имена маршрутов: `api.v1.{resource}.{action}` (полный prefix `api.` из `routes/api.php`).

## Authentication

Защищённые endpoints: header `Authorization: Bearer {token}` (Laravel Sanctum).

Публичные endpoints помечены в [routes.md](routes.md) как «—».

## Response envelope

JSON resources используют стандартный Laravel wrap:

```json
{
  "data": { ... }
}
```

Коллекции:

```json
{
  "data": [ ... ],
  "links": { ... },
  "meta": { ... }
}
```

Не устанавливать `public static $wrap = null` на project resources.

## Pagination

List endpoints используют Laravel paginator. Per-page часто из `setting('{section}.per_page')` — см. [domains/settings/README.md](../../domains/settings/README.md).

## Errors

- `403` — policy / Form Request `authorize()` failed
- `422` — validation (`assertUnprocessable`)
- `429` — throttle (например `question-view`)

## Related

- [routes.md](routes.md) — полная таблица маршрутов
- [permissions.md](../permissions.md)
- [domains/](../../domains/)
