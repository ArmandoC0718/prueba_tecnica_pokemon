<?php

namespace App\Domain\Exception;

use Exception;

class UnauthorizedAccessException extends Exception
{
    public function __construct(string $message = "Acceso denegado")
    {
        parent::__construct($message);
    }
}