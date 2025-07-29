<?php

namespace App\Services\api\v1;

use App\DTOs\v1\Users\UserChangePasswordDTO;
use App\DTOs\v1\Users\UserRegisterDTO;
use App\DTOs\v1\Users\UserResetPasswordDTO;
use App\Events\v1\GuestRegistered;
use App\Exceptions\v1\BusinessLogicException;
use App\Factories\v1\UserFactory;
use App\Models\User;
use App\Repositories\v1\UserRepository;
use Exception;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Exception\BadRequestException;

class AuthService
{
    public function __construct(
        protected UserFactory $factory,
        protected UserRepository $repository,
    )
    {}

    /**
     * @throws BusinessLogicException
     */
    public function registerAndLoginGuest(): void
    {
        try {
            $guest = $this->factory->createGuest();
            $this->repository->save($guest);

            $guest->assignRole('guest');
        } catch (Exception $e) {
            Log::error('Failed to register and login guest', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }

        event(new GuestRegistered($guest));

        Auth::login($guest);
    }

    /**
     * @throws BusinessLogicException
     */
    public function promoteGuest(UserRegisterDTO $registerDTO): void
    {
        try {
            $user = Auth::user();

            $user->fill($registerDTO->toArray());

            $this->repository->save($user);

            $user->syncRoles(['user']);
        } catch (Exception $e) {
            Log::error('Failed to promote guest', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }

    /**
     * @throws BusinessLogicException
     */
    public function changePassword(UserChangePasswordDTO $changePasswordDTO): void
    {
        try {
            $user = Auth::user();

            Auth::guard('web')->logoutOtherDevices($changePasswordDTO->currentPassword);

            $user->password = $changePasswordDTO->newPassword;

            $this->repository->save($user);
        } catch (Exception $e) {
            Log::error('Failed to change password', ['exception' => $e]);

            throw new BusinessLogicException($e->getMessage());
        }
    }

    public function sendPasswordResetLink(string $email): void
    {
        $status = Password::sendResetLink([
            'email' => $email,
        ]);

        if ($status !== Password::ResetLinkSent) {
            throw new BadRequestException(__($status));
        }
    }

    public function resetPassword(UserResetPasswordDTO $resetPasswordDTO): void
    {
        $status = Password::reset(
            $resetPasswordDTO->toArray(),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));

                $this->repository->save($user);

                event(new PasswordReset($user));
            }
        );

        if ($status !== Password::PasswordReset) {
            throw new BadRequestException(__($status));
        }
    }
}
