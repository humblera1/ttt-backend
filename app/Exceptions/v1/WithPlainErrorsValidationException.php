<?php

namespace App\Exceptions\v1;

use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Validator as ValidatorFacade;
use Illuminate\Validation\ValidationException;

class WithPlainErrorsValidationException extends ValidationException
{
    /**
     * Get only one (first) validation error message for each attribute
     */
    public function errors(): array
    {
        return array_map(function ($messages) {
            return Arr::first($messages);
        }, $this->validator->errors()->messages());
    }

    /**
     * @param string $message
     * @param string $property
     * @return static
     */
    public static function withMessage(string $message, string $property = 'general'): static
    {
        return new static(tap(ValidatorFacade::make([], []), function ($validator) use ($message, $property) {
            $validator->errors()->add($property, $message);
        }));
    }
}
