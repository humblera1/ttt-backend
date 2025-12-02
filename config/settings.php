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
    ]
];
