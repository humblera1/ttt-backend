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
];
