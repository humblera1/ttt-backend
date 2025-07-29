<?php

namespace App\Http\Requests\v1\Users;

use App\DTOs\v1\Users\UserRegisterDTO;
use App\Http\Requests\BaseFormRequest;
use App\Interfaces\v1\Requests\RequestDTOInterface;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rules\Password;

class UserRegisterRequest extends BaseFormRequest implements RequestDTOInterface
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
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'unique:users','email:255'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ];
    }

    public function getDTO(): UserRegisterDTO
    {
        return new UserRegisterDTO(
            email: $this->validated('email'),
            password: $this->validated('password'),
        );
    }
}
