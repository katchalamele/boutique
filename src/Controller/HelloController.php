<?php

namespace App\Controller;

use Twig\Environment;
use App\Taxes\Detector;
use App\Taxes\Calculator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController
{
    /**
     * @Route("/hello/{name?World}", name="hello")
     */
    public function hello($name, Calculator $calculator, Detector $detector, Environment $twig)
    {
        dump($detector->detect(101.5));
        dump($detector->detect(10));

        $tva = $calculator->calcul(200);
        dump($tva);

        $html = $twig->render('hello.html.twig', [
            'prenom' => $name,
            'ages' => [
                11,
                14,
                18,
                22,
                25
            ]
        ]);
        return new Response($html);
    }
}
