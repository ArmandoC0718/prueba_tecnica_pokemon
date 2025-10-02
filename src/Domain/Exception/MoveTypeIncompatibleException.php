<?php

namespace App\Domain\Exception;

use Exception;

class MoveTypeIncompatibleException extends Exception
{
    public function __construct(string $message = "El tipo del movimiento no es compatible con el Pokémon")
    {
        parent::__construct($message);
    }
}