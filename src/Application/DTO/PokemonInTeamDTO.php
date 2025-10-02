<?php

namespace App\Application\DTO;

class PokemonInTeamDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $nickname,
        public readonly int $level,
        public readonly array $types,
        public readonly array $moves,
        public readonly int $healthPoints,
        public readonly int $attack,
        public readonly int $defense,
        public readonly int $speed
    ) {
    }
}