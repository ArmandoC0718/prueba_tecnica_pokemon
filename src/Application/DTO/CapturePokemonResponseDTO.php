<?php

namespace App\Application\DTO;

class CapturePokemonResponseDTO
{
    public function __construct(
        public readonly bool $success,
        public readonly string $message,
        public readonly ?array $capturedPokemon = null
    ) {}
}