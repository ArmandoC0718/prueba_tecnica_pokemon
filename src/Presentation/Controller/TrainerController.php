<?php

namespace App\Presentation\Controller;

use App\Application\UseCase\GetTrainerTeamUseCase;
use App\Domain\Exception\UserNotFoundException;
use App\Domain\Exception\UnauthorizedAccessException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class TrainerController extends AbstractController
{
    public function __construct(
        private GetTrainerTeamUseCase $getTrainerTeamUseCase
    ) {
    }

    #[Route('/api/trainers/{id}/team', name: 'api_trainer_team', methods: ['GET'])]
    #[IsGranted('ROLE_USER')]
    public function getTrainerTeam(int $id): JsonResponse
    {
        try {
            $currentUser = $this->getUser();
            $teamData = $this->getTrainerTeamUseCase->execute($id, $currentUser);

            return new JsonResponse([
                'success' => true,
                'message' => 'Listado del equipo del entrenador',
                'data' => [
                    'id' => $teamData->trainerId,
                    'name' => $teamData->trainerName,
                    'type' => $teamData->trainerType,
                    'team' => $teamData->pokemon
                ],
            ], Response::HTTP_OK);

        } catch (UserNotFoundException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'No se encontró el entrenador'
            ], Response::HTTP_NOT_FOUND);

        } catch (UnauthorizedAccessException $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Acceso denegado, solo puedes ver tu propio equipo'
            ], Response::HTTP_FORBIDDEN);

        } catch (\Exception $e) {
            return new JsonResponse([
                'success' => false,
                'error' => 'Internal server error'
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}