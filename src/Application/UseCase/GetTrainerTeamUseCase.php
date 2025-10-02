<?php

namespace App\Application\UseCase;

use App\Application\DTO\TrainerTeamResponseDTO;
use App\Application\DTO\PokemonInTeamDTO;
use App\Domain\Repository\UserRepositoryInterface;
use App\Domain\Repository\PokemonRepositoryInterface;
use App\Domain\Exception\UserNotFoundException;
use App\Domain\Exception\UnauthorizedAccessException;
use App\Domain\Entity\User;

class GetTrainerTeamUseCase
{
    public function __construct(
        private UserRepositoryInterface $userRepository,
        private PokemonRepositoryInterface $pokemonRepository
    ) {}

    public function execute(int $trainerId, User $currentUser): TrainerTeamResponseDTO
    {
        // Verificar que el entrenador existe
        $trainer = $this->userRepository->findById($trainerId);
        if (!$trainer) {
            throw new UserNotFoundException("No se encontró el entrenador con ID $trainerId.");
        }

        // Verificar permisos de acceso
        $this->validateAccess($trainer, $currentUser);

        // Obtener el equipo del entrenador
        $pokemonTeam = $this->pokemonRepository->findByTrainer($trainer);

        // Convertir a DTOs
        $pokemonDTOs = array_map(function($pokemon) {
            return new PokemonInTeamDTO(
                id: $pokemon->getId(),
                name: $pokemon->getName(),
                nickname: $pokemon->getNickname(),
                level: $pokemon->getLevel(),
                types: $pokemon->getTypes()->map(fn($type) => $type->getName())->toArray(),
                healthPoints: $pokemon->getHealthPoints(),
                attack: $pokemon->getAttack(),
                defense: $pokemon->getDefense(),
                speed: $pokemon->getSpeed(),
                moves: $pokemon->getMoves()->map(fn($move) => [
                    'id' => $move->getId(),
                    'name' => $move->getName()
                ])->toArray(),
            );
        }, $pokemonTeam);

        return new TrainerTeamResponseDTO(
            trainerId: $trainer->getId(),
            trainerName: $trainer->getUsername(),
            trainerType: in_array('ROLE_PROFESSOR', $trainer->getRoles()) ? 'proffesor' : 'trainer',
            pokemon: $pokemonDTOs
        );
    }

    private function validateAccess(User $trainer, User $currentUser): void
    {
        // Un profesor puede ver cualquier equipo
        if (in_array('ROLE_PROFESSOR', $currentUser->getRoles())) {
            return;
        }

        // Un entrenador solo puede ver su propio equipo
        if (in_array('ROLE_TRAINER', $currentUser->getRoles()) && $currentUser->getId() === $trainer->getId()) {
            return;
        }

        // Cualquier otro caso es acceso no autorizado
        throw new UnauthorizedAccessException("Acceso denegado al equipo de otro entrenador.");
    }
}