<?php

namespace App\EventListener;

// src/App/EventListener/JWTCreatedListener.php

use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $user = $event->getUser();

        if ($user instanceof UserInterface === false || $user->isVerified() === false) {
            throw new AccessDeniedException('You need to activate your account');
        }
    }
}
