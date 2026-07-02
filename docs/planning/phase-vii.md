# Этап VII

> **Документация (реализовано):** [domains/comment/](../domains/comment/), [reference/admin/filament-resources.md](../reference/admin/filament-resources.md)

Данный этап завершает разработку основных сущностей проекта: после завершения этапа в систему будут добавлены голоса, просмотры, уведомления, комментарии, а также логика расчета рейтинга вопросов на основе некоторых из перечисленных метрик.

Пошаговый план реализации Этапа VII. Каждая секция ниже — отдельный шаг для ИИ-агента: контекст, задача, acceptance criteria, ограничения.

**Шаблон для новых шагов:**

```markdown
## Шаг NN: <краткий заголовок>

### Контекст
…

### Задача
…

### Acceptance criteria
- [ ] …

### Ограничения
- …
```

---

## Шаг 01: Добавить список комментариев в админ-панель

### Контекст

Шаг 01 (часть Этапа VII по комментариям в Filament). Каркас [`CommentResource`](../../app/Filament/Resources/CommentResource.php) и страница [`ListComments`](../../app/Filament/Resources/CommentResource/Pages/ListComments.php) уже созданы, но таблица и фильтры пустые. Модель [`Comment`](../../app/Models/Comment.php) использует Laravel **SoftDeletes** (`deleted_at`); в UI и фильтрах статус «удалён» — по `deleted_at`. Ресурс уже вызывает `withTrashed()` в `getEloquentQuery()`.

### Задача

1. **Навигация и запрос**
   - Задать метки ресурса через `__()` (`navigationLabel`, `modelLabel`, `pluralModelLabel`) — группа `Main`, иконка уже задана.
   - В `getEloquentQuery()` добавить eager load: `->with(['question', 'user'])` (сохранить `withTrashed()`).

2. **Колонки таблицы** в `CommentResource::table()`:
   - **Вопрос:** `TextColumn::make('question.title')` — searchable, limit; ссылка на редактирование вопроса: `QuestionResource::getUrl('edit', ['record' => $record->question_id])`.
   - **Пользователь:** отобразить имя и email — например `user.full_name` (accessor в [`User`](../../app/Models/User.php)) и/или `user.username` + `user.email` в одной колонке (`description` / `formatStateUsing`) по образцу [`UserResource`](../../app/Filament/Resources/UserResource.php).
   - **Текст:** `body` с `->limit(100)` (допустимо 80–120).
   - **Статус:** badge по `deleted_at` — «Активен» (`deleted_at` null, цвет success/gray) / «Удалён» (`deleted_at` not null, цвет danger) через `formatStateUsing` + `badge()`; **не** вводить колонку `is_deleted`.
   - **Даты:** `created_at` — `sortable()`, `since()`, `dateTooltip()`; `updated_at` — опционально, тем же стилем что в `QuestionResource`.
   - **Сортировка по умолчанию:** `->defaultSort('created_at', 'desc')`.

3. **Фильтры:**
   - **Вопрос:** `SelectFilter::make('question_id')->relationship('question', 'title')->searchable()->preload()`.
   - **Пользователь:** `SelectFilter` по `user` — searchable; при необходимости снять `NotBannedScope` в `modifyQueryUsing` (как в `UserResource::getEloquentQuery`), если автор комментария не попадает в фильтр.
   - **Статус удаления:** переиспользовать [`TrashedFilter::make()`](../../app/Filament/Filters/Trash/TrashedFilter.php) **или** `TernaryFilter` по `deleted_at` с подписями «Активен» / «Удалён» / «Все» (соответствие ТЗ важнее дефолтных label `TrashedFilter`).
   - **Интервал дат:** `Filter::make('created_at')` с двумя `DatePicker` (`from`, `until`); query: `whereDate >= from`, `whereDate <= until` (применять только заполненные поля).

4. **Действия строки (минимум для списка):**
   - Оставить `EditAction` (страница edit уже есть).
   - Не реализовывать на этом шаге форму create/edit, relation managers, bulk-модерацию — только список.

5. **Проверка вручную:** зайти в `/admin` под пользователем с `view-any-comment`, убедиться что пункт меню «Комментарии» ведёт на список.

### Acceptance criteria

- [ ] В меню Filament (группа Main) отображается ресурс комментариев; список открывается без ошибок для пользователя с `view-any-comment`.
- [ ] В таблице видны колонки: вопрос (title + ссылка на edit вопроса), пользователь (имя и/или email), усечённый `body`, badge статуса «Активен»/«Удалён», `created_at` (и опционально `updated_at`).
- [ ] Удалённые комментарии (`deleted_at` не null) отображаются в списке с статусом «Удалён».
- [ ] Работают фильтры: по вопросу (searchable select), по пользователю, по статусу удаления, по диапазону `created_at`.
- [ ] Нет заметных N+1 на списке (eager load `question`, `user`).
- [ ] Подписи UI через `__()` где уместно; PHPDoc/комментарии в коде — на английском ([`.cursor/rules/language.mdc`](../../.cursor/rules/language.mdc)).

### Ограничения

- Следовать [`.cursor/rules/filament.mdc`](../../.cursor/rules/filament.mdc) и [`.cursor/rules/project-overview.mdc`](../../.cursor/rules/project-overview.mdc).
- **Образец:** [`app/Filament/Resources/QuestionResource.php`](../../app/Filament/Resources/QuestionResource.php) (таблица, фильтры, даты).
- **Не трогать:** API-слой (`app/Http/Controllers/v1/CommentController.php`, `app/Services/api/v1/Comment/*`, `app/Repositories/v1/Comment/*`), миграции, политики (кроме необходимости — не менять), тесты (если пользователь не попросит отдельно).
- **Не добавлять** поле `is_deleted` в БД; статус только через `deleted_at` / SoftDeletes.
- **Не выносить** в отдельные классы колонки/фильтры, если они не переиспользуются (YAGNI для шага 1).
- Бизнес-логику удаления/восстановления не дублировать в resource — только отображение и фильтрация.

---

## Шаг 02: Добавить страницу редактирования комментария

### Контекст

После шага 01 в админке есть список комментариев с переходом на edit. Страница [`EditComment`](../../app/Filament/Resources/CommentResource/Pages/EditComment.php) существует, но форма [`CommentResource::form()`](../../app/Filament/Resources/CommentResource.php) пуста; в header стоит стандартный `DeleteAction` без причины удаления. Модель [`Comment`](../../app/Models/Comment.php) поддерживает ветвление (`parent_id`, связи `parent`, `question`, `user`) и метаданные удаления: `deleted_reason_code` ([`ReasonForDeletion`](../../app/Enums/Comment/ReasonForDeletion.php)), `deleted_reason_comment`, `deleted_by_id`. API удаляет только от имени автора через [`CommentService::deleteByUser()`](../../app/Services/api/v1/Comment/CommentService.php) с кодом `user_removed` — для модерации в админке нужен отдельный метод сервиса (не дублировать логику в Filament). Восстановление уже есть в [`CommentService::restore()`](../../app/Services/api/v1/Comment/CommentService.php); для безвозвратного удаления — добавить `forceDelete()` в сервис (через repository). Политика [`CommentPolicy`](../../app/Policies/CommentPolicy.php): `restore` (`restore-any-comment` / `restore-own-comment`), `forceDelete` (`force-delete-any-comment`).

### Задача

1. **Загрузка записи на edit**
   - В `getEloquentQuery()` или на странице edit обеспечить eager load: `question`, `user`, `parent.user` (для контекста родителя).
   - Для удалённых записей страница должна открываться (ресурс уже с `withTrashed()`).

2. **Древовидный контекст** (над формой или отдельной `Section` / infolist-блок, только чтение):
   - Если `parent_id` задан: текст вида «Ответ на комментарий #`{id}` / автора `{имя или username}`» — ссылка на [`CommentResource::getUrl('edit', ['record' => $parent])`](../../app/Filament/Resources/CommentResource.php).
   - Всегда: ссылка на вопрос — заголовок `question.title` → [`QuestionResource::getUrl('edit', ...)`](../../app/Filament/Resources/QuestionResource.php).
   - Если `parent_id` null: достаточно контекста вопроса (корневой комментарий).

3. **Форма редактирования** в `CommentResource::form()` / `EditComment`:
   - Поле `body` (`Textarea`, required, max length по валидации API/модели).
   - Read-only при необходимости: автор (`user`), даты `created_at` / `updated_at`, статус удаления (badge по `deleted_at`).
   - Проверка политики `update` / permission `edit-any-comment`.

4. **Удаление с причиной** (заменить стандартный `DeleteAction` на кастомный header action):
   - Модальное окно с полями:
     - `deleted_reason_code` — `Select` по кейсам [`ReasonForDeletion`](../../app/Enums/Comment/ReasonForDeletion.php), **кроме** `UserRemoved` (модераторские причины: spam, abuse, offtopic, duplicate).
     - `deleted_reason_comment` — `Textarea`, опционально или required (зафиксировать в реализации; минимум — optional с `maxLength`).
   - Пример использования модального окна в кастомном действии: app/Filament/Actions/Question/RejectQuestionAction.php.
   - Под кейс удаления комментария также необходимо реализовать полноценный action-компонент по примеру RejectQuestionAction.
   - Добавить в `CommentService` метод, например `deleteWithReason(Comment $comment, User $moderator, ReasonForDeletion $code, ?string $commentText): void` — soft delete, заполнение `deleted_at`, `deleted_reason_code`, `deleted_reason_comment`, `deleted_by_id`; persist через repository.
   - Возможно, оптимальнее будет реализовать DTO для использования в `deleteWithReason`, разместить DTO в app/DTOs/v1/Comment по примеру app/DTOs/v1/Comment/CommentStoreDTO.php
   - В action: `auth()->user()->can('delete', $record)` (или `delete-any-comment`); после успеха — редирект на список + notification.
   - Скрывать action, если комментарий уже удалён (`trashed()`).

5. **Восстановление (restore)** — header action на `EditComment`:
   - Кастомный action — видим только для `trashed()` записей и при `auth()->user()->can('restore', $record)` ([`CommentPolicy::restore`](../../app/Policies/CommentPolicy.php)).
   - Вызов [`CommentService::restore()`](../../app/Services/api/v1/Comment/CommentService.php) (очистить `deleted_at`, `deleted_reason_code`, `deleted_reason_comment`, `deleted_by_id`); не вызывать `$record->restore()` напрямую из Filament.
   - После успеха — обновить форму / notification; action модераторского удаления снова доступен.

6. **Безвозвратное удаление (force delete)** — header action:
   - Использовать [`ForceDeleteAction`](../../app/Filament/Actions/Delete/ForceDeleteAction.php) с переопределением `action`, либо кастомный action по образцу других edit-страниц ([`EditQuestion`](../../app/Filament/Resources/QuestionResource/Pages/EditQuestion.php)).
   - Видим при `trashed()` и `can('forceDelete', $record)`.
   - Добавить `CommentService::forceDelete(Comment $comment): void` — persist через `CommentRepository` / `bulkForceDelete` (один id); не оставлять прямой `$record->forceDelete()` в UI-слое.
   - После успеха — редирект на список комментариев + notification.

7. **Страница `EditComment`**
   - Убрать/не использовать голый `Actions\DeleteAction::make()`.
   - Header actions: модераторское удаление с причиной, `RestoreAction`, `ForceDeleteAction` (с корректной видимостью по состоянию записи).
   - При необходимости: `getTitle()` / подзаголовок с ID комментария.
   - Для удалённой записи: `body` и прочие редактируемые поля — disabled или read-only (редактирование только активных комментариев).

8. **Проверка вручную:** открыть корневой и вложенный комментарий; контекст и ссылки корректны; удаление с причиной сохраняет поля в БД; restore возвращает комментарий в активное состояние; force delete удаляет запись из БД; повторное удаление недоступно для уже удалённого.

### Acceptance criteria

- [ ] Страница edit отображает древовидный контекст: для ответа — «Ответ на комментарий #ID / автора X» со ссылкой на edit родителя; ссылка на edit вопроса всегда.
- [ ] Редактируется `body`; сохранение идёт через `CommentService::updateBody`, с учётом политики.
- [ ] Удаление из админки — через модалку с выбором `deleted_reason_code` (модераторские значения) и полем `deleted_reason_comment`; данные пишутся в БД, `deleted_by_id` — текущий админ.
- [ ] Стандартный Filament soft delete без причины не используется для комментариев на этой странице.
- [ ] Удалённый комментарий: форма/read-only контекст доступны при `withTrashed`; action модераторского удаления скрыт; `body` не редактируется.
- [ ] **Restore:** для soft-deleted комментария доступен restore при наличии права; после restore метаданные удаления очищены, статус «Активен».
- [ ] **Force delete:** для soft-deleted комментария доступно безвозвратное удаление при `force-delete-any-comment`; запись исчезает из БД, редирект на список.
- [ ] Подписи UI через `__()`; PHPDoc и комментарии в коде — на английском.

### Ограничения

- Следовать [`.cursor/rules/filament.mdc`](../../.cursor/rules/filament.mdc), [`.cursor/rules/laravel-architecture.mdc`](../../.cursor/rules/laravel-architecture.mdc).
- **Образцы:** [`EditUser`](../../app/Filament/Resources/UserResource/Pages/EditUser.php) (форма, header actions), [`EditUserNotification`](../../app/Filament/Resources/UserNotificationResource/Pages/EditUserNotification.php) (restore + force delete), [`EditQuestion`](../../app/Filament/Resources/QuestionResource/Pages/EditQuestion.php) (`RestoreAction`, [`ForceDeleteAction`](../../app/Filament/Actions/Delete/ForceDeleteAction.php)), [`ViewUserNotification`](../../app/Filament/Resources/UserNotificationResource/Pages/ViewUserNotification.php) (read-only контекст со ссылками).
- Сложные header actions (удаление с причиной) вынести в `app/Filament/Actions/Comment/` (по аналогии с `Actions/Question/`).
- **Не трогать:** API routes/controllers (логика — в `CommentService` + Filament), миграции, `CommentPolicy` (если текущих permissions достаточно).
- **Не реализовывать** на этом шаге: create из админки, relation manager на вопросе, bulk restore / bulk force delete на списке.
- Не вызывать `deleteByUser()` для модераторского удаления (другой `ReasonForDeletion`).
- Не использовать стандартный Filament `DeleteAction` / `RestoreAction` / `ForceDeleteAction` без делегирования в `CommentService`.

---

## Шаг 03: Дополнить ресурс вопроса комментариями

### Контекст

Шаги 01–02 дают [`CommentResource`](../../app/Filament/Resources/CommentResource.php) (список + edit) и методы [`CommentService`](../../app/Services/api/v1/Comment/CommentService.php) для удаления с причиной и restore. На странице edit вопроса модератору нужен обзор комментариев без ухода со страницы. Связь [`Question::comments()`](../../app/Models/Question.php) уже есть; relation managers регистрируются в [`QuestionResource::getRelations()`](../../app/Filament/Resources/QuestionResource.php) (сейчас только [`StatisticsRelationManager`](../../app/Filament/Resources/QuestionResource/RelationManagers/StatisticsRelationManager.php)). В БД статус задаётся через `deleted_at` / SoftDeletes, как в шагах 01–02.

### Задача

1. **RelationManager**
   - Создать [`CommentsRelationManager`](../../app/Filament/Resources/QuestionResource/RelationManagers/CommentsRelationManager.php): `protected static string $relationship = 'comments'`.
   - `protected static ?string $title = __('Comments')` — вкладка «Комментарии».
   - `canViewForRecord`: `auth()->user()->can('viewAny', Comment::class)` ([`CommentPolicy`](../../app/Policies/CommentPolicy.php)).
   - Зарегистрировать класс в `QuestionResource::getRelations()` рядом с `StatisticsRelationManager`.

2. **Запрос таблицы**
   - В relation manager: `modifyQueryUsing` с `withTrashed()` и `with('user')` — показывать активные и удалённые комментарии вопроса.
   - `defaultSort('created_at', 'desc')`.

3. **Колонки**
   - **Пользователь:** `app/Filament/Columns/User/UserColumn.php`.
   - **body:** `limit(100)` (допустимо 80–120).
   - **Статус:** badge по `deleted_at` — «Активен» / «Удалён».
   - **created_at:** `sortable()`, `since()`, `dateTooltip()`.

4. **Быстрая модерация «is_deleted»**
   - Интерактивный элемент в таблице (`ToggleColumn` или row `Action`), переключающий soft-deleted состояние:
     - **Пометить удалённым:** [`CommentService::deleteWithReason()`](../../app/Services/api/v1/Comment/CommentService.php) с модалкой выбора причины из шага 02 (`app/Filament/Actions/Comment/DeleteCommentWithReasonAction.php`).
     - **Снять пометку:** `CommentService::restore()`.
   - Видимость/disabled по `CommentPolicy::delete` / `restore` для записи.

5. **Переход в полный ресурс**
   - `Tables\Actions\EditAction::make()->url(fn (Comment $record) => CommentResource::getUrl('edit', ['record' => $record]))` — в т.ч. для soft-deleted (ресурс с `withTrashed()`).

6. **Опционально**
   - Фильтр по статусу удаления ([`TrashedFilter`](../../app/Filament/Filters/Trash/TrashedFilter.php)).
   - `CreateAction` — не включать.

7. **Проверка вручную:** на edit вопроса открыть вкладку «Комментарии»; переключить статус удаления; перейти в edit комментария.

### Acceptance criteria

- [ ] На edit вопроса вкладка «Комментарии» видна при `view-any-comment`.
- [ ] Таблица: пользователь, усечённый `body`, статус удаления (badge), `created_at`; отображаются soft-deleted записи.
- [ ] Быстрое переключение удалён/активен работает через `CommentService`, с проверкой policy.
- [ ] `EditAction` ведёт на edit в `CommentResource` (в т.ч. для удалённого комментария).
- [ ] Подписи UI через `__()`; PHPDoc и комментарии в коде — на английском.

### Ограничения

- Следовать [`.cursor/rules/filament.mdc`](../../.cursor/rules/filament.mdc), [`.cursor/rules/laravel-architecture.mdc`](../../.cursor/rules/laravel-architecture.mdc).
- **Образец:** [`StatisticsRelationManager`](../../app/Filament/Resources/QuestionResource/RelationManagers/StatisticsRelationManager.php); колонки комментариев — шаг 01 в этом файле.
- **Не трогать:** API, миграции, модель `Question` (связь `comments` уже есть).
- **Не реализовывать в RM:** create комментария, force delete, bulk actions.
- Не вызывать `$record->delete()` / `$record->restore()` напрямую — только `CommentService`.
- Поле `is_deleted` в БД не добавлять.
