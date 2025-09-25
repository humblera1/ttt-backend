<?php

namespace App\Traits\Tests;

use App\Models\User;

trait WithUser
{
    protected function getUser(): User
    {
        $user = User::factory()->create();

        $user->givePermissionTo($this->permission);

        return $user;
    }
}
