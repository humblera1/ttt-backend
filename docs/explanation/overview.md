# Обзор системы

## Продукт

TTT Backend — серверная часть базы знаний теоретических вопросов с технических собеседований. Пользователи просматривают и предлагают вопросы, оставляют комментарии, голосуют, сообщают о встречах на реальных собеседованиях. Модераторы и админы работают через Filament (`/admin`).

Pet-проект в активной разработке; API версионирован (`v1`).

## Стек

| Компонент | Технология |
|-----------|------------|
| Framework | Laravel 12, PHP 8.4 |
| Admin | Filament 3 |
| API auth | Laravel Sanctum |
| Roles / permissions | Spatie Permission |
| Cache / queues | Redis, Laravel Horizon |
| DB | MySQL |

## Основные сущности

| Сущность | Назначение |
|----------|------------|
| `Question` | Вопрос: title, answer, status, рейтинг, денормализованные метрики |
| `Comment` | Комментарии к вопросу (дерево через `parent_id`), soft delete |
| `Vote` | Полиморфный лайк/дизлайк (`value` ±1) на Question или Comment |
| `Statistic` | Опрос пользователя: встречался ли вопрос на собеседовании, company/position |
| `QuestionCompanySuggestion` / `QuestionPositionSuggestion` | Предложения связать вопрос с компанией/должностью |
| `Company`, `Position`, `Tag`, `Grade` | Таксономия и фильтрация |
| `NotificationCategory`, `NotificationType`, `UserNotification` | In-app уведомления |
| `User` | Пользователь, роли, ban |

## API

- Префикс: `/api/v1/`
- Имена маршрутов: `api.v1.*` (например `route('api.v1.questions.list')`)
- Ответы обёрнуты в `{ "data": … }` (стандартный Laravel Resource wrap)

Подробнее: [reference/api/README.md](../reference/api/README.md).

## Слои приложения

```mermaid
flowchart TB
  Client[Client / Filament]
  Controller[Http Controllers v1]
  Request[Form Requests + DTOs]
  Service[Services api/v1]
  Repository[Repositories v1]
  Model[Eloquent Models]
  Client --> Controller
  Controller --> Request
  Request --> Service
  Service --> Repository
  Repository --> Model
  Service --> Events[Domain Events]
  Events --> Listeners[Listeners v1]
```

Детали: [architecture/layering.md](architecture/layering.md).

## Центральная модель Question

```mermaid
erDiagram
  Question ||--o{ Comment : has
  Question ||--o{ Statistic : has
  Question ||--o{ Vote : "morphMany votable"
  Question }o--o{ Tag : taggables
  Question }o--o{ Grade : gradables
  Question }o--o{ Company : company_question
  Question }o--o{ Position : position_question
  Question ||--o{ QuestionCompanySuggestion : has
  Question ||--o{ QuestionPositionSuggestion : has
  Comment ||--o{ Vote : "morphMany votable"
  User ||--o{ Comment : writes
  User ||--o{ Vote : casts
  User ||--o{ Statistic : submits
```

Полная ER-модель: [data-model/er-overview.md](data-model/er-overview.md).

## Связанные разделы

- [Архитектура](architecture/layering.md)
- [Денормализованные агрегаты](architecture/denormalized-aggregates.md)
- [Домены](../domains/)
- [Справочники](../reference/)
