<?php

namespace App\Presentation\Controller;

use App\Application\UseCase\FindWildPokemonUseCase;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/pokemon')]
class PokemonController extends AbstractController
{

    #[Route('/random', name: 'api_pokemon_random', methods: ['GET'])]
    //#[IsGranted('ROLE_TRAINER')] mostrar solo a entrenadores
    public function  getRandomPokemon(FindWildPokemonUseCase $findWildPokemonUseCase) : JsonResponse 
    {
        try {
            $wildPokemonDTO = $findWildPokemonUseCase->execute();

            if(!$wildPokemonDTO) {
                return new JsonResponse([
                    'success' => false,
                    'message' => 'No hay pokemones salvajes disponibles. ',
                    'data' => null
                ], Response::HTTP_NOT_FOUND);
            }

            return new JsonResponse([
                'success' => true,
                'message' => 'Has encontrado un Pokemon salvaje',
                'data' => $wildPokemonDTO->toArray()
            ], Response::HTTP_OK);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => 'Error interno del servidor.',
                'error' => $e->getMessage(),
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}