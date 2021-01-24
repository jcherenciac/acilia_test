<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\FeaturedList;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\Exception\ClientExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\RedirectionExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\ServerExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/products", name="products")
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function getAll(ProductRepository $productRepository): JsonResponse
    {
        return new JsonResponse($productRepository->getAll(), Response::HTTP_OK);
    }

    /**
     * @Route("/product", name="add_product", methods={"POST"})
     * @param Request $request
     * @param ProductRepository $productRepository
     * @return JsonResponse
     */
    public function add(Request $request, ProductRepository $productRepository): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        $validation = $this->validateRequestContent($data);
        if (!empty($validation)) {
            $message = 'Not valid fields: ' . implode(",", $validation);
            return new JsonResponse(
                ['status' => $message],
                Response::HTTP_BAD_REQUEST
            );
        }
        try {
            $productRepository->add($data);
        } catch (NotFoundHttpException $notFoundHttpException) {
            return new JsonResponse(
                ['status' => $notFoundHttpException->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        } catch (\InvalidArgumentException $invalidArgumentException) {
            return new JsonResponse(
                ['status' => $invalidArgumentException->getMessage()],
                Response::HTTP_BAD_REQUEST
            );
        }
        return new JsonResponse(
            ['status' => 'Product created.'],
            Response::HTTP_CREATED
        );

    }

    /**
     * @Route("/product/featured", name="featured_products", methods={"GET"})
     * @param Request $request
     * @param FeaturedList $featuredListService
     * @return JsonResponse
     */
    public function featuredList(Request $request, FeaturedList $featuredListService): JsonResponse
    {
        $currency = $request->query->get('currency');
        if (!in_array($currency, [Product::CURRENCY_USD, Product::CURRENCY_EUR])) {
            return new JsonResponse(
                [
                    'status' => 'Bad parameter value, must be ' . Product::CURRENCY_USD . ' or ' . Product::CURRENCY_EUR
                ],
                Response::HTTP_BAD_REQUEST
            );
        }
        try {
            $result = $featuredListService->getFeaturedList($currency);
        } catch (
        TransportExceptionInterface |
        ClientExceptionInterface |
        RedirectionExceptionInterface |
        ServerExceptionInterface
        $exception
        ) {
            return new JsonResponse(
                ['status' => 'Error in external exchange api service.'],
                Response::HTTP_SERVICE_UNAVAILABLE
            );
        }

        return new JsonResponse(
            ['data' => $result],
            Response::HTTP_OK
        );
    }

    private function validateRequestContent(array $data)
    {
        $validateFields = ['name', 'category', 'price', 'currency', 'featured'];
        $notEmptyFields = ['name', 'category', 'price', 'currency'];
        $invalidFields = [];
        foreach ($validateFields as $field) {
            if (!isset($data[$field])) {
                $invalidFields[] = $field;
            }
            foreach ($notEmptyFields as $notEmpty) {
                if (empty($data[$notEmpty])) {
                    $invalidFields[] = $notEmpty;
                }
            }
        }
        return $invalidFields;

    }
}
