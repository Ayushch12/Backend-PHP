<?php

// src/Controller/DefaultController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    #[Route('/', name: 'homepage', methods: ['GET'])]
    public function index(): Response
    {
        return $this->json([
            'message' => 'Welcome to the API!',
            'endpoints' => [
                'categories' => '/categories',
                'products' => '/products',
            ],
        ]);
    }
}
