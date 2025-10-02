<?php

namespace App\Presentation\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class AuthController extends AbstractController
{
    #[Route('/api/login', name:'api_login', methods: ['POST'])]
    public function login(): JsonResponse 
    {
        return new JsonResponse([
            'message' => 'Acceso autorizado',
        ]);
    }
}