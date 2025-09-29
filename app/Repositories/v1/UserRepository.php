<?php

namespace App\Repositories\v1;

use App\Models\User;
use App\Repositories\Repository;

class UserRepository extends Repository
{
    public function __construct()
    {
        parent::__construct(User::class);
    }
}
