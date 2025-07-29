<?php

namespace App\Http\Requests\v1\Users;

use App\DTOs\v1\Users\UserResetPasswordDTO;
use App\Http\Requests\BaseFormRequest;
use App\Interfaces\v1\Requests\RequestDTOInterface;
use Illuminate\Validation\Rules\Password;

class UserResetPasswordRequest extends BaseFormRequest implements RequestDTOInterface
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'exists:users,email'],
            'token' => ['required', 'string'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function getDTO(): UserResetPasswordDTO
    {
        return new UserResetPasswordDTO(
            email: $this->validated('email'),
            password: $this->validated('password'),
            passwordConfirmation: $this->validated('password'),
            token: $this->validated('token'),
        );
    }
}
