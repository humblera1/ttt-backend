<?php

return [
    'redis' => [
        'buffer_key' => 'question:views:buffer',
        'buffer_tmp_key' => 'question:views:buffer:tmp',
        'dedup_key_prefix' => 'question:views:dedup:',
    ],

    'flush_chunk_size' => 300,
];
