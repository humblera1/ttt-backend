<?php

namespace App\Http\Controllers\v1;

use App\Exceptions\v1\BusinessLogicException;
use App\Http\Controllers\Controller;
use App\Http\Requests\v1\Users\UserChangePasswordRequest;
use App\Http\Requests\v1\Users\UserForgotPasswordRequest;
use App\Http\Requests\v1\Users\UserLoginRequest;
use App\Http\Requests\v1\Users\UserRegisterRequest;
use App\Http\Requests\v1\Users\UserResetPasswordRequest;
use App\Http\Resources\v1\UserResource;
use App\Services\api\v1\AuthService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        protected AuthService $service,
    )
    {}

    /**
     * @throws BusinessLogicException
     */
    public function me(): UserResource
    {
        if (!Auth::check()) {
            $this->service->registerAndLoginGuest();
        }

        return new UserResource(Auth::user());
    }

    /**
     * @throws BusinessLogicException
     */
    public function register(UserRegisterRequest $request): UserResource
    {
        $this->service->promoteGuest($request->getDTO());

        return new UserResource(Auth::user());
    }

    public function login(UserLoginRequest $request): UserResource
    {
        $credentialsDTO = $request->getDTO();

        if (!Auth::attempt($credentialsDTO->toArray())) {
            $this->responseWithPlainValidationError(__('auth.failed'));
        }

        session()->regenerate();

        return new UserResource(Auth::user());
    }

    /**
     * @throws BusinessLogicException
     */
    public function changePassword(UserChangePasswordRequest $request)
    {
        $this->service->changePassword($request->getDTO());
    }

    public function forgotPassword(UserForgotPasswordRequest $request): Response
    {
        $this->service->sendPasswordResetLink($request->validated('email'));

        return response()->noContent();
    }

    public function resetPassword(UserResetPasswordRequest $request): Response
    {
        $this->service->resetPassword($request->getDTO());

        return response()->noContent();
    }
}
