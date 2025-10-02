<?php

namespace App\Application\UseCase;

use App\Application\DTO\PokemonMoveOperationResponseDTO;
use App\Domain\Repository\PokemonRepositoryInterface;
use App\Domain\Repository\MoveRepositoryInterface;
use App\Domain\Exception\PokemonNotFoundException;
use App\Domain\Exception\MoveNotFoundException;
use App\Domain\Exception\UnauthorizedAccessException;
use App\Domain\Exception\MoveTypeIncompatibleException;
use App\Domain\Entity\User;

class AssignMoveToPokemonUseCase
{
    public function __construct(
        private PokemonRepositoryInterface $pokemonRepository,
        private MoveRepositoryInterface $moveRepository
    ) {}

    public function execute(int $pokemon_id, int $moveId, User $currentUser): PokemonMoveOperationResponseDTO
    {
        // Verificar que el Pokémon existe
        $pokemon = $this->pokemonRepository->findById($pokemon_id);
        if (!$pokemon) {
            throw new PokemonNotFoundException("Pokemon con el id {$pokemon_id} no encontrado");
        }

        // Verificar que el movimiento existe
        $move = $this->moveRepository->findById($moveId);
        if (!$move) {
            throw new MoveNotFoundException("Movimiento con el id {$moveId} no encontrado");
        }

        // Verificar que el usuario es el dueño del Pokémon
        if (!$pokemon->getTrainer() || $pokemon->getTrainer()->getId() !== $currentUser->getId()) {
            throw new UnauthorizedAccessException("Solo el entrenador dueño puede enseñar movimientos a este Pokémon");
        }

        // Verificar compatibilidad de tipos
        $this->validateMoveCompatibility($pokemon, $move);

        // Verificar si el Pokémon ya conoce el movimiento
        if ($pokemon->getMoves()->contains($move)) {
            return new PokemonMoveOperationResponseDTO(
                false,
                "El Pokémon ya conoce este movimiento"
            );
        }

        // Enseñar el movimiento
        $pokemon->addMove($move);
        $this->pokemonRepository->save($pokemon);

        // Preparar la lista de movimientos actualizada
        $movesList = $pokemon->getMoves()->map(fn($m) => [
            'id' => $m->getId(),
            'name' => $m->getName(),
        ])->toArray();

        return new PokemonMoveOperationResponseDTO(
            true,
            "Movimiento enseñado exitosamente",
            $movesList
        );
    }

    private function validateMoveCompatibility($pokemon, $move): void
    {
        $moveType = $move->getType();
        if (!$moveType) {
            return; // Si el movimiento no tiene tipo, lo permitimos
        }

        $pokemonTypes = $pokemon->getTypes();
        
        // Verificar si al menos uno de los tipos del pokemon coincide con el tipo del movimiento
        foreach ($pokemonTypes as $pokemonType) {
            if ($pokemonType->getId() === $moveType->getId()) {
                return; 
            }
        }

        throw new MoveTypeIncompatibleException(
            "El movimiento de tipo {$moveType->getName()} no es compatible con los tipos del Pokémon"
        );
    }
}