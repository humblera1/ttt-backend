<?php

namespace App\Traits\Filament\Forms\Utils;

use Illuminate\Support\HtmlString;

trait HasLinkUtils
{
    protected function createLink(string $link, string $label): HtmlString
    {
        return new HtmlString(sprintf(
            '<a href="%s" class="text-primary-600 font-medium">%s</a>',
            e($link),
            e($label),
        ));
    }
}
