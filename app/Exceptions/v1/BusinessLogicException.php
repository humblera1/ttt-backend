<?php

namespace App\Exceptions\v1;

use App\Exceptions\BaseException;
use Throwable;

class BusinessLogicException extends BaseException
{
    public function __construct(
        string $message = "Business Logic Exception",
        int $code = 0,
        ?array $context = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
    }
}
