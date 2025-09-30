<?php

namespace App\Enums;

use App\Traits\Enums\WithValues;

enum Type: string
{
    use WithValues;

    case Boolean = 'boolean';

    case Integer = 'integer';

    case Float = 'float';

    case String = 'string';

    case Array = 'array';
}
