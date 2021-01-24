<?php

namespace App\Controller;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;


class CategoryController extends AbstractController
{

    /**
     * @Route("/categories", name="get_category", methods={"GET"})
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     */
    public function getAll(CategoryRepository $categoryRepository): JsonResponse
    {
        return new JsonResponse($categoryRepository->getAll(), Response::HTTP_OK);
    }

    /**
     * @Route("/category", name="add_category", methods={"POST"})
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     */
    public function add(Request $request, CategoryRepository $categoryRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        if (empty($data['name']) || empty($data['description'])) {
            throw new NotFoundHttpException('Expecting mandatory parameters');
        }
        $categoryRepository->add($data);
        return new JsonResponse(
            ['status' => 'Category created.'],
            Response::HTTP_CREATED
        );

    }

    /**
     * @Route("/category/{id}", name="update_category", methods={"PUT"})
     * @param $id
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     */
    public function put(int $id, Request $request, CategoryRepository $categoryRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $category = $categoryRepository->find($id);
        if (!$category) {
            return new JsonResponse(
                ['status' => 'Category not found.'],
                Response::HTTP_NOT_FOUND
            );
        }
        $categoryRepository->update($category, $data);
        return new JsonResponse(
            ['status' => 'Category updated.'],
            Response::HTTP_OK
        );

    }

    /**
     * @Route("/category/{id}", name="delete_category", methods={"DELETE"})
     * @param int $id
     * @param CategoryRepository $categoryRepository
     * @return JsonResponse
     */
    public function delete(int $id, CategoryRepository $categoryRepository): JsonResponse
    {
        $category = $categoryRepository->find($id);
        if (!$category) {
            return new JsonResponse(
                ['status' => 'Category not found.'],
                Response::HTTP_NOT_FOUND
            );
        }

        $categoryRepository->remove($category);
        return new JsonResponse(
            ['status' => 'Category removed.'],
            Response::HTTP_OK
        );
    }
}
