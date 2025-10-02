<?php

namespace App\Application\DTO;

class PokemonMoveOperationResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?array $pokemonMoves = null
    ) {}
}