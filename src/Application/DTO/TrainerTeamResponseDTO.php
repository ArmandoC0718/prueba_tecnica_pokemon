<?php

namespace App\Application\DTO;

class TrainerTeamResponseDTO
{
    public function __construct(
        public readonly int $trainerId,
        public readonly string $trainerName,
        public readonly string $trainerType,
        public readonly array $pokemon
    ) {
    }
}