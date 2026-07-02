# Документация TTT Backend

Pet-проект: база знаний теоретических вопросов с технических собеседований с развёрнутыми ответами. Публичный REST API (`v1`) и админ-панель Filament.

## Структура

| Раздел | Назначение |
|--------|------------|
| [planning/](planning/) | Пошаговые планы реализации для AI-агентов (фазы VII–IX) |
| [explanation/](explanation/) | Как устроена система: архитектура, модель данных, сквозные концепции |
| [domains/](domains/) | Логика по доменам (Question, Comment, Vote, …) |
| [reference/](reference/) | Справочники: маршруты API, permissions, jobs, Filament |
| [guides/](guides/) | Операционные инструкции: Docker, тесты, cron, очереди |

## For developers

- [Локальная разработка](guides/local-development.md)
- [Тестирование](guides/testing.md)
- [Cron и schedule](guides/cron-and-schedule.md)
- [Очереди и Horizon](guides/queues-and-horizon.md)

## For AI agents

- Конвенции кода: [`.cursor/rules/project-overview.mdc`](../.cursor/rules/project-overview.mdc)
- Обзор системы: [explanation/overview.md](explanation/overview.md)
- Планы реализации: [planning/README.md](planning/README.md)

## Домен → документ

| Домен | Документ |
|-------|----------|
| Question | [domains/question/](domains/question/) |
| Comment | [domains/comment/](domains/comment/) |
| Vote | [domains/vote/](domains/vote/) |
| Taxonomy (Company, Position, Tag, Grade) | [domains/taxonomy/](domains/taxonomy/) |
| Suggestion | [domains/suggestion/](domains/suggestion/) |
| Notification | [domains/notification/](domains/notification/) |
| Auth & Users | [domains/auth-and-users/](domains/auth-and-users/) |
| Settings | [domains/settings/](domains/settings/) |

## Сквозные темы

- [Слои приложения](explanation/architecture/layering.md)
- [События и побочные эффекты](explanation/architecture/events-and-side-effects.md)
- [Авторизация](explanation/architecture/authorization.md)
- [Денормализованные агрегаты](explanation/architecture/denormalized-aggregates.md)
- [ER-модель](explanation/data-model/er-overview.md)
- [FK-граф для тестов](explanation/data-model/tables-by-domain.md)
