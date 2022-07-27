<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;

class HomeController
{
    public function indexAction()
    {
        return new Response("Hello World", 200);
    }
}