<?php

namespace App\Interfaces\v1\Resolving;

use App\Models\Tag;

class TagResolver extends Resolver
{
    public string $modelClass = Tag::class;
}
