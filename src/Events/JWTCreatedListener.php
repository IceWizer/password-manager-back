<?php

namespace App\EventListener;

// src/App/EventListener/JWTCreatedListener.php

use ApiPlatform\Symfony\Security\Exception\AccessDeniedException;
use Lexik\Bundle\JWTAuthenticationBundle\Event\JWTCreatedEvent;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\User\UserInterface;

class JWTCreatedListener
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function onJWTCreated(JWTCreatedEvent $event)
    {
        $payload = $event->getData();
        $user = $event->getUser();

        if ($user instanceof UserInterface === false || $user->isVerified() === false) {
            throw new AccessDeniedException('You need to activate your account');
        }

        $payload['ip'] = $this->requestStack->getCurrentRequest()->getClientIp();

        $event->setData($payload);
    }
}
