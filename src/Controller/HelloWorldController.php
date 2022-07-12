<?php

namespace App\Controller;

use App\Entity\Cars;
use App\Entity\User;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HelloWorldController extends AbstractController
{
    #[Route("/hello", "hello")]
    public function hello_world(ManagerRegistry $doctrine): Response {
        return $this->render('HelloWorld.html.twig', [
            // variables here
        ]);
    }
}