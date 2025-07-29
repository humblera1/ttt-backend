<?php

namespace App\Http\Requests;

use App\Exceptions\v1\WithPlainErrorsValidationException;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\ValidationException;

class BaseFormRequest extends FormRequest
{
    /**
     * Handle a failed validation attempt.
     *
     * @param  Validator  $validator
     * @return void
     *
     * @throws ValidationException
     */
    protected function failedValidation(Validator $validator): void
    {
        throw new WithPlainErrorsValidationException($validator)
            ->errorBag($this->errorBag)
            ->redirectTo($this->getRedirectUrl());
    }
}
