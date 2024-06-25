<?php

namespace App\Controller;

use App\Entity\Password;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminPasswordController extends AbstractController
{
    #[Route('/api/admin/passwords', name: 'app_admin_passwords')]
    public function index(Request $request, EntityManagerInterface $em): Response
    {
        // Pagination
        $maxItems = $em->getRepository(Password::class)->count([]);
        $limit = $request->query->get('limit', 10);
        $lastPage = ceil($maxItems / $limit);
        $page = min($request->query->get('page', 1), $lastPage);

        $passwords = $em->getRepository(Password::class)->findBy([], null, $limit, ($page - 1) * $limit);

        return $this->json(["data" => $passwords, "pagination" => ["lastItem" => $maxItems, "page" => $page, "itemsPerPage" => $limit]], 200, [], ['groups' => 'password:read']);
    }
}
