<?php

namespace App\Controller;

use App\Taxes\Calculator;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    /**
     * @Route("/hello/{name?World}", name="hello")
     */
    public function hello($name, Calculator $calculator)
    {
        $p = $calculator->calcul(200);
        return new Response("$p Hello $name");
    }
}
