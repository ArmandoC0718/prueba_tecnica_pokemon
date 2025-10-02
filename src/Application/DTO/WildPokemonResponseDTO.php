<?php

namespace App\Application\DTO;

class WildPokemonResponseDTO
{
    public function __construct(
        public readonly int $id,
        public readonly string $name,
        public readonly string $nickname,
        public readonly array $types,
        public readonly int $level,
        public readonly int $healthPoints,
        public readonly int $attack,
        public readonly int $defense,
        public readonly int $speed,
        public readonly int $catchRate,
        public readonly array $moves
    ) {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'nickname' => $this->nickname,
            'types' => $this->types,
            'level' => $this->level,
            'health_points' => $this->healthPoints,
            'attack' => $this->attack,
            'defense' => $this->defense,
            'speed' => $this->speed,
            'catch_rate' => $this->catchRate,
            'moves' => $this->moves
        ];
    }
}