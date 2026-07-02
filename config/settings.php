<?php

use App\Enums\Settings\Section;

return [
    Section::Question->value => [
        [
            'key' => 'per_page',
            'label' => 'Количество записей на странице',
            'value' => 10,
        ],
        [
            'key' => 'rate-limit-seconds',
            'label' => 'Минимальный интервал в секундах между постановками job пересчёта рейтинга для одного вопроса',
            'value' => 60,
        ],
    ],
    Section::Tag->value => [
        [
            'key' => 'first_page_per_page',
            'label' => 'Количество записей на превью',
            'value' => 10,
        ],
        [
            'key' => 'per_page',
            'label' => 'Количество подгружаемых за раз записей',
            'value' => 15,
        ],
    ],
    Section::Company->value => [
        [
            'key' => 'first_page_per_page',
            'label' => 'Количество записей на превью',
            'value' => 10,
        ],
        [
            'key' => 'per_page',
            'label' => 'Количество подгружаемых за раз записей',
            'value' => 15,
        ],
    ],
    Section::Statistics->value => [
        [
            'key' => 'max_records_per_user_question',
            'label' => 'Максимальное число опросов, которое пользователь может пройти для одного вопроса',
            'value' => 3,
        ],
        [
            'key' => 'company_suggestion_evidence_threshold',
            'label' => 'Число встреч комбинации вопрос-компания, достаточное для создания предложения на связывание',
            'value' => 3,
        ],
        [
            'key' => 'position_suggestion_evidence_threshold',
            'label' => 'Число встреч комбинации вопрос-должность, достаточное для создания предложения на связывание',
            'value' => 3,
        ],
    ],
    Section::Notifications->value => [
        [
            'key' => 'read_ttl_days',
            'label' => 'Количество дней, после которого прочитанные уведомления будут удалены',
            'value' => 30,
        ],
        [
            'key' => 'unread_ttl_days',
            'label' => 'Количество дней, после которого непрочитанные уведомления будут удалены',
            'value' => 90,
        ],
        [
            'key' => 'max_per_user',
            'label' => 'Максимальное количество уведомлений на одного пользователя, после которого старые уведомления будут удалены',
            'value' => 500,
        ],
        [
            'key' => 'default_per_page',
            'label' => 'Количество подгружаемых за раз записей (по умолчанию)',
            'value' => 10,
        ],
    ],
    Section::Comments->value => [
        [
            'key' => 'per_page',
            'label' => 'Количество записей на странице вопроса, подгружаемых за раз',
            'value' => 30,
        ],
    ],
    Section::QuestionViews->value => [
        [
            'key' => 'dedup_ttl_seconds',
            'label' => 'IP deduplication window (seconds)',
            'description' => 'Time window during which repeated POST /view requests from the same IP for the same question do not increment the Redis buffer. Implemented via SET key NX EX.',
            'value' => 120,
        ],
        [
            'key' => 'flush_lock_seconds',
            'label' => 'Flush lock TTL (seconds)',
            'description' => 'TTL for Cache::lock("flush-question-views") while the Redis view buffer is being flushed to the database. Prevents concurrent flush runs.',
            'value' => 30,
        ],
        [
            'key' => 'rate_limit_per_minute',
            'label' => 'POST view rate limit per IP (per minute)',
            'description' => 'Maximum number of POST /view requests allowed per minute from a single IP address. Exceeding the limit returns HTTP 429 via throttle:question-view middleware.',
            'value' => 60,
        ],
    ],
];
