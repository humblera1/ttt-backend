<?php

namespace App\Traits\User;

trait HasFilamentName
{
    public function getFilamentName(): string
    {
        return $this->username;
    }
}
