<?php

namespace App\Exceptions\v1;

use App\Exceptions\BaseException;
use Throwable;

class RepositoryException extends BaseException
{
    public function __construct(
        string $message = "Repository Exception",
        int $code = 0,
        ?array $context = null,
        ?Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);

        $this->context = $context;
    }
}
