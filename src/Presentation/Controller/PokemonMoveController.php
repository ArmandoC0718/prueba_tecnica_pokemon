<?php

namespace App\Presentation\Controller;

use App\Application\UseCase\AssignMoveToPokemonUseCase;
use App\Application\UseCase\RemoveMoveFromPokemonUseCase;
use App\Domain\Exception\PokemonNotFoundException;
use App\Domain\Exception\MoveNotFoundException;
use App\Domain\Exception\UnauthorizedAccessException;
use App\Domain\Exception\MoveTypeIncompatibleException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class PokemonMoveController extends AbstractController
{
    public function __construct(
        private AssignMoveToPokemonUseCase $assignMoveToPokemonUseCase,
        private RemoveMoveFromPokemonUseCase $removeMoveFromPokemonUseCase
    ) {}

    #[Route('/api/pokemon/{pokemon_id}/moves', name: 'api_pokemon_teach_move', methods: ['POST'])]
    #[IsGranted('ROLE_TRAINER')]
    public function teachMove(int $pokemon_id, Request $request): JsonResponse
    {
        try {
            $data = json_decode($request->getContent(), true);
            
            if (!isset($data['move_id'])) {
                return new JsonResponse([
                    'success' => false,
                    'error' => 'move_id es requerido'
                ], 400);
            }

            $currentUser = $this->getUser();
            $result = $this->assignMoveToPokemonUseCase->execute(
                $pokemon_id, 
                $data['move_id'], 
                $currentUser
            );

            $statusCode = $result->success ? 200 : 400;
            
            return new JsonResponse([
                'success' => $result->success,
                'message' => $result->message,
                'data' => [
                    'pokemon_moves' => $result->pokemonMoves
                ]
            ], $statusCode);

        } catch (PokemonNotFoundException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'Pokémon no encontrado'
            ], 404);

        } catch (MoveNotFoundException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'Movimiento no encontrado'
            ], 404);

        } catch (UnauthorizedAccessException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'No tienes permiso para realizar esta acción'
            ], 403);

        } catch (MoveTypeIncompatibleException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => $e->getMessage()
            ], 400);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }

    #[Route('/api/pokemon/{pokemon_id}/moves/{move_id}', name: 'api_pokemon_forget_move', methods: ['DELETE'])]
    #[IsGranted('ROLE_TRAINER')]
    public function forgetMove(int $pokemon_id, int $move_id): JsonResponse
    {
        try {
            $currentUser = $this->getUser();
            $result = $this->removeMoveFromPokemonUseCase->execute($pokemon_id, $move_id, $currentUser);

            $statusCode = $result->success ? 200 : 400;
            
            return new JsonResponse([
                'success' => $result->success,
                'message' => $result->message,
                'data' => [
                    'pokemon_moves' => $result->pokemonMoves
                ]
            ], $statusCode);

        } catch (PokemonNotFoundException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Pokémon no encontrado'
            ], 404);

        } catch (MoveNotFoundException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Movimiento no encontrado'
            ], 404);

        } catch (UnauthorizedAccessException $e) {
            return new JsonResponse([
                'success' => false,
                'message' => $e->getMessage(),
                'error' => 'No tienes permiso para realizar esta acción'
            ], 403);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Error interno del servidor'
            ], 500);
        }
    }
}