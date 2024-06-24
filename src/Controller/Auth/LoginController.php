<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class LoginController extends AbstractController
{
    #[Route('/api/auth/login', name: 'api_login')]
    public function login(#[CurrentUser] ?User $user): Response
    {
        return $this->json([
            'user' => $user,
        ]);
    }

    #[Route('/api/auth/logout', name: 'api_logout')]
    public function logout(): Response
    {
        return $this->json([
            'message' => 'logout successful',
        ]);
    }
}
