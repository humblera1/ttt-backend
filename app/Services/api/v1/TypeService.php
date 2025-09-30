<?php

namespace App\Services\api\v1;

use App\Enums\Type;

final class TypeService
{
    public function convertStringToType(string $string, string $type): mixed
    {
        return match ($type) {
            Type::Boolean->value => $this->convertStringToBoolean($string),
            Type::Integer->value => $this->convertStringToInteger($string),
            Type::Float->value => $this->convertStringToFloat($string),
            Type::Array->value => $this->convertStringToArray($string),

            default => $string,
        };
    }

    public function convertStringToBoolean(string $string): bool
    {
        return match (strtolower($string)) {
            "true", "1", "yes", "on" => true,
            default => false,
        };
    }

    public function convertStringToInteger(string $string): int
    {
        return (int) $string;
    }

    public function convertStringToFloat(string $string): float
    {
        return (float) $string;
    }

    public function convertStringToArray(string $string): array
    {
        if (json_validate($string)) {
            return json_decode($string, true);
        }

        // todo: поведение convertStringToArray
        return explode('', $string);
    }
}
