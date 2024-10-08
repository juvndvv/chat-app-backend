<?php

namespace App\User\Domain\Exception;

use Exception;
use Throwable;

final class UserCreationException extends Exception
{
    public function __construct(string $message = "", int $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
