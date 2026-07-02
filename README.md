# Документация Tech Talk Tactics Backend

Pet-проект: база знаний теоретических вопросов с технических собеседований с развёрнутыми ответами. Публичный REST API (`v1`) и админ-панель Filament.

## Структура

| Раздел | Назначение |
|--------|------------|
| [docs/planning/](docs/planning/) | Пошаговые планы реализации для AI-агентов (фазы VII–IX) |
| [docs/explanation/](docs/explanation/) | Как устроена система: архитектура, модель данных, сквозные концепции |
| [docs/domains/](docs/domains/) | Логика по доменам (Question, Comment, Vote, …) |
| [docs/reference/](docs/reference/) | Справочники: маршруты API, permissions, jobs, Filament |
| [docs/guides/](docs/guides/) | Операционные инструкции: Docker, тесты, cron, очереди |

## For developers

- [Локальная разработка](docs/guides/local-development.md)
- [Тестирование](docs/guides/testing.md)
- [Cron и schedule](docs/guides/cron-and-schedule.md)
- [Очереди и Horizon](docs/guides/queues-and-horizon.md)

## For AI agents

- Конвенции кода: [`.cursor/rules/project-overview.mdc`](.cursor/rules/project-overview.mdc)
- Обзор системы: [docs/explanation/overview.md](docs/explanation/overview.md)
- Планы реализации: [docs/planning/README.md](docs/planning/README.md)

## Домен → документ

| Домен | Документ |
|-------|----------|
| Question | [docs/domains/question/](docs/domains/question/) |
| Comment | [docs/domains/comment/](docs/domains/comment/) |
| Vote | [docs/domains/vote/](docs/domains/vote/) |
| Taxonomy (Company, Position, Tag, Grade) | [docs/domains/taxonomy/](docs/domains/taxonomy/) |
| Suggestion | [docs/domains/suggestion/](docs/domains/suggestion/) |
| Notification | [docs/domains/notification/](docs/domains/notification/) |
| Auth & Users | [docs/domains/auth-and-users/](docs/domains/auth-and-users/) |
| Settings | [docs/domains/settings/](docs/domains/settings/) |

## Сквозные темы

- [Слои приложения](docs/explanation/architecture/layering.md)
- [События и побочные эффекты](docs/explanation/architecture/events-and-side-effects.md)
- [Авторизация](docs/explanation/architecture/authorization.md)
- [Денормализованные агрегаты](docs/explanation/architecture/denormalized-aggregates.md)
- [ER-модель](docs/explanation/data-model/er-overview.md)
- [FK-граф для тестов](docs/explanation/data-model/tables-by-domain.md)
