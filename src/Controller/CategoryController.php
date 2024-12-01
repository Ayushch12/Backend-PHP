<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'get_categories', methods: ['GET'])]
    public function index(CategoryRepository $repository): Response
    {
        $categories = $repository->findAll();
        return $this->json($categories, 200, [], ['groups' => 'category:read']);
    }

    #[Route('/categories', name: 'create_category', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $category = new Category();
        $category->setName($data['name']);

        $errors = $validator->validate($category);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($category);
        $em->flush();

        return $this->json($category, Response::HTTP_CREATED, [], ['groups' => 'category:read']);
    }

    #[Route('/categories/{id}', name: 'get_category', methods: ['GET'])]
    public function getCategory(int $id, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($category, 200, [], ['groups' => 'category:read']);
    }

    #[Route('/categories/{id}', name: 'update_category', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);
        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $category->setName($data['name']);
        $errors = $validator->validate($category);

        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return $this->json($category, 200, [], ['groups' => 'category:read']);
    }

    #[Route('/categories/{id}', name: 'delete_category', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $category = $em->getRepository(Category::class)->find($id);

        if (!$category) {
            return $this->json(['message' => 'Category not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($category);
        $em->flush();

        return $this->json(['message' => 'Category deleted'], Response::HTTP_NO_CONTENT);
    }
}
