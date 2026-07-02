# Taxonomy

Справочники для фильтрации и классификации вопросов: **Company**, **Position**, **Tag**, **Grade**.

## Общие правила

- Status workflow (`App\Enums\Status`): pending / approved / rejected
- API list endpoints возвращают только **active + approved** записи (см. feature tests)
- Premium user может `suggest-*` и видеть расширенные списки companies
- Soft delete и restore — в Filament для admin/moderator

## Модели и связи с Question

| Entity | Pivot / morph | API list |
|--------|---------------|----------|
| `Tag` | `taggables` morph | `GET api/v1/tags/list` |
| `Grade` | `gradables` morph | `GET api/v1/grades/list` |
| `Company` | `company_question` | `GET api/v1/companies/list` |
| `Position` | `position_question` | `GET api/v1/positions/list` |

## Propose flow

При `POST questions/propose` tags и grades привязываются к новому вопросу. Company/position могут создаваться через resolvers внутри statistic.

## Filament

Группа навигации **Main**: `CompanyResource`, `PositionResource`, `TagResource`, `GradeResource`.

Status badges — widgets `StatusBadge`, `StatusWithReviewBadge`.

## Code map

| Domain | Service | Controller |
|--------|---------|------------|
| Company | `CompanyService` | `CompanyController` |
| Position | `PositionService` | `PositionController` |
| Tag | `TagService` | `TagController` |
| Grade | `GradeService` | `GradeController` |

Policies: `CompanyPolicy`, `PositionPolicy`, `TagPolicy`, `GradePolicy`.

Tests: `CompaniesTest`, `PositionsTest`, `TagsTest`, `GradesTest`.

## Related

- [question/list-and-filters.md](../question/list-and-filters.md)
- [suggestion/README.md](../suggestion/README.md)
- [reference/admin/filament-resources.md](../../reference/admin/filament-resources.md)

## Planning

- Базовый домен до phase-vii; suggestions — phase-viii step 06.
