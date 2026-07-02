# Планы реализации (planning)

Исполняемые пошаговые спецификации для AI-агентов. Каждый шаг содержит контекст, задачу, acceptance criteria, ограничения и (для phase-viii/ix) блок тестов.

После реализации шага устойчивое знание переносится в [domains/](../domains/) и [reference/](../reference/); здесь остаётся история и детали «как делали».

## Фазы

| Фаза | Файл | Тема | Статус | Domain-docs |
|------|------|------|--------|-------------|
| VII | [phase-vii.md](phase-vii.md) | Комментарии в Filament (list, edit, relation manager) | Реализовано | [domains/comment/](../domains/comment/), [reference/admin/filament-resources.md](../reference/admin/filament-resources.md) |
| VIII | [phase-viii.md](phase-viii.md) | Голоса, просмотры, агрегаты `met_in_real_interview_count` | Реализовано | [domains/vote/](../domains/vote/), [domains/question/views.md](../domains/question/views.md), [domains/question/aggregates.md](../domains/question/aggregates.md) |
| IX | [phase-ix.md](phase-ix.md) | Пересчёт `questions.rating` (coordinator, job, формула) | Реализовано | [domains/question/rating.md](../domains/question/rating.md) |

## Шаблон нового шага

```markdown
## Шаг NN: <краткий заголовок>

### Контекст
…

### Задача
…

### Acceptance criteria
- [ ] …

### Тесты
- [ ] …

### Ограничения
- …
```

## Документация по фазам

- **Phase VII** → [comment/README.md](../domains/comment/README.md), [moderation-and-soft-delete.md](../domains/comment/moderation-and-soft-delete.md)
- **Phase VIII** → [vote/README.md](../domains/vote/README.md), [question/views.md](../domains/question/views.md)
- **Phase IX** → [question/rating.md](../domains/question/rating.md), [question/aggregates.md](../domains/question/aggregates.md)
