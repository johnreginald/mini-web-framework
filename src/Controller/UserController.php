<?php

namespace App\Controller;

use App\Models\User;
use Infrastructure\Auth;
use Infrastructure\Database;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class UserController extends AbstractController
{

    private Auth $auth;
    private Database $database;

    public function __construct(Auth $auth, Database $database)
    {
        $this->auth = $auth;
        $this->database = $database;
    }

    public function login(Request $request)
    {
        $requestJson = $this->getJsonRequest($request);

        $user = User::where('username', $requestJson['username'])->where('password', sha1($requestJson['password']))->first();

        if (!$user) {
            return new JsonResponse(['error' => 'User not found'], 404);
        }

        $jwt = $this->auth->encode(array_merge($requestJson, ['id' => $user->id]));

        return new JsonResponse(["token" => $jwt], 200);
    }

    public function register(Request $request)
    {
        $requestJson = $this->getJsonRequest($request);

        $user = new User();
        $user->username = $requestJson['username'];
        $user->password = sha1($requestJson['password']);
        $user->save();

        $jwt = $this->auth->encode(array_merge($requestJson, ['id' => $user->id]));

        return new JsonResponse(["token" => $jwt], 201);
    }
}