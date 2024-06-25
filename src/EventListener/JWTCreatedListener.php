<?php

namespace App\EventListener;

// src/App/EventListener/JWTCreatedListener.php

use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use App\Entity\User;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        if (!($user instanceof User)) {
            throw new AccessDeniedException('You need to be logged in');
        }

        if ($user->isVerified() === false) {
            throw new AccessDeniedException('You need to activate your account');
        }

        $payload = $event->getData();

        $payload['email'] = $user->getEmail();

        $event->setData($payload);
    }
}
