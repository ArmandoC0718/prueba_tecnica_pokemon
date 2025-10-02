<?php

namespace App\Application\UseCase;

use App\Application\DTO\CapturePokemonResponseDTO;
use App\Domain\Repository\PokemonRepositoryInterface;
use App\Domain\Exception\PokemonNotFoundException;
use App\Domain\Exception\PokemonAlreadyCaptureException;
use App\Domain\Exception\UnauthorizedAccessException;
use App\Domain\Entity\User;

class CapturePokemonUseCase
{
    public function __construct(
        private PokemonRepositoryInterface $pokemonRepository
    ) {}

    public function execute(int $pokemonId, User $trainer, ?string $nickname = null): CapturePokemonResponseDTO
    {
        // Verificar que el usuario es un entrenador
        if (!in_array('ROLE_TRAINER', $trainer->getRoles())) {
            throw new UnauthorizedAccessException("Solo los entrenadores pueden capturar Pokémon");
        }

        // Verificar que el Pokémon existe
        $pokemon = $this->pokemonRepository->findById($pokemonId);
        if (!$pokemon) {
            throw new PokemonNotFoundException("No se encontro el pokemon con el id {$pokemonId}");
        }

        // Verificar que el Pokémon está salvaje (no tiene entrenador)
        if ($pokemon->getTrainer() !== null) {
            throw new PokemonAlreadyCaptureException(
                "El Pokémon {$pokemon->getName()} ya pertenece a otro entrenador"
            );
        }

        // Asignar el Pokémon al entrenador
        $pokemon->setTrainer($trainer);

        // Asignar nickname si se proporciona, sino generar uno automático
        if ($nickname) {
            $pokemon->setNickname($nickname);
        } else {
            // Generar nickname automático: nombre + número aleatorio
            $autoNickname = ucfirst($pokemon->getName()) . '_' . rand(100, 999);
            $pokemon->setNickname($autoNickname);
        }

        // Guardar cambios
        $this->pokemonRepository->save($pokemon);

        // Preparar respuesta
        $caughtPokemonData = [
            'id' => $pokemon->getId(),
            'name' => $pokemon->getName(),
            'nickname' => $pokemon->getNickname(),
            'level' => $pokemon->getLevel(),
            'trainer' => $trainer->getUsername(),
            'types' => $pokemon->getTypes()->map(fn($type) => $type->getName())->toArray(),
            'moves' => $pokemon->getMoves()->map(fn($move) => [
                'id' => $move->getId(),
                'name' => $move->getName()
            ])->toArray()
        ];

        return new CapturePokemonResponseDTO(
            true,
            "¡Pokémon capturado exitosamente!",
            $caughtPokemonData
        );
    }
}