<?php

namespace App\Application\DTO;

class AssignMoveToPokemonRequestDTO
{
    public function __construct(
        public readonly int $moveId
    ) {}
}