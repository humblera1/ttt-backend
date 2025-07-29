<?php

namespace App\DTOs\v1\Users;

use App\DTOs\BaseDTO;

class UserLoginDTO extends BaseDTO
{
    public function __construct(
        public readonly string $username,
        public readonly string $password,
    ) {}
}
