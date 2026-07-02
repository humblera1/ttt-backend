# Question — агрегаты

## Purpose

Денормализованные колонки на `questions` обновляются синхронно (strong) или bulk (weak). Все участвуют в формуле rating.

## Таблица агрегатов

| Поле | Обновляет | Trigger | Rating signal |
|------|-----------|---------|---------------|
| `likes_count` | `VotableLikesCountService` via `UpdateVotableLikesCountSubscriber` | `VoteChanged` | Strong (Question votable only) |
| `comments_count` | `QuestionAggregateService` via `UpdateQuestionCommentsCountListener` | `CommentCreated`, `CommentDeleted`, `CommentRestored` | Strong |
| `met_in_real_interview_count` | `QuestionAggregateService` via `UpdateQuestionMetInRealInterviewCountListener` | `QuestionStatisticCreated` (met=true) | Strong |
| `views_count` | `QuestionRepository::incrementViewsCounts` | `FlushQuestionViewsBufferService` | Weak (flag only) |
| `rating` | `QuestionRatingService` | `RecalculateQuestionRatingJob` | — |
| `rating_needs_recalculation` | Coordinator, repository bulk, listeners | Любое изменение метрик | — |

## QuestionAggregateService

`app/Services/api/v1/Question/QuestionAggregateService.php`:

- `incrementCommentsCount` / `decrementCommentsCount`
- `incrementMetInRealInterviewCount` — **без decrement** (domain rule: met=true statistics не откатываются)

## Comments count

Listener синхронный (не queued). Restore после soft delete снова increment.

Тесты: `tests/Feature/v1/Comment/QuestionCommentsCountAndRatingTest.php`.

## Met in real interview

Increment только когда новая statistic с `met_in_real_interview = true`. Placeholder `met=false` records удаляются перед созданием met=true (см. propose flow).

## Views

Bulk SQL chunk (`config/question_views.flush_chunk_size`). Sets `rating_needs_recalculation` без immediate coordinator per id.

## Code map

| Component | Path |
|-----------|------|
| Aggregate service | `app/Services/api/v1/Question/QuestionAggregateService.php` |
| Comments listener | `app/Listeners/v1/Question/UpdateQuestionCommentsCountListener.php` |
| Met listener | `app/Listeners/v1/Question/UpdateQuestionMetInRealInterviewCountListener.php` |
| Vote subscriber | `app/Listeners/v1/Vote/UpdateVotableLikesCountSubscriber.php` |
| Repository bulk views | `app/Repositories/v1/QuestionRepository.php` |

## Related

- [rating.md](rating.md)
- [propose-and-statistics.md](propose-and-statistics.md)
- [vote/README.md](../vote/README.md)

## Planning

- Phase VIII steps 02, 06, 07 — [phase-viii.md](../../planning/phase-viii.md)
- Phase IX step 03 (comments sync) — [phase-ix.md](../../planning/phase-ix.md)
