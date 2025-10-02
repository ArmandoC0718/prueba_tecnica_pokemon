<?php

namespace App\Domain\Repository;

use App\Domain\Entity\Pokemon;

interface PokemonRepositoryInterface
{
    public function findRandomWildPokemon(): ?Pokemon;
    public function save(Pokemon $pokemon): void;
}