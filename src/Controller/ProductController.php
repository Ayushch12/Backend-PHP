<?php

namespace App\Controller;

use App\Entity\Product;
use App\Entity\Category;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ProductController extends AbstractController
{


#[Route('/products', name: 'get_products', methods: ['GET'])]
public function getAllProducts(Request $request, ProductRepository $repository): Response
{
    $search = $request->query->get('search', '');
    $categoryId = $request->query->get('category', null);

    $products = $repository->findBySearchAndCategory($search, $categoryId);

    return $this->json($products, 200, [], ['groups' => 'product:read']);
}





    #[Route('/products/{id}', name: 'get_product', methods: ['GET'])]
    public function getProduct(int $id, ProductRepository $repository): Response
    {
        $product = $repository->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }

    #[Route('/products', name: 'create_product', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $product = new Product();
        $product->setName($data['name']);
        $product->setDescription($data['description']);
        $product->setPrice($data['price']);
        $product->setCreatedAt(new \DateTime());

        $category = $em->getRepository(Category::class)->find($data['category']);
        if (!$category) {
            return $this->json(['message' => 'Category not found'], Response::HTTP_BAD_REQUEST);
        }
        $product->setCategory($category);

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->persist($product);
        $em->flush();

        return $this->json($product, Response::HTTP_CREATED, [], ['groups' => 'product:read']);
    }

    #[Route('/products/{id}', name: 'update_product', methods: ['PUT'])]
    public function update(int $id, Request $request, EntityManagerInterface $em, ValidatorInterface $validator): Response
    {
        $data = json_decode($request->getContent(), true);

        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        if (isset($data['name'])) {
            $product->setName($data['name']);
        }
        if (isset($data['description'])) {
            $product->setDescription($data['description']);
        }
        if (isset($data['price'])) {
            $product->setPrice($data['price']);
        }

        if (isset($data['category'])) {
            $category = $em->getRepository(Category::class)->find($data['category']);
            if (!$category) {
                return $this->json(['message' => 'Category not found'], Response::HTTP_BAD_REQUEST);
            }
            $product->setCategory($category);
        }

        $errors = $validator->validate($product);
        if (count($errors) > 0) {
            return $this->json($errors, Response::HTTP_BAD_REQUEST);
        }

        $em->flush();

        return $this->json($product, 200, [], ['groups' => 'product:read']);
    }

    #[Route('/products/{id}', name: 'delete_product', methods: ['DELETE'])]
    public function delete(int $id, EntityManagerInterface $em): Response
    {
        $product = $em->getRepository(Product::class)->find($id);

        if (!$product) {
            return $this->json(['message' => 'Product not found'], Response::HTTP_NOT_FOUND);
        }

        $em->remove($product);
        $em->flush();

        return $this->json(['message' => 'Product deleted'], Response::HTTP_NO_CONTENT);
    }
}
