<?php

namespace App\Controller;

use App\Entity\Password;
use App\Entity\Share;
use App\Entity\User;
use DateInterval;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\CurrentUser;

class ShareController extends AbstractController
{
    #[Route('/api/shares/create-items', name: 'share_create_items', methods: ['POST'])]
    public function createItems(Request $request, #[CurrentUser] ?User $user, EntityManagerInterface $em): Response
    {
        if (!$user) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $data = json_decode($request->getContent(), true);

        $password = $em->getRepository(Password::class)->find($data['password']);

        if ($password === null || $password->getOwner() !== $user) {
            return $this->json(['message' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $items = $data['emails'];
        $expiration = $data['expiration'] === null ? null : new DateTime();
        if ($expiration !== null) {
            $expiration->add(new DateInterval('PT' . $data['expiration'] . 'S'));
            $expiration = new \DateTimeImmutable($expiration->format('Y-m-d H:i:s'));
        }

        foreach ($items as $item) {
            $user = $em->getRepository(User::class)->findOneBy(["email" => $item]);

            if ($user !== null) {
                $share = new Share();
                $share->setPassword($password);
                $share->setTarget($user);
                $share->setExpireAt($expiration);

                $em->persist($share);
            }
        }

        $em->flush();

        return $this->json(['message' => 'Items created'], Response::HTTP_CREATED);
    }
}
