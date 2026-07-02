# Тестирование

## Запуск

Только внутри контейнера `app-dev`:

```bash
docker compose exec app-dev php artisan test
docker compose exec app-dev php artisan test --filter=QuestionsTest
docker compose exec app-dev php artisan test tests/Feature/v1/Question/QuestionRatingCoordinatorTest.php
```

Не запускайте тесты на хосте без полного зеркала Docker-стека.

## DatabaseTransactions

Используйте `Illuminate\Foundation\Testing\DatabaseTransactions`, **не** `RefreshDatabase`:

```php
class QuestionsTest extends TestCase
{
    use DatabaseTransactions, WithUser;
}
```

Каждый тест откатывается транзакцией; seeded data из миграций/seeders сохраняется между тестами в классе.

## Auth: WithUser

```php
use App\Traits\Tests\WithUser;

protected string $permission = 'view-any-question';

$user = $this->getUser(); // User + givePermissionTo
$this->actingAs($user);
```

## Layout

| Rule | Value |
|------|-------|
| API tests | `tests/Feature/v1/{Domain}/` |
| Namespace | `Feature\v1\{Domain}` |
| Unit tests | `tests/Unit/v1/{Domain}/` для чистой логики |
| Base class | `Tests\TestCase` |
| Method names | `test_{actor}_{condition}_{expected}` |
| API calls | `getJson`, `postJson`, `route('api.v1.…')` |
| Assertions | `assertJsonPath('data.field', …)` — payload under `data` |

## ClearsTestTables

Когда тесту нужен пустой срез таблиц поверх seeded data — trait `App\Traits\Tests\ClearsTestTables`:

| Method | When |
|--------|------|
| `clearQuestionsAndDependencies()` | Question tests, rating tests |
| `clearCompaniesAndDependencies()` | `CompaniesTest` |
| `clearPositionsAndDependencies()` | `PositionsTest` |
| `clearTagsAndDependencies()` | `TagsTest` |
| `clearQuestionDomainTables()` | `QuestionsProposeTest`, propose flows |

Порядок FK: [explanation/data-model/tables-by-domain.md](../explanation/data-model/tables-by-domain.md).

## Queue in tests

`QUEUE_CONNECTION=sync` в `phpunit.xml`. Для проверки dispatch:

```php
Bus::fake();
Event::fake([VoteChanged::class]);
```

Redis-dependent tests (views): skip if Redis unavailable (`FlushQuestionViewsTest` pattern).

## Related

- [`.cursor/rules/tests.mdc`](../../.cursor/rules/tests.mdc)
- [local-development.md](local-development.md)
