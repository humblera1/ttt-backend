<?php

namespace App\Factories\v1;

use App\DTOs\v1\Users\UserRegisterDTO;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserFactory
{
    public function createGuest(): User
    {
        return new User([
            'username' => $this->generateGuestUsername(),
        ]);
    }

    public function promoteGuest(UserRegisterDTO $loginDTO): User
    {
        $guest = Auth::user();

        $guest->email = $loginDTO->email;
        $guest->password = $loginDTO->password;

        return $guest;
    }

    protected function generateGuestUsername(): string
    {
        return 'guest' . '-' . Str::lower(Str::random(4)) . time();
    }
}
