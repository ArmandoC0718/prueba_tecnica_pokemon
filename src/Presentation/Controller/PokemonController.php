<?php

namespace App\Presentation\Controller;

use App\Application\UseCase\FindWildPokemonUseCase;
use App\Application\UseCase\CapturePokemonUseCase;
use App\Domain\Exception\PokemonNotFoundException;
use App\Domain\Exception\PokemonAlreadyCaptureException;
use App\Domain\Exception\UnauthorizedAccessException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

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

    #[Route('/{id}/catch', name: 'api_pokemon_catch', methods: ['POST'])]
    #[IsGranted('ROLE_TRAINER')]
    public function catchPokemon(int $id, Request $request, CapturePokemonUseCase $capturePokemonUseCase): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            $nickname = $data['nickname'] ?? null;

            $currentUser = $this->getUser();
            $result = $capturePokemonUseCase->execute($id, $currentUser, $nickname);

            return new JsonResponse([
                'success' => $result->success,
                'message' => $result->message,
                'data' => [
                    'caught_pokemon' => $result->capturedPokemon
                ]
            ], Response::HTTP_OK);

        } catch (PokemonNotFoundException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Pokémon no encontrado'
            ], Response::HTTP_NOT_FOUND);

        } catch (PokemonAlreadyCaptureException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], Response::HTTP_CONFLICT);

        } catch (UnauthorizedAccessException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Solo los entrenadores pueden capturar Pokémon'
            ], Response::HTTP_FORBIDDEN);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

}