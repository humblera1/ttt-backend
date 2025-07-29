<?php

namespace App\DTOs;

use Illuminate\Contracts\Support\Arrayable;
use ReflectionClass;
use ReflectionProperty;

class BaseDTO implements Arrayable
{
    public function toArray(): array
    {
        $reflectionClass = new ReflectionClass($this);
        $properties = $reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC);

        $propertiesAsArray = [];

        foreach ($properties as $property) {
            $propertiesAsArray[$property->getName()] = $property->getValue($this);
        }

        return $propertiesAsArray;
    }
}
