<?php

namespace App\Domain\Exception;

use Exception;

class UserNotFoundException extends Exception
{
    public function __construct(string $message = "No se encontró el usuario")
    {
        parent::__construct($message);
    }
}