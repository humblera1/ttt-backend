# ER-обзор

Диаграмма основных связей. Детальный FK-граф для очистки тестов — [tables-by-domain.md](tables-by-domain.md).

```mermaid
erDiagram
  users ||--o{ questions : "author optional"
  users ||--o{ comments : writes
  users ||--o{ votes : casts
  users ||--o{ statistics : submits
  users ||--o{ user_notifications : receives

  questions ||--o{ comments : has
  questions ||--o{ statistics : has
  questions ||--o{ votes : "votable morph"
  questions ||--o{ question_company_suggestions : has
  questions ||--o{ question_position_suggestions : has
  questions }o--o| questions : "duplicate_of_id"
  questions }o--o{ tags : taggables
  questions }o--o{ grades : gradables
  questions }o--o{ companies : company_question
  questions }o--o{ positions : position_question

  comments ||--o{ comments : "parent_id tree"
  comments ||--o{ votes : "votable morph"

  tags ||--o{ taggables : ""
  grades ||--o{ gradables : ""
  companies ||--o{ statistics : optional
  positions ||--o{ statistics : optional
  companies ||--o{ question_company_suggestions : ""
  positions ||--o{ question_position_suggestions : ""

  notification_categories ||--o{ notification_types : has
  notification_types ||--o{ user_notifications : ""
```

## Self-reference: duplicates

`questions.duplicate_of_id` → `questions.id`, `nullOnDelete`. Дубликат указывает на канонический вопрос.

## Soft deletes

| Таблица | Механизм |
|---------|----------|
| `questions` | `SoftDeletes` |
| `comments` | `SoftDeletes` + `deleted_by_id`, `deleted_reason_code` |
| `companies`, `positions`, `tags` | status + soft delete где применимо в домене |

Статус «удалён» у комментария в UI — по `deleted_at`, не отдельное поле `is_deleted`.

## Полиморфные связи

| Таблица | Morph name | Targets |
|---------|------------|---------|
| `votes` | `votable` | `Question`, `Comment` (будущий `Answer`) |
| `taggables` | `taggables` | `Question`, … |
| `gradables` | `gradables` | `Question`, … |

## Pivot-таблицы

- `company_question` — связь вопрос ↔ компания (cascade on delete)
- `position_question` — связь вопрос ↔ должность (cascade on delete)

## Связанные разделы

- [tables-by-domain.md](tables-by-domain.md)
- [overview.md](../overview.md)
