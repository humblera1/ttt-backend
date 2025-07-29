<?php

namespace App\Http\Requests\v1\Users;

use App\DTOs\v1\Users\UserChangePasswordDTO;
use App\Http\Requests\BaseFormRequest;
use App\Interfaces\v1\Requests\RequestDTOInterface;
use Illuminate\Validation\Rules\Password;

class UserChangePasswordRequest extends BaseFormRequest implements RequestDTOInterface
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
            'current_password' => ['required', 'string', 'current_password'],
            'new_password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function getDTO(): UserChangePasswordDTO
    {
        return new UserChangePasswordDTO(
            currentPassword: $this->validated('current_password'),
            newPassword: $this->validated('new_password')
        );
    }
}
