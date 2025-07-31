<?php

namespace App\Repositories\v1;

use App\Models\User;
use App\Repositories\Repository;
use Illuminate\Database\Eloquent\Model;

class UserRepository extends Repository
{
    protected function getModelInstance(): Model
    {
        return new User();
    }
}
