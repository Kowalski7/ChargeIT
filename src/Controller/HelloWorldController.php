<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloWorldController extends AbstractController
{
    #[Route("/hello", "hello")]
    public function hello_world(): Response {
        return $this->render('HelloWorld.html.twig', [
            'name' => "Alex"
        ]);
    }
}