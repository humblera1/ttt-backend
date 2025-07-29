<?php

namespace App\DTOs\v1\Users;

use App\DTOs\BaseDTO;

class UserChangePasswordDTO extends BaseDTO
{
    public function __construct(
        public readonly string $currentPassword,
        public readonly string $newPassword,
    )
    {}
}
