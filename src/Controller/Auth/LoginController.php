<?php

namespace App\Controller\Auth;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use StrRandom;
use Symfony\Component\Security\Http\Attribute\CurrentUser;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

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

    #[Route("/api/auth/register", name: "api_register", methods: ["POST"])]
    public function register(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $payload = $request->getPayload();

        // Check the unicity of the email
        if ($em->getRepository(User::class)->findOneBy(['email' => $payload->get('email')]) !== null) {
            return $this->json([
                'message' => 'Email already exists',
            ], 400);
        }

        $user = new User();

        $user->setEmail($payload->get('email'));
        // Hash the password
        $user->setPassword($passwordHasher->hashPassword($user, $payload->get('password')));

        $token = StrRandom::generateRandomString(250, 350);

        while ($em->getRepository(User::class)->findOneBy(['token' => $token])) {
            $token = StrRandom::generateRandomString(250, 350);
        }

        $user->setToken($token);

        $em->persist($user);
        $em->flush();

        $email = (new Email())
            ->from('no-reply@password-manager.icewize.fr')
            ->to($payload->get('email'))
            ->subject('Vérification de votre adresse email')
            ->text('Veuillez cliquer sur ce lien pour valider votre adresse email')
            ->html('<a href="http://localhost:5173/verify-email/' . $user->getToken() . '">Cliquez ici pour valider votre adresse email</a>');

        $mailer->send($email);

        return $this->json([
            'message' => 'User registered successfully',
        ]);
    }

    #[Route("/api/auth/verify-email/{token}", name: "api_verify_email", methods: ["POST"])]
    public function verifyEmail(string $token, EntityManagerInterface $em): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            return $this->json([
                'message' => 'Invalid token',
            ], 400);
        }

        $user->setEmailVerifiedAt(new \DateTimeImmutable());
        $user->setToken(null);

        $em->flush();

        return $this->json([
            'message' => 'Email verified successfully',
        ]);
    }

    #[Route("/api/auth/forgot-password", name: "api_forgot_password", methods: ["POST"])]
    public function forgotPassword(Request $request, EntityManagerInterface $em, MailerInterface $mailer): Response
    {
        $payload = $request->getPayload();

        $user = $em->getRepository(User::class)->findOneBy(['email' => $payload->get('email')]);

        if (!$user) {
            return $this->json([
                'message' => 'OK',
            ]);
        }

        $token = StrRandom::generateRandomString(250, 350);

        while ($em->getRepository(User::class)->findOneBy(['token' => $token])) {
            $token = StrRandom::generateRandomString(250, 350);
        }

        $user->setToken($token);
        $user->setEmailVerifiedAt(null);

        $em->flush();

        $email = (new Email())
            ->from('no-reply@password-manager.icewize.fr')
            ->to($payload->get('email'))
            ->subject('Récupération de mot de passe')
            ->text('Veuillez cliquer sur ce lien pour réinitialiser votre mot de passe')
            ->html('<a href="http://localhost:5173/reset-password/' . $user->getToken() . '">Cliquez ici pour réinitialiser votre mot de passe</a>');

        $mailer->send($email);

        return $this->json([
            'message' => 'OK',
        ]);
    }

    #[Route("/api/auth/reset-password/{token}", name: "api_reset_password", methods: ["POST"])]
    public function resetPassword(string $token, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $user = $em->getRepository(User::class)->findOneBy(['token' => $token]);

        if (!$user) {
            return $this->json([
                'message' => 'Invalid token',
            ], 400);
        }

        $payload = $request->getPayload();

        $user->setPassword($passwordHasher->hashPassword($user, $payload->get('password')));
        $user->setToken(null);
        $user->setEmailVerifiedAt(new \DateTimeImmutable());

        $em->flush();

        return $this->json([
            'message' => 'Password reset successfully',
        ]);
    }
}
