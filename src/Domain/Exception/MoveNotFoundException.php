<?php

namespace App\Domain\Exception;

use Exception;

class MoveNotFoundException extends Exception
{
    public function __construct(string $message = "Movimiento no encontrado")
    {
        parent::__construct($message);
    }
}