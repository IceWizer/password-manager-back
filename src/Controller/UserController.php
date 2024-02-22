<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api/' . UserController::ROUTE_PREFIX, name: UserController::ROUTE_PREFIX . '_')]
class UserController extends BaseApiController
{
    public const ROUTE_PREFIX = 'users';

    public function __construct(UserRepository $repository)
    {
        parent::__construct(User::class, $repository);
    }
}
