# Settings

Runtime-настройки приложения в БД с fallback на [`config/settings.php`](../../config/settings.php).

## Структура config

Секции — ключи enum `App\Enums\Settings\Section` (не raw strings):

| Section | Примеры keys |
|---------|--------------|
| `Question` | `per_page`, `rate-limit-seconds` |
| `Tag`, `Company` | `first_page_per_page`, `per_page` |
| `Statistics` | `max_records_per_user_question`, `*_suggestion_evidence_threshold` |
| `Notifications` | `read_ttl_days`, `unread_ttl_days`, `max_per_user` |
| `Comments` | `per_page` |
| `QuestionViews` | `dedup_ttl_seconds`, `flush_lock_seconds`, `rate_limit_per_minute` |
| `RatingWeights` | `votes`, `met`, `views`, `comments` (float) |

Labels в config — на русском для Filament; поле `description` не добавлять (конвенция проекта).

## Runtime access

```php
setting('question.per_page', 15);
setting('rating_weights.votes', 1.0);
```

`SettingsService` + container binding в `SettingsServiceProvider`.

## Import

```bash
docker compose exec app-dev php artisan settings:import
```

Command: `app/Console/Commands/Settings/ImportSettings.php`.

Синхронизирует defaults из config в таблицу `settings`.

## Filament

Admin с `view-any-setting` — просмотр/редактирование через админку (если ресурс подключён).

## Code map

| Component | Path |
|-----------|------|
| Config | `config/settings.php` |
| Enum | `app/Enums/Settings/Section.php` |
| Model | `app/Models/Setting.php` |
| Service | `app/Services/api/v1/SettingsService.php` |
| Provider | `app/Providers/v1/SettingsServiceProvider.php` |
| Rules | [`.cursor/rules/settings.mdc`](../../.cursor/rules/settings.mdc) |

## Related

- [question/rating.md](../question/rating.md) — rating weights
- [question/views.md](../question/views.md) — view settings
- [notification/README.md](../notification/README.md)

## Planning

- `RatingWeights` — [phase-ix.md — Шаг 02](../../planning/phase-ix.md)
