# Question

Центральная сущность системы: теоретический вопрос с ответом, статусом модерации, таксономией и денормализованными метриками.

## Модель

Файл: `app/Models/Question.php`

| Поле / аспект | Описание |
|---------------|----------|
| `title`, `answer` | Текст вопроса и ответа |
| `status` | Workflow: `pending`, `approved`, `rejected` (`App\Enums\Status`) |
| `is_premium` | Premium-контент; виден с `view-premium-question` |
| `is_anonymous` | Анонимное предложение |
| `duplicate_of_id` | Self-FK на канонический вопрос |
| `rejection_reason`, `rejection_comment` | При отклонении |
| `published_at` | Дата публикации approved-вопроса |
| `views_count`, `likes_count`, `comments_count`, `met_in_real_interview_count` | Денормализованные агрегаты |
| `rating`, `rating_needs_recalculation` | Рейтинг и флаг пересчёта |
| Soft delete | `deleted_at` |

## Связи

- `tags`, `grades` — morph many-to-many
- `companies`, `positions` — pivot `company_question`, `position_question`
- `comments`, `statistics`, `votes` — has many / morphMany
- `user` — автор (optional для imported)

## Подразделы

| Тема | Документ |
|------|----------|
| Список и фильтры API | [list-and-filters.md](list-and-filters.md) |
| Propose и statistics | [propose-and-statistics.md](propose-and-statistics.md) |
| Просмотры (Redis) | [views.md](views.md) |
| Рейтинг | [rating.md](rating.md) |
| Агрегаты | [aggregates.md](aggregates.md) |

## Code map

| Layer | Location |
|-------|----------|
| Model | `app/Models/Question.php` |
| Service | `app/Services/api/v1/QuestionService.php`, `QuestionProposalService.php` |
| Repository | `app/Repositories/v1/QuestionRepository.php` |
| Controller | `app/Http/Controllers/v1/QuestionController.php` |
| Policy | `app/Policies/QuestionPolicy.php` |
| Filament | `app/Filament/Resources/QuestionResource.php` |

## Planning

- Phase VIII: views, aggregates — [phase-viii.md](../../planning/phase-viii.md)
- Phase IX: rating — [phase-ix.md](../../planning/phase-ix.md)
