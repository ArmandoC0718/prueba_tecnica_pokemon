<?php

namespace App\Domain\Exception;

use Exception;

class PokemonAlreadyCaptureException extends Exception
{
    public function __construct(string $message = "Este Pokémon ya ha sido capturado por otro entrenador")
    {
        parent::__construct($message);
    }
}