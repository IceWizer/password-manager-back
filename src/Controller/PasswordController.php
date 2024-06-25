<?php

namespace App\Controller;

use App\Entity\Password;
use App\Entity\Share;
use App\Entity\User;
use App\Service\EncryptionService;
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

    #[Route('/api/passwords/shared', name: 'app_passwords_index_shared', methods: ['GET'])]
    public function indexShared(#[CurrentUser] ?UserInterface $user, EntityManagerInterface $em): Response
    {
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $sharedPasswords = $em->getRepository(Share::class)->findBy(['target' => $user->getId()]);

        $passwords = [];

        foreach ($sharedPasswords as $sharedPassword) {
            $passwords[] = $sharedPassword->getPassword();
        }

        return $this->json($passwords, 200, [], ['groups' => 'password:read']);
    }

    #[Route('/api/passwords/{id}', name: 'app_passwords_show', methods: ['GET'])]
    public function show(#[CurrentUser] ?UserInterface $user, Password $password): Response
    {
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        if ($password->getOwner() !== $user) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        return $this->json($password, 200, [], ['groups' => 'password:read']);
    }

    #[Route('/api/passwords', name: 'app_passwords_create', methods: ['POST'])]
    public function create(#[CurrentUser] ?UserInterface $user, Request $request, EntityManagerInterface $em, LoggerInterface $logger, EncryptionService $encryptionService): Response
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
        $password->setPassword($encryptionService->encrypt($data->get("password"), $password->getCreatedAt()->format('Y-m-d H:i:s')));
        $password->setComment($data->get('comment'));
        $password->setOwner($user);

        $em->persist($password);
        $em->flush();

        return $this->json($password, 200, [], ['groups' => 'password:read']);
    }

    #[Route('/api/passwords/{id}', name: 'app_passwords_delete', methods: ['DELETE'])]
    public function delete(#[CurrentUser] ?UserInterface $user, Password $password, EntityManagerInterface $em): Response
    {
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        if ($password->getOwner() !== $user) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $em->remove($password);
        $em->flush();

        return $this->json(null, 204);
    }

    #[Route('/api/passwords/{id}', name: 'app_passwords_update', methods: ['PUT'])]
    public function update(#[CurrentUser] ?UserInterface $user, Password $password, Request $request, EntityManagerInterface $em): Response
    {
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        if ($password->getOwner() !== $user) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $data = $request->getPayload();

        $password->setLabel($data->get('label'));

        if ($data->get('password')) {
            $password->setPassword($data->get('password'));
        }

        $password->setComment($data->get('comment'));

        $em->flush();

        return $this->json($password, 200, [], ['groups' => 'password:read']);
    }

    #[Route('/api/passwords/{id}/{encryptionKey}', name: 'app_passwords_show_password', methods: ['GET'])]
    public function showPassword(#[CurrentUser] ?UserInterface $user, Password $password, EncryptionService $encryptionService, string $encryptionKey, EntityManagerInterface $em): Response
    {
        if (!$user) {
            return $this->json(['error' => 'Unauthorized'], 401);
        }

        $sharedPasswords = $em->getRepository(Share::class)->findBy(['target' => $user->getId()]);

        if ($password->getOwner() !== $user && count(array_filter($sharedPasswords, fn ($sharedPassword) => $sharedPassword->getPassword() === $password)) === 0) {
            return $this->json(['error' => 'Forbidden'], 403);
        }

        $password = $encryptionService->decrypt($password->getPassword(), $password->getCreatedAt()->format('Y-m-d H:i:s'));
        // $password = $encryptionService->encrypt("1234", $encryptionKey);
        // $t = $encryptionService->decrypt($password, $encryptionKey);

        return $this->json(["data" => $password]);
    }
}
