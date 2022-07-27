<?php

namespace App\Controller;

use App\Models\Cart;
use Infrastructure\Auth;
use Infrastructure\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class CartController extends AbstractController
{

    private Auth $auth;
    private Database $database;

    public function __construct(Auth $auth, Database $database)
    {
        $this->auth = $auth;
        $this->database = $database;
    }

    public function addToCart(Request $request): JsonResponse
    {
        if ($request->headers->get('Authorization') === null) {
            return new JsonResponse(['error' => 'Authorization header is missing'], Response::HTTP_UNAUTHORIZED);
        }

        $requestJson = $this->getJsonRequest($request);

        $user = $this->auth->getUser(
            $request->headers->get('Authorization')
        );

        $cart = $user->carts()->firstOrCreate([]);

        $user->carts()->save($cart);

        $cart->items()->create([
            'product_id' => $requestJson["product"],
            'quantity' => $requestJson["quantity"],
        ]);

        return new JsonResponse("Successfully added to Cart", 201);
    }

    public function getCart(Request $request): JsonResponse
    {
        if ($request->headers->get('Authorization') === null) {
            return new JsonResponse(['error' => 'Authorization header is missing'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->auth->getUser(
            $request->headers->get('Authorization')
        );

        // get total from cart items
        $cart = $user->carts()->first();

        if (!$cart) {
            return new JsonResponse(['error' => 'Cart is empty'], Response::HTTP_NOT_FOUND);
        }

        $total = 0;

        foreach ($cart->items as $item) {
            $total += $item->product->price * $item->quantity;
        }

        return new JsonResponse([
            'cart_items' => $cart->items,
            'total' => $total
        ], 200);
    }

    public function checkout(Request $request)
    {
        if ($request->headers->get('Authorization') === null) {
            return new JsonResponse(['error' => 'Authorization header is missing'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->auth->getUser(
            $request->headers->get('Authorization')
        );

        $user->carts()->delete();

        return new JsonResponse("Successfully checked out", 200);
    }

    public function clearCart(Request $request)
    {
        if ($request->headers->get('Authorization') === null) {
            return new JsonResponse(['error' => 'Authorization header is missing'], Response::HTTP_UNAUTHORIZED);
        }

        $user = $this->auth->getUser(
            $request->headers->get('Authorization')
        );

        $user->carts()->delete();

        return new JsonResponse("Cart cleared", 200);
    }
}