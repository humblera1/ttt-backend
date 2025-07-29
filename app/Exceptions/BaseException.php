<?php

namespace App\Exceptions;

use Exception;

class BaseException extends Exception
{
    protected ?array $context {
        get {
            return $this->context;
        }
    }
}
