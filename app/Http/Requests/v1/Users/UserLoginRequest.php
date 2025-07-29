<?php

namespace App\Http\Requests\v1\Users;

use App\DTOs\v1\Users\UserLoginDTO;
use App\Http\Requests\BaseFormRequest;
use App\Interfaces\v1\Requests\RequestDTOInterface;

class UserLoginRequest extends BaseFormRequest implements RequestDTOInterface
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
            'username' => ['required', 'string:255'],
            'password' => ['required', 'string'],
        ];
    }

    public function getDTO(): UserLoginDTO
    {
        return new UserLoginDTO(
            username: $this->validated('username'),
            password: $this->validated('password'),
        );
    }
}
