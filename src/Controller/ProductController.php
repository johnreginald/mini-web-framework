<?php

namespace App\Controller;

use App\Models\Product;
use Infrastructure\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends AbstractController
{
    private Database $database;

    public function __construct(Database $database)
    {
        $this->database = $database;
    }

    public function index(Request $request)
    {
        $products = $this->database->getClient()->table('products')->get();
        return new JsonResponse($products, 200);
    }

    public function store(Request $request)
    {
        if ($request->headers->get('Authorization') === null) {
            return new JsonResponse(['error' => 'Authorization header is missing'], Response::HTTP_UNAUTHORIZED);
        }

        $requestJson = $this->getJsonRequest($request);

        $product = new Product();
        $product->name = $requestJson['name'];
        $product->price = $requestJson['price'];
        $product->quantity = $requestJson['quantity'];
        $product->save();

        return new JsonResponse($product, 201);
    }
}