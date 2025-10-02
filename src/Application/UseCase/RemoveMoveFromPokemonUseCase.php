<?php

namespace App\Application\UseCase;

use App\Application\DTO\PokemonMoveOperationResponseDTO;
use App\Domain\Repository\PokemonRepositoryInterface;
use App\Domain\Repository\MoveRepositoryInterface;
use App\Domain\Exception\PokemonNotFoundException;
use App\Domain\Exception\MoveNotFoundException;
use App\Domain\Exception\UnauthorizedAccessException;
use App\Domain\Entity\User;

class RemoveMoveFromPokemonUseCase
{
    public function __construct(
        private PokemonRepositoryInterface $pokemonRepository,
        private MoveRepositoryInterface $moveRepository
    ) {
    }

    public function execute(int $pokemon_id, int $moveId, User $currentUser): PokemonMoveOperationResponseDTO
    {
        // Verificar que el Pokémon existe
        $pokemon = $this->pokemonRepository->findById($pokemon_id);
        if (!$pokemon) {
            throw new PokemonNotFoundException("Pokémon con ID {$pokemon_id} no encontrado");
        }

        // Verificar que el movimiento existe
        $move = $this->moveRepository->findById($moveId);
        if (!$move) {
            throw new MoveNotFoundException("Movimiento con ID {$moveId} no encontrado");
        }

        // Verificar que el usuario es el dueño del Pokémon
        if (!$pokemon->getTrainer() || $pokemon->getTrainer()->getId() !== $currentUser->getId()) {
            throw new UnauthorizedAccessException("Solo el entrenador dueño puede hacer olvidar movimientos a este Pokémon");
        }

        // Verificar si el Pokémon conoce el movimiento
        if (!$pokemon->getMoves()->contains($move)) {
            return new PokemonMoveOperationResponseDTO(
                false,
                "El Pokémon no conoce este movimiento"
            );
        }

        // Hacer olvidar el movimiento
        $pokemon->removeMove($move);
        $this->pokemonRepository->save($pokemon);

        // Preparar la lista de movimientos actualizada
        $movesList = $pokemon->getMoves()->map(fn($m) => [
            'id' => $m->getId(),
            'name' => $m->getName()
        ])->toArray();

        return new PokemonMoveOperationResponseDTO(
            true,
            "Movimiento olvidado exitosamente",
            $movesList
        );
    }
}