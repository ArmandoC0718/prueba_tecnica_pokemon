<?php

namespace App\Application\UseCase;

use App\Domain\Repository\PokemonRepositoryInterface;
use App\Application\DTO\WildPokemonResponseDTO;

class FindWildPokemonUseCase 
{
    public function __construct(
        private PokemonRepositoryInterface $pokemonRepository
    ) {}

    public function execute(): ?WildPokemonResponseDTO
    {
        $wildPokemon = $this->pokemonRepository->findRandomWildPokemon();
        
        if (!$wildPokemon) {
            return null;
        }

        return new WildPokemonResponseDTO(
            id: $wildPokemon->getId(),
            name: $wildPokemon->getName(),
            nickname: $wildPokemon->getNickname() ?? '',
            types: array_map(fn($type) => $type->getName(), $wildPokemon->getTypes()->toArray()),
            level: $wildPokemon->getLevel(),
            healthPoints: $wildPokemon->getHealthPoints(),
            attack: $wildPokemon->getAttack(),
            defense: $wildPokemon->getDefense(),
            speed: $wildPokemon->getSpeed(),
            catchRate: $wildPokemon->getCatchRate(),
            moves: array_map(fn($move) => $move->getName(), $wildPokemon->getMoves()->toArray())
        );
    }
}