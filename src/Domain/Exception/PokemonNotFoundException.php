<?php

namespace App\Domain\Exception;

use Exception;

class PokemonNotFoundException extends Exception
{
    public function __construct(string $message = "Pokémon no encontrado")
    {
        parent::__construct($message);
    }
}