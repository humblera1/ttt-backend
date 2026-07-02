# Денормализованные агрегаты

На модели `Question` хранятся счётчики и производный рейтинг, чтобы не выполнять тяжёлые `COUNT`/`SUM` по сырым таблицам при каждом пересчёте.

## Поля на `questions`

| Поле | Источник данных | Обновление |
|------|-----------------|------------|
| `views_count` | Redis buffer → bulk flush | Weak signal для rating |
| `likes_count` | Сумма `votes.value` (+1/−1) | Strong signal |
| `comments_count` | Активные комментарии (create/delete/restore) | Strong signal |
| `met_in_real_interview_count` | `statistics` с `met_in_real_interview = true` | Strong signal |
| `rating` | Формула из агрегатов | Job `RecalculateQuestionRatingJob` |
| `rating_needs_recalculation` | Флаг «нужен пересчёт» | Любое изменение агрегата или bulk flush |

## Strong vs weak signals

| Канал | Примеры | Агрегат в БД | Запрос пересчёта rating |
|-------|---------|--------------|-------------------------|
| **Strong** | голос за Question, statistic `met=true`, comment create/delete/restore | сразу | `QuestionRatingCoordinator::requestRecalc` |
| **Weak** | flush просмотров из Redis | bulk `views_count` + флаг | только `rating_needs_recalculation`; periodic `rating:process-pending` |

Голос за **Comment** обновляет `comments.likes_count`, но **не** триггерит пересчёт rating вопроса.

## Формула rating (MVP)

`QuestionRatingCalculator` использует только колонки Question и weights из `setting('rating_weights.*')`:

```
rating = round(
  likes_count * votes_weight
  + met_in_real_interview_count * met_weight
  + ln(views_count + 1) * views_weight
  + ln(comments_count + 1) * comments_weight
)
```

Отрицательный rating допустим. **Старение по `published_at`** — вне scope MVP.

## Зачем флаг `rating_needs_recalculation`

Сотни просмотров или debounced job не должны порождать сотни тяжёлых пересчётов. Coordinator ставит флаг и debounce через `Cache::add`; safety-net `rating:process-pending` подхватывает накопившиеся флаги.

## Связанные разделы

- [domains/question/rating.md](../../domains/question/rating.md)
- [domains/question/aggregates.md](../../domains/question/aggregates.md)
- [planning/phase-ix.md](../../planning/phase-ix.md)
