<?php

use App\Enums\Settings\Section;

return [
    Section::Question->value => [
        [
            'key' => 'per_page',
            'label' => 'Количество записей на странице',
            'value' => 10,
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
    ],
];
