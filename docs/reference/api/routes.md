# API v1 Routes

Полный список маршрутов из [`routes/api/v1/`](../../routes/api/v1/). Prefix URL: `/api/v1`.

## auth.php

| Method | Path | Route name | Auth | Controller action |
|--------|------|------------|------|-------------------|
| POST | `/auth/login` | `api.v1.auth.login` | — | `AuthController@login` |
| POST | `/auth/me` | `api.v1.auth.me` | — | `AuthController@me` |
| POST | `/auth/register` | `api.v1.auth.register` | sanctum | `AuthController@register` |
| POST | `/auth/change-password` | `api.v1.auth.change-password` | sanctum | `AuthController@changePassword` |
| POST | `/auth/forgot-password` | `api.v1.auth.forgot-password` | sanctum | `AuthController@forgotPassword` |
| POST | `/auth/reset-password` | `api.v1.auth.reset-password` | sanctum | `AuthController@resetPassword` |

## questions.php

| Method | Path | Route name | Auth | Controller action |
|--------|------|------------|------|-------------------|
| GET | `/questions/list` | `api.v1.questions.list` | —* | `QuestionController@list` |
| POST | `/questions/propose` | `api.v1.questions.propose` | sanctum | `QuestionController@propose` |
| POST | `/questions/{question}/feedback` | `api.v1.questions.feedback.submit` | sanctum | `QuestionController@submitFeedback` |
| GET | `/questions/{question}/comments` | `api.v1.questions.comments.list` | —* | `CommentController@list` |
| POST | `/questions/{question}/comments` | `api.v1.questions.comments.store` | sanctum | `CommentController@store` |
| PUT | `/questions/{question}/vote` | `api.v1.questions.vote.update` | sanctum | `QuestionVoteController@update` |
| POST | `/questions/{question}/view` | `api.v1.questions.view.store` | — | `QuestionViewController@store` |

\* Form Request `authorize()` требует authenticated user с соответствующим permission для list/comments list.

## comments.php

| Method | Path | Route name | Auth | Controller action |
|--------|------|------------|------|-------------------|
| PATCH | `/comments/{comment}` | `api.v1.comments.comments.update` | sanctum | `CommentController@update` |
| DELETE | `/comments/{comment}` | `api.v1.comments.comments.delete` | sanctum | `CommentController@delete` |
| POST | `/comments/{restorable_comment}/restore` | `api.v1.comments.comments.restore` | sanctum | `CommentController@restore` |
| PUT | `/comments/{comment}/vote` | `api.v1.comments.vote.update` | sanctum | `CommentVoteController@update` |

## companies.php

| Method | Path | Route name | Auth | Controller action |
|--------|------|------------|------|-------------------|
| GET | `/companies/list` | `api.v1.companies.list` | —* | `CompanyController@list` |

## positions.php

| Method | Path | Route name | Auth | Controller action |
|--------|------|------------|------|-------------------|
| GET | `/positions/list` | `api.v1.positions.list` | —* | `PositionController@list` |

## tags.php

| Method | Path | Route name | Auth | Controller action |
|--------|------|------------|------|-------------------|
| GET | `/tags/list` | `api.v1.tags.list` | —* | `TagController@list` |

## grade.php

| Method | Path | Route name | Auth | Controller action |
|--------|------|------------|------|-------------------|
| GET | `/grades/list` | `api.v1.grades.list` | —* | `GradeController@list` |

## notifications.php

| Method | Path | Route name | Auth | Controller action |
|--------|------|------------|------|-------------------|
| GET | `/notifications/list` | `api.v1.notifications.list` | sanctum | `NotificationController@list` |
| PATCH | `/notifications/{notification}/read` | `api.v1.notifications.markAsRead` | sanctum | `NotificationController@markAsRead` |
| PATCH | `/notifications/read-all` | `api.v1.notifications.markAsReadAll` | sanctum | `NotificationController@markAsReadAll` |

## Endpoint → permission (основные)

| Route | Permission (typical) |
|-------|---------------------|
| `questions.list` | `view-any-question` |
| `questions.propose` | `propose-question` |
| `questions.feedback.submit` | `send-feedback-question` |
| `questions.vote.update` | `vote-question` |
| `questions.comments.store` | `create-comment` |
| `comments.vote.update` | `vote-comment` |
| `companies.list` | `view-any-company` |
| `tags.list` | `view-any-tag` |

Полная матрица: [permissions.md](../permissions.md).
