<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractController
{

    protected function getJsonRequest(Request $request)
    {
        return json_decode($request->getContent(), true);
    }
}