<?php

namespace App\Entities\Normalizer;

readonly class NormalizedNameOptions
{
    public function __construct(
        public string $fieldName = 'name',
        public string $normalizedFieldName = 'normalized_name',
    )
    {}
}
