<?php

namespace App\DTOs\v1\Users;

use App\DTOs\BaseDTO;

class UserResetPasswordDTO extends BaseDTO
{
    public function __construct(
        public readonly string $email,
        public readonly string $password,
        public readonly string $passwordConfirmation,
        public readonly string $token,
    ) {}
}
