<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


#[Route('/api/' . BaseApiController::ROUTE_PREFIX, name: BaseApiController::ROUTE_PREFIX . '_')]
abstract class BaseApiController extends AbstractController
{
    protected Object $model;
    protected ServiceEntityRepository $repository;
    public const ROUTE_PREFIX = 'base_api';

    public function __construct(string $modelClass, ServiceEntityRepository $repository)
    {
        $this->model = new $modelClass();
        $this->repository = $repository;
    }

    #[Route('/index', name: 'index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json($this->repository->findAll());
    }
}