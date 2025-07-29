<?php

namespace App\DTOs\v1\Users;

use App\DTOs\BaseDTO;

class UserRegisterDTO extends BaseDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
    ) {}
}
