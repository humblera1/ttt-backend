<?php

namespace App\Traits\User;

use Filament\Panel;

trait HasFilamentAccess
{
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->hasRole('admin');
    }
}
