<?php

namespace App\Controller;

use App\Entity\Password;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class PasswordController extends AbstractController
{
    #[Route('/api/passwords', name: 'app_passwords_index', methods: ['GET'])]
    public function index(#[CurrentUser] ?UserInterface $user, EntityManagerInterface $em): Response
    {
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $passwords = $em->getRepository(Password::class)->findBy(['owner' => $user]);

        return $this->json($passwords, 200, [], ['groups' => 'password:read']);
    }

    #[Route('/api/passwords', name: 'app_passwords_create', methods: ['POST'])]
    public function create(#[CurrentUser] ?UserInterface $user, Request $request, EntityManagerInterface $em, LoggerInterface $logger): Response
    {
        $logger->info('User in create method:', ['user' => $user]);
        $token = $request->headers->get('Authorization');
        $logger->info('Authorization Header:', ['token' => $token]);

        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $data = $request->getPayload();

        $password = new Password();
        $password->setLabel($data->get('label'));
        $password->setCreatedAt(new \DateTimeImmutable());
        // Encrypt the password
        $cipher = 'aes-256-cbc';
        $p = $data->get('password');
        $p = openssl_encrypt($p, $cipher, $_ENV['APP_SECRET'] . $password->getCreatedAt()->format('Y-m-d H:i:s'), 0, $_ENV['APP_IV']);
        $password->setPassword($p);
        $password->setComment($data->get('comment'));
        $password->setOwner($user);

        $em->persist($password);
        $em->flush();

        return $this->json($password);
    }
}
