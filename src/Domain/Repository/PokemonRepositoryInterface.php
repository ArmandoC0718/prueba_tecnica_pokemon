<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Pokemon;
use App\Domain\Entity\User;

interface PokemonRepositoryInterface
{
    public function findRandomWildPokemon(): ?Pokemon;
    public function findByTrainer(User $trainer): array;
    public function findById(int $id): ?Pokemon;
    public function save(Pokemon $pokemon): void;
}